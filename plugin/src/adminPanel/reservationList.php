<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../verification.php");
require_once(__DIR__."/../options.php");


function applyStyle_reservationList()
{
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("list_style", plugins_url("style/list.css", __FILE__));
    wp_enqueue_style("tabs_style", plugins_url("style/tabs.css", __FILE__));
}

function show_reservationList()
{
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

    // Sortierfuntion
    wp_enqueue_script("sort_script", plugins_url("script/sort.js", __FILE__));

    if (isset($_POST["reservationToDelete"]) && current_user_can("tv_deleteReservations")) {
        deleteReservation($_POST["reservationToDelete"]);
    }

    $required = ["reservationToEdit", "table", "from", "to", "firstname", "lastname"];
    $isset = true;
    $error = false;
    foreach ($required as $field) {
        // prüfe, ob mindestens ein Feld nicht gesetzt ist
        if (! isset($_POST[$field])) {
            $isset = false;
            break;
        }

        // Prüfe, ob mindestens ein Feld leer ist
        if (empty($_POST[$field])) {
            $error = true;
            break;
        }
    }
    if ($isset && current_user_can("tv_editReservations")) {
        if (! $empty) {
            $id = $_POST["reservationToEdit"];
            $tables = $_POST["table"];
            $from = strtotime($_POST["from"]);
            $to = strtotime($_POST["to"]);
            $numberOfSeats = ($_POST["numberOfSeats"] == "" ? 0 : $_POST["numberOfSeats"]);
            $firstname = ($_POST["firstname"] === null) ? "" : $_POST["firstname"];
            $lastname = ($_POST["lastname"] === null) ? "" : $_POST["lastname"];
            $mail = ($_POST["mail"] === null) ? "" : $_POST["mail"];
            $phonenumber = ($_POST["phonenumber"] === null) ? "" : $_POST["phonenumber"];
            $remarks = ($_POST["remarks"] === null) ? "" : $_POST["remarks"];

            $errorMsg = verifyReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber, $remarks, $id);

            if ($errorMsg === null) {
                addReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber, $remarks, $id);
            } else {
                echo '<p class="error">'.$errorMsg.'</p>';
            }
        } else {
            echo '<p class="error">Bitte fülle alle Pflichtfelder aus!</p>';
        }
    }

    $allTables = getTables();
    $allReservations = getReservations();
    echo '<script>const allTables ='.json_encode($allTables).';</script>';

    /*
     * Aufsteigende Sortierung nach der Differenz zwischen der Beginnzeit der Reservierung und der aktuellen Zeit.
     */
    usort($allReservations, function ($a, $b) {
        if ($a["from"] < $b["from"]) {
            return -1;
        } elseif ($a["from"] == $b["from"]) {
            return 0;
        } else {
            return 1;
        }
    }); ?>

<script>
    const DEFAULT_RESERVATION_DURATION = <?php echo getDefaultReservationDuration(); ?>;
    const CAN_RESERVATE_IN_MINUTES = <?php echo getCanReservateInMinutes(); ?>;
