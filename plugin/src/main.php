<?php
/**
 * Plugin Name: Tischverwaltung
 * Plugin URI: 
 * Description: 
 * Version: 1.0
 * Author: 
 * Author URI: 
 */

// Setup von CPT und ACF
require_once("initCpt.php");
require_once("initAcf.php");

require("adminPanel/addReservation.php");
require("adminPanel/addTable.php");
require("adminPanel/reservationList.php");
require("adminPanel/tableList.php");


add_action("admin_menu", "setup_admin_menu");
function setup_admin_menu() {
    $reservationList = add_menu_page("Reservierungen verwalten", "Reservierungen verwalten", "manage_options", "managereservations", "show_reservationList");
    add_action("admin_print_styles-".$reservationList , "applyStyle_reservationList");

    $addReservation = add_submenu_page("managereservations", "Neue Reservierung erstellen", "Neue Reservierung erstellen", "manage_options", "", "show_addReservation");
    add_action("admin_print_styles-".$addReservation , "applyStyle_addReservation");

    $tableList = add_menu_page("Tische verwalten", "Tische verwalten", "manage_options", "managetables", "show_tableList");
    add_action("admin_print_styles-".$tableList , "applyStyle_tableList");

    $addTable = add_submenu_page("managetables", "Neuen Tisch erstellen", "Neuen Tisch erstellen", "manage_options", "a", "show_addTable");
    add_action("admin_print_styles-".$addTable , "applyStyle_addTable");
}

?>