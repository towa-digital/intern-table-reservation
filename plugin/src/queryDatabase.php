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



?>