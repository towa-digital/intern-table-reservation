<?php

function storeOptions($defaultReservationDuration, $maxAmountOfPersons,
        $maxUnusedSeatsPerReservation, $canReservateInMinutes, $tooManyPersonsError,
        $noFreeTablesError, $userConfirmationMail, $adminConfirmationMail,
        $adminAddress, $openingHours, $holidays) {

    // $tooManyPersonsError = nl2br($tooManyPersonsError);
    // $userConfirmationMail = nl2br($userConfirmationMail);
    // $adminConfirmationMail = nl2br($adminConfirmationMail);

    // Validierung der ganzzahligen Werte
    if(intval($defaultReservationDuration) != $defaultReservationDuration && $defaultReservationDuration > 0) {
        return "Dauer einer Reservierung muss eine Ganzzahl größer als 0 sein.";
    }
    if(intval($maxAmountOfPersons) != $maxAmountOfPersons && $maxAmountOfPersons > 0) {
        return "Die maximale Anzahl Plätze pro Reservierung muss eine Ganzzahl größer als 0 sein.";
    }
    if(intval($maxUnusedSeatsPerReservation) != $maxUnusedSeatsPerReservation && $maxUnusedSeatsPerReservation > 0) {
        return "Die maximale Anzahl ungenutzter Plätze pro Reservierung muss eine Ganzzahl größer als 0 sein.";
    }
    if(intval($canReservateInMinutes) != $canReservateInMinutes && $canReservateInMinutes > 0) {
        return "Mindestdauer zwischen Reservierung und Reservierungsbeginn muss eine Ganzzahl größer als 0 sein.";
    }
    
    // Validierung der Öffnungszeiten
    for($dayKey = 0; $dayKey < 7; $dayKey++) {
        $day = $openingHours[$dayKey];
        
        if($day == null) {
            $openingHours[$dayKey] = array();
            continue;
        }

        foreach($day as $elemKey => $elem) {
            $from = $elem["from"];
            $to = $elem["to"];

            $fromSplit = explode(":", $from);
            $toSplit = explode(":", $to);

            if(intval($fromSplit[0]) != $fromSplit[0] || intval($fromSplit[1]) != $fromSplit[1] || 
                intval($toSplit[0]) != $toSplit[0] || intval($toSplit[1]) != $toSplit[1] ||
                count($fromSplit) != 2 || count($toSplit) != 2) {
                    return "Bitte gib die Öffnungszeiten im Format HH:MM an.";
            }

            if($from > $to) {
                return "Die Beginnzeit muss vor der Endzeit liegen.";
            }

            $localFrom = intval($fromSplit[0]) * 60 * 60 + intval($fromSplit[1] * 60);
            $localTo = intval($toSplit[0] * 60 * 60 + $toSplit[1] * 60);

            $d = intval(date("Z"));

            $utcFrom = $localFrom - $d;
            $utcTo = $localTo - $d;


            $openingHours[$dayKey][$elemKey]["from"] = $utcFrom;
            $openingHours[$dayKey][$elemKey]["to"] = $utcTo;
        }
    }

    if($holidays === null) $holidays = array();

    foreach($holidays as $holidayKey => $holidaySlot) {
        $from = strtotime($holidaySlot["from"]);
        $to = strtotime($holidaySlot["to"]);

        if($from === false || $to === false) {
            return "Bitte gib gültige Beginn- und Endzeiten an.";
        }
        if($from > $to) {
            return "Das Beginndatum des Urlaubs muss vor dem Enddatum liegen";
        }

        $holidays[$holidayKey]["from"] = $from;
        $holidays[$holidayKey]["to"] = $to;
    }


    storeImpl("defaultReservationDuration", $defaultReservationDuration);
    storeImpl("maxAmountOfPersons", $maxAmountOfPersons);
    storeImpl("maxUnusedSeatsPerReservation", $maxUnusedSeatsPerReservation);
    storeImpl("canReservateInMinutes", $canReservateInMinutes);

    storeImpl("noFreeTablesError", $noFreeTablesError);
    storeImpl("tooManyPersonsError", $tooManyPersonsError);
    storeImpl("adminConfirmationMail", $adminConfirmationMail);
    storeImpl("userConfirmationMail", $userConfirmationMail);
    storeImpl("adminAddress", $adminAddress);
    storeImpl("openingHours", json_encode($openingHours));
    storeImpl("holidays", json_encode($holidays));

    return null;
}


function getDefaultReservationDuration() {
    $r = getImpl("defaultReservationDuration");
    return ($r === null) ? 60 : $r;
}

function getMaxAmountOfPersons() {
    $r = getImpl("maxAmountOfPersons");
    return ($r === null) ? 5 : $r;
}

