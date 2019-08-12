<?php

function verifyTable($title, $isOutside, $numberOfSeats, $id = 0) {
    // falls id übergeben, stelle sicher dass ID ein Tisch ist

    // stelle sicher, dass title noch nicht existiert

    // stelle sicher, dass isOutside ein boolescher Wert ist

    // stelle sicher, dass numberOfSeats eine Zahl ist

    return null;
}

function verifyReservation($tables, $from, $to, $firstname, $lastname, $mail, $phonenumber, $id = 0) {
    $errorMsg = null;

    // stelle sicher, dass keine Duplikate in tables enthalten sind
    $duplicate = false;
    for($c1 = 0; $c1 < count($tables); $c1++) {
        for($c2 = 0; $c2 < count($tables); $c2++) {
            if($c1 != $c2 && $tables[$c1] == $tables[$c2]) {
                $duplicate = true;
                break 2;
            }
        }
    }

    // iteriere über tables und stelle sicher, dass jede ID im Array tatsächlich einem Tisch zugeordnet ist
    $allAreTables = true;
    foreach($tables as $t) {
        if(get_post_type($t) != "tables") {
            $allAreTables = false;
        }
    }

    // iteriere über tables und stelle sicher, dass jeder Tisch frei ist
    $allReservations = getReservations();
    $allTablesFree = true;
    foreach($tables as $t) {
        if(! isTableFree($t, $from, $to, $allReservations, $id)) {
            $allTablesFree = false;
        }
    }


    // stelle sicher, dass falls eine ID übergeben wurde, diese einer Reservierung zugeordnet ist
    if($id != 0 && get_post_type($id) != "reservations") {
        $errorMsg = "Die ID der Reservierung, die bearbeitet werden soll, ist keiner Reservierung zugeordnet.";
    }

    // stelle sicher, dass gülitge Datumsangaben eingegeben wurden
    else if($from < time() + (30 * 60) || $from > time() + ((365 / 2) * 24 * 60 * 60)) {
        $errorMsg = "Das Beginndatum darf nicht weniger als 30 Minuten und nicht mehr als ein halbes Jahr in der Zukunft liegen.";
    } else if ($from < time() + (30 * 60) || $from > time() + ((365 / 2) * 24 * 60 * 60)) {
        $errorMsg = "Das Enddatum darf nicht weniger als 30 Minuten und nicht mehr als ein halbes Jahr in der Zukunft liegen.";
    }

    // stelle sicher, dass die Reservierung beginnt, bevor sie aufhört
    else if ($from > $to) {
       $errorMsg = "Das Beginndatum darf nicht vor dem Enddatum liegen.";
    }

    // stelle sicher, dass die Reservierung nicht länger als eine Woche dauert
    else if (($to - $from) > (7 * 24 * 60 * 60)) {
        $errorMsg = "Die Reservierdauer darf nicht größer als eine Woche sein.";
    }

    // stelle sicher, dass mindestens ein Tisch reserviert ist
    else if (count($tables) == 0) {
        $errorMsg = "Mindestens ein Tisch muss reserviert werden!";
    }

    // stelle sicher, dass keine Duplikate in der Liste sind
    else if($duplicate) {
        $errorMsg = "Ein Tisch darf nicht doppelt reserviert werden.";
    }

    // stelle sicher, dass die übergebene ID des Tisches tatsächlich ein Tisch ist
    else if (! $allAreTables) {
        $errorMsg = "Die ID von mindestens einem Tisch ist keinem Tisch zugeordnet.";
    }

    // stelle sicher, dass Tisch frei ist
    else if (! $allTablesFree) {
        $errorMsg = "Mindestens ein Tisch ist zum gewünschten Zeitpunkt nicht frei.";
    }

    // stelle sicher, dass eine gültige E-Mail-Adresse eingegeben wurde
    else if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Die E-Mail-Adresse ist nicht gültig.";
    }

    // stelle sicher, dass eine gültige Telefonnummer eingegeben wurde

    return $errorMsg;
}
?>