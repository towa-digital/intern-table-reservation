<?php

function storeOptions($defaultReservationDuration, $maxAmountOfPersons,
        $maxUnusedSeatsPerReservation, $canReservateInMinutes, $tooManyPersonsError,
        $noFreeTablesError, $openingHours) {

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

    var_dump($openingHours);
    
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

            $openingHours[$dayKey][$elemKey]["from"] = intval($fromSplit[0]) * 60 * 60 + intval($fromSplit[1] * 60);
            $openingHours[$dayKey][$elemKey]["to"] = intval($toSplit[0] * 60 * 60 + $toSplit[1] * 60);
        }
    }



    storeImpl("defaultReservationDuration", $defaultReservationDuration);
    storeImpl("maxAmountOfPersons", $maxAmountOfPersons);
    storeImpl("maxUnusedSeatsPerReservation", $maxUnusedSeatsPerReservation);
    storeImpl("canReservateInMinutes", $canReservateInMinutes);

    storeImpl("noFreeTablesError", $noFreeTablesError);
    storeImpl("tooManyPersonsError", $tooManyPersonsError);
    storeImpl("openingHours", json_encode($openingHours));

    return null;
}


function getDefaultReservationDuration() {
    $r = getImpl("defaultReservationDuration");
    return ($r === null) ? 30 : $r;
}

function getMaxAmountOfPersons() {
    $r = getImpl("maxAmountOfPersons");
    return ($r === null) ? 30 : $r;
}

function getMaxUnusedSeatsPerReservation() {
    $r = getImpl("maxUnusedSeatsPerReservation");
    return ($r === null) ? 30 : $r;
}

function getCanReservateInMinutes() {
    $r = getImpl("canReservateInMinutes");
    return ($r === null) ? 30 : $r;
}

function getTooManyPersonsError() {
    $r = getImpl("tooManyPersonsError");
    return ($r === null) ? "Zu viele Personen." : $r;
}

function getNoFreeTablesError() {
    $r = getImpl("noFreeTablesError");
    return ($r === null) ? "Kein Tisch frei" : $r;
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

function getOpeningHoursOnWeekday(int $timestamp) {
    $weekday = date("w", $timestamp);
    
    /**
     * bei $weekday entspricht eine 0 einem Sonntag. Wir wollen aber, dass die 0
     * einem Montag entspricht, somit ist eine Umwandlung notwendig
     */
    $conversionArr = array(6, 0, 1, 2, 3, 4, 5);
    $weekday = $conversionArr[$weekday];
    return getOpeningHours()[$weekday];
}

function isOpen($timestamp) {
    $openingHours = getOpeningHoursOnWeekday($timestamp);

    $secondsSinceMidnight = ((intval(date("G", $timestamp)) * 60) + intval(date("i", $timestamp))) * 60;

    foreach($openingHours as $timeSlot) {
        if($secondsSinceMidnight >= $timeSlot["from"] && $secondsSinceMidnight <= $timeSlot["to"]) {
            return true;
        }
    }

    return false;
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