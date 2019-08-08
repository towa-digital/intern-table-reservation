<?php
require_once(__DIR__."/../queryDatabase.php");

function applyStyle_reservationList() {
    wp_enqueue_style("stylesheet_name", plugins_url("style/list.css", __FILE__));
}

function show_reservationList() {
    if(isset($_POST["reservationToDelete"])) {
        deleteReservation($_POST["reservationToDelete"]);
    }

    echo '<form method="post"><ul id="listContent">';

    $allTables = getTables();
    $allReservations = getReservations();
    
    usort($allReservations, function($a, $b) {
        if($a < $b) return -1;
        else if ($a == $b) return 0;
        else return 1;
    });

    foreach($allReservations as $r) {
        echo '<li class="record">';
        echo '<p class="info1">'.$r["lastname"].', '.$r["firstname"].'</p>';

        echo '<p class="info2">';
        foreach($r["tableIds"] as $tableId) {
            echo getTableById($tableId)["title"].',';
        }
        echo '</p>';

        $dateFormat = "d.m.Y H:i";
        echo '<p class="info2">von '.date($dateFormat, $r["from"]).' bis '.date($dateFormat, $r["to"]).'</p>';
        echo '<p class="info3">Mail: '.$r["mail"].'</p>';
        echo '<p class="info3">Telefon: '.$r["phonenumber"].'</p>';
        echo '<button type="submit" name="reservationToDelete" value="'.$r["id"].'">Löschen</button>';
        echo '</li>';
    }

    echo '</ul></form>';
}
?>