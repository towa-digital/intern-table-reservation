<?php
    function emailToUser(int $from, array $tables, int $numberOfSeats, string $firstname, string $lastname, string $email) {
        $content = getUserConfirmationMail();
        $content = str_replace("\$FIRSTNAME", $firstname, $content);
        $content = str_replace("\$LASTNAME", $lastname, $content);
        $content = str_replace("\$TABLES", json_encode($tables), $content);
        $content = str_replace("\$BEGIN", date("d.m.Y H:i", $from), $content);
        $content = str_replace("\$NUMBEROFSEATS", $numberOfSeats, $content);
        return wp_mail($email, "Deine Reservierung ist bei uns eingegangen", $content);
    }


?>