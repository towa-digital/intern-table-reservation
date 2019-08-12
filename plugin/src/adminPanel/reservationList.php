<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../verification.php");


function applyStyle_reservationList() {
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("list_style", plugins_url("style/list.css", __FILE__));
    wp_enqueue_style("tabs_style", plugins_url("style/tabs.css", __FILE__));
}

function show_reservationList() {
    // Quick-Edit-Feature hinzufügen
    wp_enqueue_script("quickEdit_script", plugins_url("script/quickEdit.js", __FILE__));
    wp_enqueue_script("quickEdit_reservationList_script", plugins_url("script/quickEdit_reservationList.js", __FILE__));

    // Tab-Feature hinzufügen
    wp_enqueue_script("tab_script", plugins_url("script/tabs.js", __FILE__));


    if(isset($_POST["reservationToDelete"])) {
        deleteReservation($_POST["reservationToDelete"]);
    }

    $required = array("reservationToEdit", "table", "from", "to", "firstname", "lastname", "mail", "phonenumber");
    $isset = true;
    $error = false;
    foreach($required as $field) {
        // prüfe, ob mindestens ein Feld nicht gesetzt ist
        if(! isset($_POST[$field])) {
            $isset = false;
            break;
        }

        // Prüfe, ob mindestens ein Feld leer ist
        if(empty($_POST[$field])) {
            $error = true;
            break;
        }
    }
    if($isset) {
        if(! $empty) {
            $id = $_POST["reservationToEdit"];
            $table = $_POST["table"];
            $from = strtotime($_POST["from"]);
            $to = strtotime($_POST["to"]);
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $mail = $_POST["mail"];
            $phonenumber = $_POST["phonenumber"];

            $errorMsg = verifyReservation($table, $from, $to, $firstname, $lastname, $mail, $phonenumber, $id);

            if($errorMsg === null) {
                addReservation(array($table), $from, $to, $firstname, $lastname, $mail, $phonenumber, $id);
            } else {
                echo '<p class="formError">'.$errorMsg.'</p>';
            }
        } else {
            echo '<p class="formError">Bitte fülle alle Pflichtfelder aus!</p>';
        }
    }

    $allTables = getTables();
    $allReservations = getReservations();
    echo '<script>const allTables ='.json_encode($allTables).';</script>';

    /*
        * Aufsteigende Sortierung nach der Differenz zwischen der Beginnzeit der Reservierung und der aktuellen Zeit. 
        */
    usort($allReservations, function($a, $b) {
        if($a < $b) return -1;
        else if ($a == $b) return 0;
        else return 1;
    });
?>


<div id="main">
    <div class="tabLinkContainer">
        <button onclick="openTab(this, 'current')" class="activeTabBtn tabListBtn">Aktuelle Reservierungen</button>
        <button onclick="openTab(this, 'past')" class="tabListBtn">Vergangene Reservierungen</button>
    </div>
    <div id="current" class="tabElement">
        <h1 class="inline">Aktuelle Reservierungen</h1>
        <button>Neue Reservierung erstellen</button>
        <form method="post">
            <table class="content">
                <tr>
                    <th style="width=50%">Tische</th>
                    <th style="width=16%">von</th>
                    <th style="width=16%">bis</th>
                    <th style="width=16%">Vorname</th>
                    <th style="width=16%">Nachname</th>
                    <th style="width=16%">Telefonnummer</th>
                    <th style="width=16%">E-Mail</th>
                    <th></th>
                </tr>
                <?php           
                $dateFormat = "d.m.Y H:i";
                foreach($allReservations as $r) {
                    if($r["to"] < time()) continue;

                    echo '<tr id="row_'.$r["id"].'">';
            
                    echo '<td class="m_tables">';
                    foreach($r["tableIds"] as $tableId) {
                        echo getTableById($tableId)["title"].',';
                    }
                    echo '</td>';

                    echo '<td class="m_from">'.date($dateFormat, $r["from"]).'</td>';
                    echo '<td class="m_to">'.date($dateFormat, $r["to"]).'</td>';
                    echo '<td class="m_firstname">'.$r["firstname"].'</td>';
                    echo '<td class="m_lastname">'.$r["lastname"].'</td>';
                    echo '<td class="m_mail">'.$r["mail"].'</td>';
                    echo '<td class="m_phonenumber">'.$r["phonenumber"].'</td>';

                    echo '<td><button type="submit" name="reservationToDelete" value="'.$r["id"].'">Löschen</button>';

                    echo '<button type="button" id="editBtn_'.$r["id"].'" onclick="edit('.$r["id"].')">Bearbeiten</button>';
                    echo '<button type="submit" id="saveBtn_'.$r["id"].'" class="hidden" name="reservationToEdit" value="'.$r["id"].'">Speichern</button>';
                    echo '<button type="button" id="cancelBtn_'.$r["id"].'" onclick="cancelEdit()" class="hidden">Abbrechen</button></td>';

                    echo '</tr>';
                    }
                ?>
            </table>
        </form>
    </div>
    <div id="past" class="tabElement hidden">
    <h1 class="inline">Vergangene Reservierungen</h1>
        <button>Neue Reservierung erstellen</button>
        <form method="post">
            <table class="content">
                <tr>
                    <th style="width=50%">Tische</th>
                    <th style="width=16%">von</th>
                    <th style="width=16%">bis</th>
                    <th style="width=16%">Vorname</th>
                    <th style="width=16%">Nachname</th>
                    <th style="width=16%">Telefonnummer</th>
                    <th style="width=16%">E-Mail</th>
                    <th></th>
                </tr>
                <?php           
                $dateFormat = "d.m.Y H:i";
                foreach($allReservations as $r) {
                    if($r["to"] >= time()) continue;

                     echo '<tr id="row_'.$r["id"].'">';
            
                    echo '<td class="m_tables">';
                    foreach($r["tableIds"] as $tableId) {
                        echo getTableById($tableId)["title"].',';
                    }
                    echo '</td>';

                    echo '<td class="m_from">'.date($dateFormat, $r["from"]).'</td>';
                    echo '<td class="m_to">'.date($dateFormat, $r["to"]).'</td>';
                    echo '<td class="m_firstname">'.$r["firstname"].'</td>';
                    echo '<td class="m_lastname">'.$r["lastname"].'</td>';
                    echo '<td class="m_mail">'.$r["mail"].'</td>';
                    echo '<td class="m_phonenumber">'.$r["phonenumber"].'</td>';

                    echo '</tr>';
                }
                ?>
            </table>
        </form>
    </div>
</div>

<?php
}
?>