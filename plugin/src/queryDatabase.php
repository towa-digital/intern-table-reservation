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

        return $allTables;
    }

   /**
     * Gibt ein Array zurück, welches alle in der Datenbank enthaltenen Reservierungen enthält.
     * Jedes der Elemente im Array ist ebenfalls ein (assoziatives) Array, welches folgende Schlüssle enthält:
     * - id: eindeutige ID der Reservierung
     * - from: Beginn der Reservierung, als UNIX-Timestamp (Anzahl Sekunden seit 1.1.1970 00:00:00 UTC)
     * - to: Ende der Reservierung, als UNIX-Timestamp (Anzahl Sekunden seit 1.1.1970 00:00:00 UTC)
     * - tableIds: Array bestehend aus allen Tisch-IDs, die von der Reservierung betroffen sind
     * - firstname: Vorname
     * - lastname: Nachname
     * - mail: E-Mail, als String
     * - phonenumber: Telefonnummer, als String
     * - ip: IP-Adresse, als String
     */
    function getReservations() {
        $allReservations = array();

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
                "tableIds" => get_field("tables"),
                "firstname" => get_field("firstname"),
                "lastname" => get_field("lastname"),
                "mail" => get_field("mail"),
                "phonenumber" => get_field("phonenumber"),
                "ip" => get_field("ip")
            ));

        }

        return $allReservations;
    }

    function getTableById($id) {
        return array(
            "id" => $id,
            "title" => get_the_title($id),
            "isOutside" => get_field("isOutside", $id),
            "seats" => get_field("seats", $id)
        );
    }

    function deleteTable($id) {
        $allReservations = getReservations();
        foreach($allReservations as $reservation) {
            foreach($reservation["tableIds"] as $tableId) {
                if($tableId == $id) {
                    wp_delete_post($reservation["id"]);
                }
            }
        }

        wp_delete_post($id);
    }

    function deleteReservation($id) {
        wp_delete_post($id);
    }

    function addReservation(array $tables, $from, $to, $firstname, $lastname, $mail, $phonenumber) {
        $id = wp_insert_post(array(
            'post_title'=>'Reservierung', 
            'post_type'=>'reservations', 
            'post_content'=>'',
            'post_status'=>'publish'
          ));
        if($id) {
            add_post_meta($id, "tables", $tables);
            add_post_meta($id, "from", date("Y-m-d H:i:s", $from));
            add_post_meta($id, "to", date("Y-m-d H:i:s", $to));
            add_post_meta($id, "firstname", $firstname);
            add_post_meta($id, "lastname", $lastname);
            add_post_meta($id, "mail", $mail);
            add_post_meta($id, "phonenumber", $phonenumber);
            add_post_meta($id, "ip", $_SERVER['REMOTE_ADDR']);
        }

        return $id; 
    }

    function addTable(string $title, bool $isOutside, int $numberOfSeats) {
        $id = wp_insert_post(array(
            'post_title'=>$title, 
            'post_type'=>'tables', 
            'post_content'=>'',
            'post_status'=>'publish'
          ));
        if($id) {
            add_post_meta($id, "isOutside", $isOutside);
            add_post_meta($id, "seats", $numberOfSeats);
        }

        return $id;
    }

    function isTableFree($tableId, $startTime, $endTime, $allReservations) {
        if($startTime > $endTime) throw new Exception("Die Startzeit darf nicht größer sein als die Endzeit.");

        foreach($allReservations as $reservation) {
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

    function getFreeTables($startTime, $endTime) {
        if($startTime > $endTime) throw new Exception("Die Startzeit darf nicht größer sein als die Endzeit.");

        $allTables = getTables();
        $allReservations = getReservations();

        // falls der Tisch nicht frei ist, wird er aus dem Array mit allen Tischen entfernt
        foreach($allTables as $elemKey => $table) {
            if(! isTableFree($table, $startTime, $endTime, $allReservations)) {
                unset($allTables[$elemKey]);
            }
        }

        return $allTables;
    }
 ?>