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

// Setup von CPT und ACF
require_once("initCpt.php");
require_once("initAcf.php");

require("adminPanel/addReservation.php");
require("adminPanel/addTable.php");
require("adminPanel/reservationList.php");
require("adminPanel/tableList.php");
require("adminPanel/optionsPage.php");

require_once("queryDatabase.php");
require_once("options.php");

add_action("admin_menu", "setup_admin_menu");
function setup_admin_menu() {
    $reservationList = add_menu_page("Reservierungen verwalten", "Reservierungen verwalten", "manage_options", "managereservations", "show_reservationList");
    add_action("admin_print_styles-".$reservationList , "applyStyle_reservationList");

    $addReservation = add_submenu_page("managereservations", "Neue Reservierung erstellen", "Neue Reservierung erstellen", "manage_options", "addreservation", "show_addReservation");
    add_action("admin_print_styles-".$addReservation , "applyStyle_addReservation");

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
    global $wpdb; 

    
    $startTime = strtotime($_POST['from']);
    $endTime = ($_POST["useDefaultEndTime"]) ? $startTime + (getDefaultReservationDuration() * 60) : strtotime($_POST['to']);
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
});



function rest_getFreeTables($request) {
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




function rest_saveNewReservation($request) {
    return new WP_Error(get_option("defaultReservationDuration", "Fehler"), getDefaultReservationDuration());

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
}

?>