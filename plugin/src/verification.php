<?php

function verifyTable($title, $isOutside, $numberOfSeats, $id = 0) {
    // falls id übergeben, stelle sicher dass ID ein Tisch ist

    // stelle sicher, dass title noch nicht existiert

    // stelle sicher, dass isOutside ein boolescher Wert ist

    // stelle sicher, dass numberOfSeats eine Zahl ist

    return null;
}

function verifyReservation($table, $from, $to, $firstname, $lastname, $mail, $phonenumber, $id = 0) {
    $errorMsg = null;
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

    // stelle sicher, dass die übergebene ID des Tisches tatsächlich ein Tisch ist
    else if (get_post_type($table) != "tables") {
        $errorMsg = "Die ID des übergebenenen Tisch ist keinem Tisch zugeordnet.";
    }

    // stelle sicher, dass Tisch frei ist
    else if (! isTableFree($table, $from, $to, getReservations(), $id)) {
        $errorMsg = "Der gewünschte Tisch ist zum angegebenen Zeitpunkt leider nicht frei.";
    }

    // stelle sicher, dass eine gültige E-Mail-Adresse eingegeben wurde
    else if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Die E-Mail-Adresse ist nicht gültig.";
    }

    // stelle sicher, dass eine gültige Telefonnummer eingegeben wurde

    return $errorMsg;
}
?>