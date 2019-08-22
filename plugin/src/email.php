<?php
    function emailToUser(int $from, array $tables, int $numberOfSeats, string $firstname, string $lastname, string $email)
    {
        $content = getUserConfirmationMail();
        $content = prepareEmailContent($content, $from, $tables, $numberOfSeats, $firstname, $lastname);

        return wp_mail($email, "Deine Reservierung ist bei uns eingegangen", $content, 'Content-Type: text/html');
    }

    function emailToAdmin(int $from, array $tables, int $numberOfSeats, string $firstname, string $lastname)
    {
        $content = getAdminConfirmationMail();
        $mail = getAdminAddress();
        if ($content == "" || $content === null || $mail === "") {
            return true;
        }

        $content = prepareEmailContent($content, $from, $tables, $numberOfSeats, $firstname, $lastname);

        return wp_mail($mail, "Neue Reservierung eingegangen!", $content, 'Content-Type: text/html');
    }

    function prepareEmailContent(string $content, int $from, array $tables, int $numberOfSeats, string $firstname, string $lastname)
    {
        $tableString = "";
        foreach ($tables as $t) {
            $tableString .= $t;
        }

        $content = str_replace("\$FIRSTNAME", $firstname, $content);
        $content = str_replace("\$LASTNAME", $lastname, $content);
        $content = str_replace("\$TABLES", $tableString, $content);
        $content = str_replace("\$BEGIN", date("d.m.Y H:i", $from), $content);
        $content = str_replace("\$NUMBEROFSEATS", $numberOfSeats, $content);

        return $content;
    }