function getMaxUnusedSeatsPerReservation() {
    $r = getImpl("maxUnusedSeatsPerReservation");
    return ($r === null) ? 5 : $r;
}

function getCanReservateInMinutes() {
    $r = getImpl("canReservateInMinutes");
    return ($r === null) ? 10 : $r;
}

function getTooManyPersonsError() {
    $r = getImpl("tooManyPersonsError");
    return ($r === null) ? "Zu viele Personen." : $r;
}

function getNoFreeTablesError() {
    $r = getImpl("noFreeTablesError");
    return ($r === null) ? "Kein Tisch frei" : $r;
}

function getUserConfirmationMail() {
    $r = getImpl("userConfirmationMail");
    return($r === null) ? "Deine Reservierung ist bei uns eingegangen." : $r;
}

function getAdminConfirmationMail() {
    $r = getImpl("adminConfirmationMail");
    return ($r == null) ? "Eine neue Reservierung ist eingegangen." : $r;

}

function getAdminAddress() {
    $r = getImpl("adminAddress");
    return ($r == null) ? "" : $r;
}

function getOpeningHours() {
    $r = getImpl("openingHours");
    if($r === null) {
        $r = array();
        for($c = 0; $c < 7; $c++) {
            $r[] = array(
                array(
                    "from" => 11 * 60 * 60,
                    "to" => 13 * 60 * 60
                )
            );
        }

        return $r;
    } else {
        return json_decode($r, true);
    }
}

function getHolidays() {
    $r = getImpl("holidays");
    return ($r === null) ? array() : json_decode($r, true);
}

function getOpeningHoursOnWeekday(int $timestamp) {
    // wir benötigen den Tag in der lokalen Zeitzone
    $weekday = date("w", $timestamp);

    /**
     * bei $weekday entspricht eine 0 einem Sonntag. Wir wollen aber, dass die 0
     * einem Montag entspricht, somit ist eine Umwandlung notwendig
     */
    $conversionArr = array(6, 0, 1, 2, 3, 4, 5);
    $weekday = $conversionArr[$weekday];

    return getOpeningHours()[$weekday];
}

/**
 * Diese Funktion prüft, ob der angegebene UNIX-Timestamp (Anzahl Sekunden seit 1.1.1970 00:00 UTC)
 * innerhalb der Öffnungszeiten liegt. 
 */
function isOpen($timestamp) {
    // check if the timestamp is in the holidays
    $holidays = getHolidays();
    foreach($holidays as $h) {
        $from = $h["from"];
        $to = $h["to"];

        while($from <= $to) {
            // check
            if(date("d", $timestamp) == date("d", $from) &&
                    date("m", $timestamp) == date("m", $from) &&
                    date("Y", $timestamp) == date("Y", $from)) {
                return false;
            }

            $from += 24 * 60 * 60;
        }
    }

    $openingHours = getOpeningHoursOnWeekday($timestamp);

    // in UTC, von 0 bis 86400
    $secondsSinceMidnight = $timestamp % 86400;

    foreach($openingHours as $timeSlot) {
        $from = $timeSlot["from"];
        $to = $timeSlot["to"];

        $d = 24 * 60 * 60;
        if(($secondsSinceMidnight >= $from && $secondsSinceMidnight <= $to) ||
            ($secondsSinceMidnight >= $from + $d && $secondsSinceMidnight <= $to + $d) ||
            ($secondsSinceMidnight >= $from - $d  && $secondsSinceMidnight <= $to - $d)) {
            return true;
        }
    }

    return false;
}

/**
 * Diese Funktion wandelt einen Zeitstempel (Anzahl Sekunden seit Mitternacht, in UTC)
 * um in einen String (H:i) in der lokalen Zeitzone.
 */
function secondsToValueString(int $seconds) {
    // Umrechnung in lokale Zeitzone
    $seconds += intval(date("Z"));

    $h = floor($seconds / (60 * 60));     
    $m = floor(($seconds / 60) - ($h * 60));

    $ret = str_pad($h, 2, '0', STR_PAD_LEFT).":".str_pad($m, 2, '0', STR_PAD_LEFT);
    return $ret;
}

function storeImpl($key, $value) {
    $option = get_page_by_title($key, ARRAY_A, "options");

    $id = wp_insert_post(array(
        "ID" => ($option === null ? 0 : $option["ID"]),
        "post_title" => $key,
        "post_content" => $value,
        "post_type" => "options"
    ));
}

function getImpl($key) {
    $option = get_page_by_title($key, ARRAY_A, "options");
    return ($option === null) ? null : $option["post_content"];
}