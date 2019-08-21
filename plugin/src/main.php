<?php
/**
 * Plugin Name: Tischverwaltung
 * Plugin URI: 
 * Description: 
 * Version: 1.0
 * Author: 
 * Author URI: 
 */

define('WP_DEBUG', true);

// set default timezone
date_default_timezone_set(get_option('timezone_string'));

// Setup von CPT und ACF
require_once("initCpt.php");
require_once("initAcf.php");

require("adminPanel/addReservation.php");
require("adminPanel/addTable.php");
require("adminPanel/reservationList.php");
require("adminPanel/tableList.php");
require("adminPanel/optionsPage.php");
require("adminPanel/exportCSV.php");


require_once("queryDatabase.php");
require_once("options.php");
require_once("email.php");

add_action("admin_menu", "setup_admin_menu");
function setup_admin_menu() {
    $reservationList = add_menu_page("Reservierungen verwalten", "Reservierungen verwalten", "manage_options", "managereservations", "show_reservationList");
    add_action("admin_print_styles-".$reservationList , "applyStyle_reservationList");

    $addReservation = add_submenu_page("managereservations", "Neue Reservierung erstellen", "Neue Reservierung erstellen", "manage_options", "addreservation", "show_addReservation");
    add_action("admin_print_styles-".$addReservation , "applyStyle_addReservation");

    $exportCSV = add_submenu_page("managereservations", "Exportieren als CSV", "Exportieren als CSV", "manage_options", "exportcsv", "show_exportCSV");
    add_action("admin_print_styles-".$exportCSV , "applyStyle_exportCSV");

    $tableList = add_menu_page("Tische verwalten", "Tische verwalten", "manage_options", "managetables", "show_tableList");
    add_action("admin_print_styles-".$tableList , "applyStyle_tableList");

    $addTable = add_submenu_page("managetables", "Neuen Tisch erstellen", "Neuen Tisch erstellen", "manage_options", "addtable", "show_addTable");
    add_action("admin_print_styles-".$addTable , "applyStyle_addTable");
}

add_action("admin_menu", "setup_options_page");
function setup_options_page() {
    $optionsPage = add_options_page("Tischverwaltung Konfiguration", "Tischverwaltung Konfiguration", "administrator", "config", "show_optionsPage");
    add_action("admin_print_styles-".$optionsPage, "applyStyle_optionsPage");

    add_action('admin_init', 'initSettings');
}


add_action("wp_ajax_my_action", "loadAvailableTables");

function loadAvailableTables() {
    // set default timezone
    date_default_timezone_set(get_option('timezone_string'));

    global $wpdb; 

    
    $startTime = $_POST['from'];
    $endTime = ($_POST["useDefaultEndTime"]) ? $startTime + (getDefaultReservationDuration() * 60) : localStringToUTCTimestamp($_POST['to']);
    $reservationId = $_POST["reservationId"];

    echo json_encode(getFreeTables($startTime, $endTime, $reservationId));
	wp_die(); // this is required to terminate immediately and return a proper response
}

add_action("rest_api_init", function() {
    register_rest_route("tischverwaltung/v1", "freetables/(?P<from>\d+)/(?P<numberOfSeats>\d+)", array(
        "methods" => "GET",
        "callback" => "rest_getFreeTables"
    ));
    register_rest_route("tischverwaltung/v1", "savenewreservation/", array(
        "methods" => "POST",
        "callback" => "rest_saveNewReservation"
    ));
    register_rest_route("tischverwaltung/v1", "gettimeslots/", array(
        "methods" => "GET",
        "callback" => "rest_getTimeSlots"
    ));
});



function rest_getFreeTables($request) {
    // set default timezone
    date_default_timezone_set(get_option('timezone_string'));

    $from = $request["from"];
    $to = $from + (getDefaultReservationDuration() * 60);
    $persons = $request["numberOfSeats"];

    if($from > $to) {
        return new WP_Error('invalid_data', "Das Beginndatum darf nicht nach dem Enddatum liegen.");
    }
    if($from <= time()) {
        return new WP_Error("invalid_data", "Das Beginndatum der Reservierung darf nicht in der Vergangenheit liegen.");
    }
    if($from <= time() + getCanReservateInMinutes() * 60) {
        return new WP_Error("invalid_data", "Das Beginndatum der Reservierung muss mindestens ".getCanReservateInMinutes(). " Minuten in der Zukunft liegen");
    }
    if(! isOpen($from)) {
        return new WP_Error("invalid_data", "Es kann keine Reservierung außerhalb der Öffnungszeiten getätigt werden.");
    }
    if($from > time() + ((365 / 2) * 24 * 60 * 60)) {
        return new WP_Error("invalid_data", "Das Beginndatum darf nicht länger als ein halbes Jahr in der Zukunft liegen.");
    }
    if($persons <= 0) {
        return new WP_Error("invalid_data", "Die Anzahl der Personen muss größer gleich 1 sein.");
    }
    if($persons > getMaxAmountOfPersons()) {
        return new WP_Error("tooMuchPersons", getTooManyPersonsError());
    }

    $returnArr = getSuitableTables(
        $from,
        $to,
        $persons,
        0
    );

    if(count($returnArr) == 0) {
        return new WP_Error("noSuitableTables", getNoFreeTablesError());
    }

    $response = new WP_REST_Response($returnArr);
    $response->set_status(200);

    return $response;
}

function rest_getTimeSlots($request) {
    // set default timezone
    date_default_timezone_set(get_option('timezone_string'));

    $slotsToReturn = array();
    $holidaysToReturn = array();

    // compute slots to return
    $openingHours = getOpeningHours();
    foreach($openingHours as $dayKey => $day) {
        $slotsToReturn[$dayKey] = array();

        foreach($day as $timeSlot) {
            //from und to in UTC
            $from = $timeSlot["from"];
            $to = $timeSlot["to"];
    
    
            // auf 15 Minuten runden
            $minutes = 15 - (floor($from / 60) % 15);
            if($minutes == 15) $minutes = 0;
            $from += $minutes * 60;
    
            while($from <= $to) {
                $slotsToReturn[$dayKey][] = array(
                    "display" => secondsToValueString($from),
                    "timestamp" => $from,
                );
    
                $from += 15 * 60;
            }
        }
    }

    // compute holidays
    $holidays = getHolidays();
    foreach($holidays as $h) {
        $from = $h["from"];
        $to = $h["to"];

        while($from <= $to) {
            $holidaysToReturn[] = $from;

            $from += 24 * 60 * 60;
        }
    }
    

    return array(
        "openingHours" => $slotsToReturn,
        "holidays" => $holidaysToReturn
    );
}


function rest_saveNewReservation($request) {
    // set default timezone
    date_default_timezone_set(get_option('timezone_string'));

    $from = $request["from"];
    $to = $from + (getDefaultReservationDuration() * 60);
    $tables = json_decode($request["tables"], true);
    $firstname = $request["firstname"];
    $lastname = $request["lastname"];
    $mail = $request["mail"];
    $phonenumber = $request["phonenumber"];
    $numberOfSeats = $request["numberOfSeats"];
   
    $errorMsg = verifyReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber, 0, true);
    if($errorMsg !== null) {
        return new WP_Error("verification_error", $errorMsg);
    }


    addReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber);
    
    register_shutdown_function(function($from, $tables, $numberOfSeats, $firstname, $lastname, $mail) {
        emailToUser($from, $tables, $numberOfSeats, $firstname, $lastname, $mail);
        emailToAdmin($from, $tables, $numberOfSeats, $firstname, $lastname);
    }, $from, $tables, $numberOfSeats, $firstname, $lastname, $mail);

}

?>