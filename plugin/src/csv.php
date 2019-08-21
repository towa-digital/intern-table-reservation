<?php

require_once("queryDatabase.php");

function exportCSV() {
    
    $allReservations = getReservations();

    $timestamp = time();

    $time = $timestamp = date("d.m.Y H.i");

    foreach($allReservations as $reservationKey => $reservation) {
        $allReservations[$reservationKey]["from"] = date("d.m.Y H:i", $reservation["from"]);
        $allReservations[$reservationKey]["to"] = date("d.m.Y H:i", $reservation["to"]);
    }

    foreach($allReservations as $reservationKey => $reservation) {
        $name = "";

        foreach($reservation["tableIds"] as $table){
            $name .= $table.";";
        }

        $allReservations[$reservationKey]["tableIds"] = $name;

       

    }
    
    $daten = array('Reservierungsnummer', 'von', 'bis', 'Anzahl_Plätze', 'Tische', 'Vorname', 'Nachname', 'E-Mail', 'Telefonnummer');
    
    $fp = fopen($_SERVER["DOCUMENT_ROOT"].'\plugin\plugin\src\csv\daten.csv', 'w');

    fputcsv($fp, $daten);
    
    foreach($allReservations as $arrays){
            fputcsv($fp, $arrays);    
    }
    
    fclose($fp);

    add_action('send_header', 'headerHook');

    
}

function headerHook() {
    echo "in";

    $file = $_SERVER["DOCUMENT_ROOT"].'\plugin\plugin\src\csv\daten.csv';

    header("Content-Type: application/csv");
    header("Content-Disposition: attachment; reservationen ".$time.".csv");
    header('Content-Transfer-Encoding: binary');
    header('Connection: Keep-Alive');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header("Content-Length: ". filesize($file));

    readfile($file);
}





?>