</script>
<div id="main">
    <div class="tabLinkContainer">
        <button onclick="openTab(this, 'current')" class="activeTabBtn tabListBtn">Aktuelle Reservierungen</button>
        <button onclick="openTab(this, 'past')" class="tabListBtn">Vergangene Reservierungen</button>
    </div>
    <div id="current" class="tabElement">
        <p id="jsError" class="hidden"></h1>
        <h1 class="inline">Aktuelle Reservierungen</h1>
        <?php if(current_user_can("tv_addReservations")) echo '<a href="admin.php?page=addreservation" class="btn">Neue Reservierung erstellen</a>' ?>       
        <form method="post">
            <table class="content">
                <tr id="head">
                    <th style="width: 8%; max-width: 8%">
                        Tische
                    </th>
                    <th style="width: 10%" id="m_from_current">
                        von
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, 'date')"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, 'date')"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 10%" id="m_to_current">
                        bis
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, 'date')"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, 'date')"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 11.5%" id="m_numberOfSeats_current">
                        Personenzahl
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, 'number')"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, 'number')"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 10%" id="m_firstname_current">
                        Vorname
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 11%" id="m_lastname_current">
                        Nachname
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 10%" id="m_mail_current">
                        E-Mail
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 12%" id="m_phonenumber_current">
                        Tel.-Nummer
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 12%" id="m_remarks_current">
                        Anmerkungen
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 8%"></th>
                </tr>
                <?php
                $dateFormat = "d.m.Y H:i";
    foreach ($allReservations as $r) {
        if ($r["to"] < time()) {
            continue;
        }

        echo '<tr class="toSort" id="row_'.$r["id"].'">';
            
        echo '<td class="m_tables">';
        for ($c = 0; $c < count($r["tableIds"]); $c++) {
            $tableId = $r["tableIds"][$c];

            echo getTableById($tableId)["title"].($c != count($r["tableIds"]) - 1 ? "," : "");
        }
        echo '</td>';

        echo '<td class="m_from m_from_current">'.date($dateFormat, $r["from"]).'</td>';
        echo '<td class="m_to m_to_current">'.date($dateFormat, $r["to"]).'</td>';
        echo '<td class="m_numberOfSeats m_numberOfSeats_current">'.($r["numberOfSeats"] == 0 ? "" : $r["numberOfSeats"]).'</td>';
        echo '<td class="m_firstname m_firstname_current">'.$r["firstname"].'</td>';
        echo '<td class="m_lastname m_lastname_current">'.$r["lastname"].'</td>';
        echo '<td class="m_mail m_mail_current">'.$r["mail"].'</td>';
        echo '<td class="m_phonenumber m_phonenumber_current">'.$r["phonenumber"].'</td>';
        echo '<td class="m_remarks m_remarks_current">'.$r["remarks"].'</td>';


        echo '<td>';
        if(current_user_can("tv_deleteReservations")) {
            echo '<button type="submit" name="reservationToDelete" value="'.$r["id"].'" class="edit" id="deleteBtn_'.$r["id"].'" onclick="return confirm(\'Willst du diesen Eintrag wirklich löschen?\');"><i class="fa fa-trash"></i></button>';
        }

        if(current_user_can("tv_editReservations")) {
            echo '<button type="button" id="editBtn_'.$r["id"].'" class="edit" onclick="edit('.$r["id"].')"><i class="fa fa-pencil"></i></button>';
            echo '<button type="submit" id="saveBtn_'.$r["id"].'" class="hidden edit" name="reservationToEdit" value="'.$r["id"].'"><i class="fa fa-floppy-o"></i></button>';
            echo '<button type="button" id="cancelBtn_'.$r["id"].'" onclick="cancelEdit()" class="hidden edit"><i class="fa fa-times"></i></button>';
        }
        echo '</td>';

        echo '</tr>';
    } ?>
            </table>
        </form>
    </div>
    <div id="past" class="tabElement hidden">
        <h1 class="inline">Vergangene Reservierungen</h1>
        <a href="admin.php?page=addreservation" class="btn">Neue Reservierung erstellen</a>
        <form method="post">
            <table class="content">
            <tr id="head">
                    <th style="width: 8%; max-width: 8%">
                        Tische
                    </th>
                    <th style="width: 10%" id="m_from_past">
                        von
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, 'date')"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, 'date')"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 10%" id="m_to_past">
                        bis
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, 'date')"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, 'date')"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 12%" id="m_numberOfSeats_past">
                        Personenzahl
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, 'number')"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, 'number')"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 11%" id="m_firstname_past">
                        Vorname
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 11%" id="m_lastname_past">
                        Nachname
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 12.5%" id="m_mail_past">
                        E-Mail
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 12%" id="m_phonenumber_past">
                        Tel.-Nummer
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 12.5%" id="m_remarks_past">
                        Anmerkungen
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th style="width: 8%"></th>
                </tr>
                <?php
                $dateFormat = "d.m.Y H:i";
    foreach ($allReservations as $r) {
        if ($r["to"] >= time()) {
            continue;
        }

        echo '<tr class="toSort" id="row_'.$r["id"].'">';
            
        echo '<td class="m_tables">';
        foreach ($r["tableIds"] as $tableId) {
            echo getTableById($tableId)["title"].',';
        }
        echo '</td>';

        echo '<td class="m_from m_from_past">'.date($dateFormat, $r["from"]).'</td>';
        echo '<td class="m_to m_to_past">'.date($dateFormat, $r["to"]).'</td>';
        echo '<td class="m_numberOfSeats m_numberOfSeats_past">'.$r["numberOfSeats"].'</td>';
        echo '<td class="m_firstname m_firstname_past">'.$r["firstname"].'</td>';
        echo '<td class="m_lastname m_lastname_past">'.$r["lastname"].'</td>';
        echo '<td class="m_mail m_mail_past">'.$r["mail"].'</td>';
        echo '<td class="m_phonenumber m_phonenumber_past">'.$r["phonenumber"].'</td>';
        echo '<td class="m_remarks m_remarks_past">'.$r["remarks"].'</td>';


        echo '</tr>';
    } ?>
            </table>
        </form>
    </div>
</div>

<?php
}
?>