<?php

require_once("queryDatabase.php");

function exportCSV()
{
    $allReservations = getReservations();

    $timestamp = time();

    $time = $timestamp = date("d.m.Y H.i");

    foreach ($allReservations as $reservationKey => $reservation) {
        $allReservations[$reservationKey]["from"] = date("d.m.Y H:i", $reservation["from"]);
        $allReservations[$reservationKey]["to"] = date("d.m.Y H:i", $reservation["to"]);
    }

    foreach ($allReservations as $reservationKey => $reservation) {
        $name = "";

        foreach ($reservation["tableIds"] as $table) {
            $name .= $table.";";
        }

        $allReservations[$reservationKey]["tableIds"] = $name;
    }
    
    $daten = ['Reservierungsnummer', 'von', 'bis', 'Anzahl_Plätze', 'Tische', 'Vorname', 'Nachname', 'E-Mail', 'Telefonnummer'];
    
    $fp = fopen(plugin_dir_path(__FILE__)."\csv\daten.csv", 'w');

    fputcsv($fp, $daten);
    
    foreach ($allReservations as $arrays) {
        fputcsv($fp, $arrays);
    }
    
    fclose($fp);


    // add_action('send_headers', 'headerHook');
    // $file = plugin_dir_path( __FILE__ )."\csv\daten.csv";

    // readfile($file);
}



function headerHook()
{
    echo "Dies ist ein test.";
    throw new Exception("TEst");

    $file = plugin_dir_path(__FILE__)."\csv\daten.csv";

    header("Content-Type: application/csv");
    header("Content-Disposition: attachment; reservationen ".$time.".csv");
    header('Content-Transfer-Encoding: binary');
    header('Connection: Keep-Alive');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header("Content-Length: ". filesize($file));
}
