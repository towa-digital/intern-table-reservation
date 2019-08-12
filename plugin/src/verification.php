<?php

function verifyTable($title, $isOutside, $numberOfSeats, $id = 0) {
    // falls id übergeben, stelle sicher dass ID ein Tisch ist

    // stelle sicher, dass title noch nicht existiert

    // stelle sicher, dass isOutside ein boolescher Wert ist

    // stelle sicher, dass numberOfSeats eine Zahl ist

    return null;
}

function verifyReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber, $id = 0) {
    if($from === "") {
        return "Das Beginndatum muss angegeben sein!";
    }
    if($to === "") {
        return "Das Enddatum muss angegeben sein!";
    }

    // entferne leere Werte aus dem Array
    foreach($tables as $key => $value) {
        if($value == "") {
            array_splice($tables, $key, 1);
        }
    }

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
        return "Die ID der Reservierung, die bearbeitet werden soll, ist keiner Reservierung zugeordnet.";
    }

    // stelle sicher, dass gülitge Datumsangaben eingegeben wurden
    if($from < time() + (30 * 60) || $from > time() + ((365 / 2) * 24 * 60 * 60)) {
        return "Das Beginndatum darf nicht weniger als 30 Minuten und nicht mehr als ein halbes Jahr in der Zukunft liegen.";
    } else if ($from < time() + (30 * 60) || $from > time() + ((365 / 2) * 24 * 60 * 60)) {
        return "Das Enddatum darf nicht weniger als 30 Minuten und nicht mehr als ein halbes Jahr in der Zukunft liegen.";
    }

    // stelle sicher, dass die Reservierung beginnt, bevor sie aufhört
   if ($from > $to) {
       return "Das Beginndatum darf nicht vor dem Enddatum liegen.";
    }

    // stelle sicher, dass die Reservierung nicht länger als eine Woche dauert
    if (($to - $from) > (7 * 24 * 60 * 60)) {
        return "Die Reservierdauer darf nicht größer als eine Woche sein.";
    }

    // stelle sicher, dass mindestens ein Tisch reserviert ist
    if (count($tables) == 0) {
        return "Mindestens ein Tisch muss reserviert werden!";
    }

    // stelle sicher, dass keine Duplikate in der Liste sind
    if($duplicate) {
        return "Ein Tisch darf nicht doppelt reserviert werden.";
    }

    // stelle sicher, dass die übergebene ID des Tisches tatsächlich ein Tisch ist
    if (! $allAreTables) {
        return "Die ID von mindestens einem Tisch ist keinem Tisch zugeordnet.";
    }

    // stelle sicher, dass Tisch frei ist
    if (! $allTablesFree) {
        return "Mindestens ein Tisch ist zum gewünschten Zeitpunkt nicht frei.";
    }

    // stelle sicher, dass eine gültige E-Mail-Adresse eingegeben wurde
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Die E-Mail-Adresse ist nicht gültig.";
    }

    // stelle sicher, dass eine gültige Telefonnummer eingegeben wurde

    return null;
}
?>