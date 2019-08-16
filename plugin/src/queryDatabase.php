<?php
    /**
     * Gibt ein Array zurück, welches alle in der Datenbank enthaltenen Tische enthält.
     * Jedes der Elemente im Array ist ebenfalls ein (assoziatives) Array, welches folgende Schlüssle enthält:
     * - id: eindeutige ID der Tisches
     * - title: Bezeichnung des Tisches
     * - isOutside: boolescher Wahrheitswert, welcher true entspricht, wenn sich der Tisch im Außenbereich befindet
     * - seats: Anzahl der Sitzplätze am Tisch
     */
    function getTables() {
        $allTables = array();

        $query = new WP_Query(array(
            'post_type' => 'tables',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));
        
        
        
        while ($query->have_posts()) {
            $query->the_post();
            array_push($allTables, array(
                "id" => get_the_ID(),
                "title" => get_the_title(),
                "isOutside" => get_field("isOutside"),
                "seats" => get_field("seats")
            ));
        }

        wp_reset_postdata();

        return $allTables;
    }

    $isCalledFirstTime = true;
    
   /**
     * Gibt ein Array zurück, welches alle in der Datenbank enthaltenen Reservierungen enthält.
     * Jedes der Elemente im Array ist ebenfalls ein (assoziatives) Array, welches folgende Schlüssle enthält:
     * - id: eindeutige ID der Reservierung
     * - from: Beginn der Reservierung, als UNIX-Timestamp (Anzahl Sekunden seit 1.1.1970 00:00:00 UTC)
     * - to: Ende der Reservierung, als UNIX-Timestamp (Anzahl Sekunden seit 1.1.1970 00:00:00 UTC)
     * - tableIds: Array bestehend aus allen Tisch-IDs, die von der Reservierung betroffen sind
     * - numberOfSeats: Anzahl Plätze
     * - firstname: Vorname
     * - lastname: Nachname
     * - mail: E-Mail, als String
     * - phonenumber: Telefonnummer, als String
     * - ip: IP-Adresse, als String
     */
    function getReservations() {        
        $allReservations = array();
        $isCalledFirstTime = false;
        $query = new WP_Query(array(
            'post_type' => 'reservations',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));  
        
        while ($query->have_posts()) {
            $query->the_post();
            
            array_push($allReservations, array(
                "id" => get_the_ID(),
                "from" => strtotime(get_field("from")),
                "to" => strtotime(get_field("to")),
                "numberOfSeats" => get_field("numberOfSeats"),
                "tableIds" => get_field("tables"),
                "firstname" => get_field("firstname"),
                "lastname" => get_field("lastname"),
                "mail" => get_field("mail"),
                "phonenumber" => get_field("phonenumber"),
                "ip" => get_field("ip")
            ));

        }


        wp_reset_postdata();

        return $allReservations;
    }

    /**
     * Gibt die Daten des Tisches mit der übergebenen ID als assoziatives Array zurück.
     * - id: eindeutige ID der Tisches (dieselbe, die übergeben wurde)
     * - title: Bezeichnung des Tisches
     * - isOutside: boolescher Wahrheitswert, welcher true entspricht, wenn sich der Tisch im Außenbereich befindet
     * - seats: Anzahl der Sitzplätze am Tisch
     * 
     * Im Fehlerfall (wenn die ID nicht einem Tisch zugeordnet ist oder nicht existiert) wird null zurückgegeben.
     */
    function getTableById($id) {
        if(get_post_type($id) != "tables") return null;

        return array(
            "id" => $id,
            "title" => get_the_title($id),
            "isOutside" => get_field("isOutside", $id),
            "seats" => get_field("seats", $id)
        );
    }

    /**
     * Löscht den Tisch mit der übergebenen ID. Ferner werden alle Reservierungen gelöscht, welche entsprechenden Tisch beinhalten.
     * Falls die ID nicht existiert oder keinem Tisch zugeordet ist, wird nichts gelöscht und false zurückgegeben, ansonsten gibt diese
     * Funktion true zurück.
     */
    function deleteTable($id) {
        if(get_post_type($id) != "tables") return false;

        $allReservations = getReservations();
        foreach($allReservations as $reservation) {
            foreach($reservation["tableIds"] as $tableId) {
                if($tableId == $id) {
                    wp_delete_post($reservation["id"]);
                }
            }
        }

        wp_delete_post($id);
        return true;
    }

    /**
     * Löscht die Reservierung mit der übergebenen ID.
     * Falls die ID nicht existiert oder keiner Reservierung zugeordet ist, wird nichts gelöscht und false zurückgegeben, ansonsten gibt diese
     * Funktion true zurück.
     */
    function deleteReservation($id) {
        if(get_post_type($id) != "reservations") return false;

        wp_delete_post($id);
        return false;
    }
    

    /**
     * Fügt eine neue Reservierung ein.
     * ACHTUNG! Diese Funktion prüft NICHT, ob die übergebenen Werte gültig sind, beispielsweise ob ein Tisch schon belegt ist oder die Datumsangaben korrekt sind. Hierfür ist der Aufrufer verantwortlich!
     * - tables: Array aus allen von der Reservierung betroffenen Tischen
     * - from: Beginn der Reservierung als UNIX-Timestamp
     * - to: Ende der Reservierung als UNIX-Timestamp
     * - numberOfSeats: Anzahl Plätze
     * - firstname: Vorname
     * - lastname: Nachname
     * - mail: E-Mail
     * - phonenumber: Telefonnummer
     * - reservationToUpdate: optionaler Parameter, kann angegeben werden, wenn keine neue Reservierung erstellt werden soll, sondern eine bestehende aktualisiert werden soll. Falls die ID angegeben wird, aber keiner Reservierung
     *   zugeordnet ist, wird eine Exception geworfen.
     * 
     * Gibt die ID der erstellten/aktualisierten Reservierung zurück. 
     */
    function addReservation(array $tables, int $from, int $to, int $numberOfSeats, string $firstname, string $lastname, string $mail, string $phonenumber, int $reservationToUpdate = 0) {
        if($reservationToUpdate !== 0 && get_post_type($reservationToUpdate) != "reservations") throw new Exception("reservationToUpdate ist keiner Reservierung zugeordnet");

        $id = wp_insert_post(array(
            'ID' => $reservationToUpdate,
            'post_title'=>'Reservierung', 
            'post_type'=>'reservations', 
            'post_content'=>'',
            'post_status'=>'publish'
        ));

        // entferne leere Werte aus tables
        foreach($tables as $key => $value) {
            if($value == "") {
                array_splice($tables, $key, 1);
            }
        }

        update_field("tables", $tables, $id);
        update_field("from", date("Y-m-d H:i:s", $from), $id);
        update_field("to", date("Y-m-d H:i:s", $to), $id);
        update_field("numberOfSeats", $numberOfSeats, $id);
        update_field("firstname", sanitize_text_field($firstname), $id);
        update_field("lastname", sanitize_text_field($lastname), $id);
        update_field("mail", sanitize_email($mail), $id);
        update_field("phonenumber", sanitize_text_field($phonenumber), $id);
        update_field("ip", $_SERVER["REMOTE_ADDR"], $id);

        return $id; 
    }

    /**
     * Fügt einen neuen Tisch ein.
     * ACHTUNG! Diese Funktion prüft NICHT, ob die übergebenen Werte gültig sind, beispielsweise ob der Name des Tisches schon vorhanden ist. Hierfür ist der Aufrufer verantwortlich!
     * - title: Bezeichnung des Tischs
     * - isOutside: true, wenn sich der Tisch im Außenbereich befindet
     * - numberOfSeats: Anzahl der Sitzplätze am Tisch
     * - tableToUpdate: optionaler Parameter, kann angegeben werden, wenn kein neuer Tisch erstellt werden soll, sondern ein bestehender aktualisiert werden soll. Falls die ID angegeben wrid, aber keinem Tisch
     *   zugeordnet ist, wird eine Exception geworfen.
     *
     * Gibt die ID des erstellten/aktualisierten Tisches zurück.
     */
    function addTable(string $title, bool $isOutside, int $numberOfSeats, int $tableToUpdate = 0) {
        if($tableToUpdate !== 0 && get_post_type($tableToUpdate) != "tables") throw new Exception("tableToUpdate ist keinem Tisch zugeordnet");

        $id = wp_insert_post(array(
            'ID' => $tableToUpdate,
            'post_title'=> sanitize_text_field($title), 
            'post_type'=>'tables', 
            'post_content'=>'',
            'post_status'=>'publish'
          ));

        update_field("isOutside", $isOutside, $id);
        update_field("seats", $numberOfSeats, $id);


        return $id;
    }

    /**
     * Prüft, ob der übergebene Tisch frei ist und gibt true zurück, wenn dies der Fall ist.
     * - tableId: ID des Tisches, welcher geprüft werden soll. Falls diese ID nicht einem Tisch zugeordnet ist, wird eine Exception geworfen.
     * - startTime: gewünschter Startzeitpunkt als UNIX-Timestamp
     * - endTime: gewünschter Endzeitpunkt als UNIX-Timestamp
     * - allReservations: um sich bei mehrfachem Funktionsaufruf hintereinander mehrfache Datenbankabfragen zu sparen, fordert diese Funktion
     *   ein Array mit allen Reservierungen, welches von getReservations() erstellt wird.
     * - reservationId: optionaler Parameter, muss angegeben werden, wenn keine neue Reservierung erstellt wird, sondern eine bestehende bearbeitet.
     *   
     */
    function isTableFree($tableId, $startTime, $endTime, $allReservations, $reservationId = 0) {
        if(get_post_type($tableId) != "tables") throw new Exception(get_post_type($tableId) ."tableId (".$tableId.")ist nicht die ID eines Tisches");
        if($startTime > $endTime) throw new Exception("Die Startzeit darf nicht größer sein als die Endzeit.");

        foreach($allReservations as $reservation) {
            // falls die Reservierung der Reservierung entspricht, die gerade bearbeitet werden soll, weiter fortfahren mit der nächsten Reservierung
            if($reservationId != 0 && $reservation["id"] == $reservationId) {
                continue;
            }

            // falls die Reservierung nicht die gewünschte Zeitspanne betrifft, weiter fortfahren mit der nächsten Reservierung
            if(($startTime < $reservation["from"] && $endTime < $reservation["from"]) || ($startTime > $reservation["to"] && $endTime > $reservation["to"])) {
                continue;
            }

            // falls die Reservierung den übergebenen Tisch beinhaltet, ist der Tisch nicht frei
            foreach($reservation["tableIds"] as $affectedTable) {
                if($tableId == $affectedTable) return false;
            }
        }

        // nachdem keine Reservierung zur gewünschten Zeit den Tisch beinhaltet, ist dieser frei
        return true;
    }

    /**
     * Gibt wie getTables() ein Array aus allen Tischen zurück, allerdings werden alle Tische gefiltert, welche in der gewünschten Zeitspanne nicht frei sind.
     */
    function getFreeTables($startTime, $endTime, $reservationId = 0) {
        if($startTime > $endTime) throw new Exception("Die Startzeit darf nicht größer sein als die Endzeit.");

        $freeTables = array();
        $allTables = getTables();
        $allReservations = getReservations();

        // falls der Tisch frei ist, wird er ins Array mit den verfügbaren Tischen aufgenommen
        foreach($allTables as $elemKey => $table) {
            if(isTableFree($table["id"], $startTime, $endTime, $allReservations, $reservationId)) {
                $freeTables[] = $allTables[$elemKey];
            }
        }

        return $freeTables;
    }

    function getSuitableTables($startTime, $endTime, $numberOfSeats, $reservationId = 0) {
        $freeTables = getFreeTables($startTime, $endTime, $reservationId);

        $suitableTables = array();
        foreach($freeTables as $elemKey => $table) {
            // füge alle Tische hinzu, bei denen nicht mehr Plätze frei bleiben würden, als maxUnusedSeatsPerReservation gestattet
            if($table["seats"] <= $numberOfSeats + get_option("maxUnusedSeatsPerReservation")) {
                $suitableTables[] = $table;
            }
        }

        // falls alle Tische in Summe nicht ausreichen, keinen Tisch zurückgeben
        $suitableTables_seatSum = 0;
        foreach($suitableTables as $table) {
            $suitableTables_seatSum += $table["seats"];
        }

        if($suitableTables_seatSum < $numberOfSeats) return array();
        else return $suitableTables;
    }
 ?>