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
require("adminPanel/reservationList.php");
require("adminPanel/tableList.php");
require("adminPanel/tableGui.php");
require("adminPanel/optionsPage.php");
require("adminPanel/exportCSV.php");

require_once("permissions.php");
require_once("queryDatabase.php");
require_once("options.php");
require_once("email.php");

add_action("admin_menu", "setup_admin_menu");
function setup_admin_menu()
{
    $reservationList = add_menu_page("Reservierungen verwalten", "Reservierungen verwalten", "tv_viewReservations", "managereservations", "show_reservationList");
    add_action("admin_print_styles-".$reservationList, "applyStyle_reservationList");

    $addReservation = add_submenu_page("managereservations", "Neue Reservierung erstellen", "Neue Reservierung erstellen", "tv_addReservations", "addreservation", "show_addReservation");
    add_action("admin_print_styles-".$addReservation, "applyStyle_addReservation");

    $exportCSV = add_submenu_page("managereservations", "Exportieren als CSV", "Exportieren als CSV", "tv_exportReservations", "exportcsv", "show_export");
    add_action("admin_print_styles-".$exportCSV, "applyStyle_export");

    $tableList = add_menu_page("Tische verwalten", "Tische verwalten", "tv_viewTables", "managetables", "show_tableList");
    add_action("admin_print_styles-".$tableList, "applyStyle_tableList");

    $tableGui = add_menu_page("Tische grafisch verwalten", "Tische grafisch verwalten", "tv_viewTables", "managetablesgui", "show_tableGui");
    add_action("admin_print_styles-".$tableGui, "applyStyle_tableGui");

    $optionsPage = add_menu_page("Tischverwaltung Konfiguration", "Tischverwaltung Konfiguration", "tv_editOptions", "config", "show_optionsPage");
    add_action("admin_print_styles-".$optionsPage, "applyStyle_optionsPage");
}


add_action("wp_ajax_my_action", "loadAvailableTables");

function loadAvailableTables()
{
    // set default timezone
    date_default_timezone_set(get_option('timezone_string'));

    global $wpdb;

    $startTime = strtotime($_POST['from']);
    $endTime = ($_POST["useDefaultEndTime"]) ? $startTime + (getDefaultReservationDuration() * 60) : strtotime($_POST['to']);
    $reservationId = $_POST["reservationId"];


    echo json_encode(getFreeTables($startTime, $endTime, $reservationId));
    wp_die(); // this is required to terminate immediately and return a proper response
}

add_action("rest_api_init", function () {
    register_rest_route("tischverwaltung/v1", "freetables/(?P<from>\d+)/(?P<numberOfSeats>\d+)(?:/(?P<isOutside>\d+))?", [
        "methods" => "GET",
        "callback" => "rest_getFreeTables",
    ]);
    register_rest_route("tischverwaltung/v1", "savenewreservation/", [
        "methods" => "POST",
        "callback" => "rest_saveNewReservation",
    ]);
    register_rest_route("tischverwaltung/v1", "gettimeslots/", [
        "methods" => "GET",
        "callback" => "rest_getTimeSlots",
    ]);
    register_rest_route("tischverwaltung/v1", "getobjects/(?P<isOutside>\d+)", [
        "methods" => "GET",
        "callback" => "rest_getObjects"
    ]);
    register_rest_route("tischverwaltung/v1", "getoptions", [
        "methods" => "GET",
        "callback" => "rest_getOptions"
    ]);
});

function rest_getOptions($request) {
    $response = new WP_REST_Response(array(
        "maxAmountOfPersons" => getMaxAmountOfPersons(),
        "maxUnusedSeatsPerReservation" => getMaxUnusedSeatsPerReservation()
    ));
    $response->set_status(200);

    return $response;

}

function rest_getObjects($request) {
    $isOutside = $request["isOutside"];
    $payload = [
        "roomOutlines" => getObjectsByType("roomOutlines", $isOutside),
        "seperators" => getObjectsByType("seperators", $isOutside),
        "windows" => getObjectsByType("windows", $isOutside),
        "doors" => getObjectsByType("doors", $isOutside),
        "toilets" => getObjectsByType("toilets", $isOutside),
        "bars" => getObjectsByType("bars", $isOutside),
    ];

    $response = new WP_REST_Response($payload);
    $response->set_status(200);

    return $response;
}



