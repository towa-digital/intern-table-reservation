<?php
require_once("options.php");


function verifyTable(string $title, bool $isOutside, int $numberOfSeats, bool $isDisabled, $posX = 0, $posY = 0, $width = 0, $height = 0, int $id = 0)
{
    // falls id übergeben, stelle sicher dass ID ein Tisch ist
    if ($id !== 0 && get_post_type($id) != "tables") {
        return "Die ID des Tisches, der bearbeitet werden soll, ist keinem Tisch zugeordet.";
    }

    // stelle sicher, dass title noch nicht existiert
    $existingPage = get_page_by_title($title, OBJECT, 'tables');
    if ($existingPage !== null && $existingPage->ID != $id) {
        return "Ein Tisch mit diesem Namen existiert bereits.";
    }

    return null;
}

function verifyReservation(array $tables, int $from, int $to, int $numberOfSeats, string $firstname, string $lastname, string $mail, string $phonenumber, string $remarks, $id = 0, $frontend = false)
{
    if ($from == "") {
        return "Das Beginndatum muss angegeben sein!";
    }
    if ($to == "") {
        return "Das Enddatum muss angegeben sein!";
    }
    if ($frontend && ($firstname == "" || $lastname == "")) {
        return "Vor- und Nachname müssen angegeben sein!";
    }
    if ($frontend && $mail == "") {
        return "Die E-Mail muss angegeben sein!";
    }


    // entferne leere Werte aus dem Array
    $tables = array_filter($tables);

    // stelle sicher, dass keine Duplikate in tables enthalten sind
    $duplicate = false;
    foreach ($tables as $key1 => $value1) {
        foreach ($tables as $key2 => $value2) {
            if ($key1 != $key2 && $value1 == $value2) {
                $duplicate = true;
                break 2;
            }
        }
    }
    
    // iteriere über tables und stelle sicher, dass jede ID im Array tatsächlich einem Tisch zugeordnet ist
    $allAreTables = true;
    foreach ($tables as $t) {
        if (get_post_type($t) != "tables") {
            $allAreTables = false;
        }
    }

    // iteriere über tables und stelle sicher, dass jeder Tisch frei ist
    $allReservations = getReservations();
    $allTablesFree = true;
    foreach ($tables as $t) {
        if (! isTableFree($t, $from, $to, $allReservations, $id)) {
            $allTablesFree = false;
        }
    }

    // stelle sicher, dass falls eine ID übergeben wurde, diese einer Reservierung zugeordnet ist
    if ($id != 0 && get_post_type($id) != "reservations") {
        return "Die ID der Reservierung, die bearbeitet werden soll, ist keiner Reservierung zugeordnet.";
    }

    // stelle sicher, dass die Reservierung beginnt, bevor sie aufhört
    if ($from > $to) {
        return "Das Beginndatum darf nicht nach dem Enddatum liegen.";
    }

    // stelle sicher, dass gülitge Datumsangaben eingegeben wurden
    if ($from < time() + (getCanReservateInMinutes() * 60) || $from > time() + ((365 / 2) * 24 * 60 * 60)) {
        return "Das Beginndatum darf nicht weniger als ".getCanReservateInMinutes()." Minuten und nicht mehr als ein halbes Jahr in der Zukunft liegen.";
    }


    // stelle sicher, dass die Reservierung nicht länger als eine Woche dauert
    if (($to - $from) > (7 * 24 * 60 * 60)) {
        return "Die Reservierdauer darf nicht größer als eine Woche sein.";
    }

    // stelle sicher, dass die Reservierung innerhalb der Öffnungszeiten liegt
    if ($frontend && ! isOpen($from)) {
        return "Es kann keine Reservierung außerhalb der Öffnungszeiten getätigt werden.";
    }

    // stelle sicher, dass mindestens ein Tisch reserviert ist
    if (count($tables) == 0) {
        return "Mindestens ein Tisch muss reserviert werden!";
    }

    // stelle sicher, dass keine Duplikate in der Liste sind
    if ($duplicate) {
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

    // stelle sicher, dass Anzahl Personen nicht negativ ist
    if ($frontend && $numberOfSeats <= 0) {
        return "Die Anzahl der Personen muss größer gleich 1 sein";
    }
    if ($numberOfSeats < 0) {
        return "Die Anzahl der Personen darf nicht negativ sein";
    }

    if ($frontend && $numberOfSeats > getMaxAmountOfPersons()) {
        return getTooManyPersonsError();
    }


    /**
     * dazugehörige table-Objekte zum tables-Array laden
     */
    $tableObjects = [];
    foreach($tables as $id) {
        array_push($tableObjects, getTableById($id));
    }


    /**
     * Das tables-Array muss sortiert werden, damit die darauf folgende Prüfung, ob zu viele Sitze reserviert wurden,
     * korrekt stattfinden kann.
     */
    usort($tableObjects, function($a, $b) {
        return intval($b["seats"]) - intval($a["seats"]);
    });



    // zähle die Sitzplätze an allen Tischen und prüfe, ob die Reservierung für mehr Leute gedacht ist
    /**
     * Ferner müssen wir ausschließen, dass eine Reservierung für zwei Personen z.B. zwei Tische mit zwei und einem
     * Platz umfasst, da am zweiten Tisch dann keine Person sitzen würde.
     */
    /**
     * Weiters prüft diese Schleife, ob mindestens ein Tisch nicht reserviert werden kann.
     */
    $cannotReserveTable = false;

    $tooManyTablesForPersons_error = false;
    $tooMuchTablesForPersons_flag = false;
    $availableSeats = 0;
    foreach($tableObjects as $t) {
        if($tooMuchTablesForPersons_flag) $tooManyTablesForPersons_error = true;

        $availableSeats += $t["seats"];
        $cannotReserveTable |= $t["isDisabled"];

        if($availableSeats >= $numberOfSeats) $tooMuchTablesForPersons_flag = true;
    }
    if ($availableSeats < $numberOfSeats) {
        return "Die gebuchten Tische haben nicht die gewünschte Anzahl an Sitzplätzen";
    }

    // prüfe, ob die Anzahl zu reservierender Plätze über der Anzahl benötigten Plätze liegt
    if ($frontend && $availableSeats > $numberOfSeats + getMaxUnusedSeatsPerReservation()) {
        return "Du hast mehr als ".getMaxUnusedSeatsPerReservation()." Plätze reserviert als benötigt.";
    }

    if($frontend && $tooMuchTablesForPersons_error) {
        return "Mindestens ein Tisch in deiner Reservierung wird nicht benötigt!";
    }

    // stelle sicher, dass eine gültige E-Mail-Adresse eingegeben wurde
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Die E-Mail-Adresse ist nicht gültig.";
    }

    return null;
}
