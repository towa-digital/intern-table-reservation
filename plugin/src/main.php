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

require_once("queryDatabase.php");

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

add_action("wp_ajax_my_action", "loadAvailableTables");

function loadAvailableTables() {
	global $wpdb; // this is how you get access to the database

	$startTime = strtotime($_POST['from']);
    $endTime = strtotime($_POST['to']);
    $reservationId = $_POST["reservationId"];

    echo json_encode(getFreeTables($startTime, $endTime, $reservationId));
	wp_die(); // this is required to terminate immediately and return a proper response
}

add_action("rest_api_init", function() {
    register_rest_route("tischverwaltung/v1", "freetables/(?P<from>\d+)/(?P<to>\d+)", array(
        "methods" => "GET",
        "callback" => "rest_getFreeTables"
    ));
});
function rest_getFreeTables($request) {
    /*$returnArr = getFreeTables(
        strtotime($request["from"]),
        strtotime($request["to"]),
        0
    );*/
    $returnArr = array($request["from"], $request["to"]);
    $response = new WP_REST_Response($returnArr);
    $response->set_status(200);

    return $response;
}

?>