function rest_getFreeTables($request)
{
    // set default timezone
    date_default_timezone_set(get_option('timezone_string'));

    $from = $request["from"];
    $to = $from + (getDefaultReservationDuration() * 60);
    $persons = $request["numberOfSeats"];
    $outside = ($request["isOutside"] === null) ? -1 : $request["isOutside"];

    if ($from > $to) {
        return new WP_Error('invalid_data', "Das Beginndatum darf nicht nach dem Enddatum liegen.");
    }
    if ($from <= time()) {
        return new WP_Error("invalid_data", "Das Beginndatum der Reservierung darf nicht in der Vergangenheit liegen.");
    }
    if ($from <= time() + getCanReservateInMinutes() * 60) {
        return new WP_Error("invalid_data", "Das Beginndatum der Reservierung muss mindestens ".getCanReservateInMinutes(). " Minuten in der Zukunft liegen");
    }
    if (! isOpen($from)) {
        return new WP_Error("invalid_data", "Es kann keine Reservierung außerhalb der Öffnungszeiten getätigt werden.");
    }
    if ($from > time() + ((365 / 2) * 24 * 60 * 60)) {
        return new WP_Error("invalid_data", "Das Beginndatum darf nicht länger als ein halbes Jahr in der Zukunft liegen.");
    }
    if ($persons <= 0) {
        return new WP_Error("invalid_data", "Die Anzahl der Personen muss größer gleich 1 sein.");
    }
    if ($persons > getMaxAmountOfPersons()) {
        return new WP_Error("tooMuchPersons", getTooManyPersonsError());
    }

    $returnArr = getSuitableTablesWithFlag(
        $from,
        $to,
        $persons,
        $outside,
        0
    );
    
    $atLeastOneFree = false;
    foreach($returnArr as $table) {
        if($table["isFree"]) $atLeastOneFree = true;
    }

    if (! $atLeastOneFree) {
        return new WP_Error("noSuitableTables", getNoFreeTablesError());
    }

    $response = new WP_REST_Response($returnArr);
    $response->set_status(200);

    return $response;
}

function rest_getTimeSlots($request)
{
    // set default timezone
    date_default_timezone_set(get_option('timezone_string'));

    $slotsToReturn = [];
    $holidaysToReturn = [];

    // compute slots to return
    $openingHours = getOpeningHours();
    foreach ($openingHours as $dayKey => $day) {
        $slotsToReturn[$dayKey] = [];

        foreach ($day as $timeSlot) {
            //from und to in UTC
            $from = $timeSlot["from"];
            $to = $timeSlot["to"];
    
    
            // auf 15 Minuten runden
            $minutes = 15 - (floor($from / 60) % 15);
            if ($minutes == 15) {
                $minutes = 0;
            }
            $from += $minutes * 60;
    
            while ($from <= $to) {
                $slotsToReturn[$dayKey][] = [
                    "display" => secondsToValueString($from),
                    "timestamp" => $from,
                ];
    
                $from += 15 * 60;
            }
        }
    }

    // compute holidays
    $holidays = getHolidays();
    foreach ($holidays as $h) {
        $from = $h["from"];
        $to = $h["to"];

        while ($from <= $to) {
            $holidaysToReturn[] = $from;

            $from += 24 * 60 * 60;
        }
    }
    

    return [
        "openingHours" => $slotsToReturn,
        "holidays" => $holidaysToReturn,
    ];
}


function rest_saveNewReservation($request)
{
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
    $remarks = ($request["remarks"] === null) ? "" : $request["remarks"];
   
    $errorMsg = verifyReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber, $remarks, 0, true);
    if ($errorMsg !== null) {
        return new WP_Error("verification_error", $errorMsg);
    }


    addReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber, $remarks);
    
    register_shutdown_function(function ($from, $tables, $numberOfSeats, $firstname, $lastname, $mail) {
        emailToUser($from, $tables, $numberOfSeats, $firstname, $lastname, $mail);
        emailToAdmin($from, $tables, $numberOfSeats, $firstname, $lastname);
    }, $from, $tables, $numberOfSeats, $firstname, $lastname, $mail);
}
