<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../verification.php");


function applyStyle_reservationList() {
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("list_style", plugins_url("style/list.css", __FILE__));
    wp_enqueue_style("tabs_style", plugins_url("style/tabs.css", __FILE__));
}

function show_reservationList() {
    //AJAX
    wp_enqueue_script("ajax", "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js");

    //FontAwesome
    wp_enqueue_script("fontawesome", "https://kit.fontawesome.com/2c9e55113a.js");


    // Quick-Edit-Feature hinzufügen
    wp_enqueue_script("quickEdit_script", plugins_url("script/quickEdit.js", __FILE__));
    wp_enqueue_script("quickEdit_reservationList_script", plugins_url("script/quickEdit_reservationList.js", __FILE__));

    // Tab-Feature hinzufügen
    wp_enqueue_script("tab_script", plugins_url("script/tabs.js", __FILE__));

    
    wp_enqueue_script("loadAvailableTables_script", plugins_url("script/loadAvailableTables.js", __FILE__));
    wp_enqueue_script("reservationsClientVerification_script", plugins_url("script/reservationsClientVerification.js", __FILE__));


    if(isset($_POST["reservationToDelete"])) {
        deleteReservation($_POST["reservationToDelete"]);
    }

    $required = array("reservationToEdit", "table", "from", "to", "firstname", "lastname");
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
            $tables = $_POST["table"];
            $from = strtotime($_POST["from"]);
            $to = strtotime($_POST["to"]);
            $numberOfSeats = ($_POST["numberOfSeats"] == "" ? 0 : $_POST["numberOfSeats"]);
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $mail = $_POST["mail"];
            $phonenumber = $_POST["phonenumber"];

            $errorMsg = verifyReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber, $id);

            if($errorMsg === null) {
                addReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber, $id);
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
        if($a["from"] < $b["from"]) return -1;
        else if ($a["from"] == $b["from"]) return 0;
        else return 1;
    });

?>


<div id="main">
    <div class="tabLinkContainer">
        <button onclick="openTab(this, 'current')" class="activeTabBtn tabListBtn">Aktuelle Reservierungen</button>
        <button onclick="openTab(this, 'past')" class="tabListBtn">Vergangene Reservierungen</button>
    </div>
    <div id="current" class="tabElement">
        <p id="jsError" class="hidden"></h1>
        <h1 class="inline">Aktuelle Reservierungen</h1>
        <a href="admin.php?page=addreservation" class="btn">Neue Reservierung erstellen</a>
        <form method="post">
            <table class="content">
                <tr id="head">
                    <th style="width: 15%; max-width: 15%">Tische</th>
                    <th style="width: 7%">von</th>
                    <th style="width: 7%">bis</th>
                    <th style="width: 7%">Anzahl Plätze</th>
                    <th style="width: 14.5%">Vorname</th>
                    <th style="width: 14.5%">Nachname</th>
                    <th style="width: 14.5%">E-Mail</th>
                    <th style="width: 14.5%">Telefonnummer</th>
                    <th style="width: 6%"></th>
                </tr>
                <?php           
                $dateFormat = "d.m.Y H:i";
                foreach($allReservations as $r) {
                    if($r["to"] < time()) continue;

                    echo '<tr id="row_'.$r["id"].'">';
            
                    echo '<td class="m_tables">';
                    for($c = 0; $c < count($r["tableIds"]); $c++) {
                        $tableId = $r["tableIds"][$c];

                        echo getTableById($tableId)["title"].($c != count($r["tableIds"]) - 1 ? "," : "");
                    }
                    echo '</td>';

                    echo '<td class="m_from">'.date($dateFormat, $r["from"]).'</td>';
                    echo '<td class="m_to">'.date($dateFormat, $r["to"]).'</td>';
                    echo '<td class="m_numberOfSeats">'.($r["numberOfSeats"] == 0 ? "" : $r["numberOfSeats"]).'</td>';
                    echo '<td class="m_firstname">'.$r["firstname"].'</td>';
                    echo '<td class="m_lastname">'.$r["lastname"].'</td>';
                    echo '<td class="m_mail">'.$r["mail"].'</td>';
                    echo '<td class="m_phonenumber">'.$r["phonenumber"].'</td>';

                    echo '<td><button type="submit" name="reservationToDelete" value="'.$r["id"].'" class="edit" id="deleteBtn_'.$r["id"].'" onclick="return confirm(\'Willst du diesen Eintrag wirklich löschen?\');"><i class="fa fa-trash"></i></button>';

                    echo '<button type="button" id="editBtn_'.$r["id"].'" class="edit" onclick="edit('.$r["id"].')"><i class="fa fa-pencil"></i></button>';
                    echo '<button type="submit" id="saveBtn_'.$r["id"].'" class="hidden edit" name="reservationToEdit" value="'.$r["id"].'"><i class="fa fa-floppy-o"></i></button>';
                    echo '<button type="button" id="cancelBtn_'.$r["id"].'" onclick="cancelEdit()" class="hidden edit"><i class="fa fa-times"></i></button></td>';

                    echo '</tr>';
                }
                ?>
            </table>
        </form>
    </div>
    <div id="past" class="tabElement hidden">
        <h1 class="inline">Vergangene Reservierungen</h1>
        <a href="admin.php?page=addreservation" class="btn">Neue Reservierung erstellen</a>
        <form method="post">
            <table class="content">
                <tr id="head">
                    <th style="width=50%">Tische</th>
                    <th style="width=16%">von</th>
                    <th style="width=16%">bis</th>
                    <th style="width=16%">Anzahl Plätze</th>
                    <th style="width=16%">Vorname</th>
                    <th style="width=16%">Nachname</th>
                    <th style="width=16%">E-Mail</th>
                    <th style="width=16%">Telefonnummer</th>
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
                    echo '<td class="m_numberOfSeats">'.$r["numberOfSeats"].'</td>';
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