<?php

require_once("queryDatabase.php");

function getCsvAsString()
{
    $allReservations = getReservations();
    
    $toReturn = "Reservierungsnummer, von, bis, Anzahl_Plätze, Tische, Vorname, Nachname, E-Mail, Telefonnummer\n";
    foreach($allReservations as $r) {
        $toReturn .= $r["id"].",";
        $toReturn .= date("d.m.Y H:i", $r["from"]).",";
        $toReturn .= date("d.m.Y H:i", $r["to"]).",";
        $toReturn .= $r["numberOfSeats"].",";

        foreach($r["tableIds"] as $tableId) {
            $toReturn .= $tableId.";";
        }
        $toReturn .= ",";

        $toReturn .= $r["firstname"].",";
        $toReturn .= $r["lastname"].",";
        $toReturn .= $r["mail"].",";
        $toReturn .= $r["phonenumber"];
        $toReturn .= "\n";
    }

    return $toReturn;
}

