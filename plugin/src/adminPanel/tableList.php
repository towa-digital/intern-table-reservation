<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../verification.php");

function applyStyle_tableList() {
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("list_style", plugins_url("style/list.css", __FILE__));
}

function show_tableList() {
    //AJAX
   wp_enqueue_script("ajax", "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js");

    // Quick-Edit-Feature hinzufügen
    wp_enqueue_script("quickEdit_script", plugins_url("script/quickEdit.js", __FILE__));
    wp_enqueue_script("quickEdit_tableList_script", plugins_url("script/quickEdit_tableList.js", __FILE__));

    if(isset($_POST["tableToDelete"])) {
        deleteTable($_POST["tableToDelete"]);
    }

    if(isset($_POST["tableToEdit"]) && isset($_POST["title"]) && isset($_POST["numberOfSeats"])) {
        if(empty($_POST["tableToEdit"]) || empty($_POST["title"]) || empty($_POST["numberOfSeats"])) {
            echo '<p class="formError">Bitte fülle alle Pflichtfelder aus!</p>';
        } else {
            $id = $_POST["tableToEdit"];
            $title = $_POST["title"];
            $isOutside = $_POST["isOutside"] === null ? false : true;
            $numberOfSeats = $_POST["numberOfSeats"];


            $errorMsg = verifyTable($title, $isOutside, $numberOfSeats, $id);
            if($errorMsg === null) {
                addTable($title, $isOutside, $numberOfSeats, $id);
            } else {
                echo '<p class="formError">'.$errorMsg.'</p>';
            }
        }
    }
?>

<div id="main">
    <h1 class="inline">Alle Tische</h1>
    <a href="admin.php?page=addtable" class="button" id="addtable">Neuen Tisch erstellen</a>
    <form method="post">
        <table class="content">
            <tr id="head">
                <th style="width=50%">Titel</th>
                <th style="width=16%">ist draußen?</th>
                <th style="width=16%">Platzanzahl</th>
                <th></th>
            </tr>
            <?php
                $allTables = getTables();
                foreach($allTables as $r) {
                    echo '<tr id="row_'.$r["id"].'">';
                    echo '<td class="m_title">'.$r["title"].'</td>';
                    echo '<td class="m_isOutside">'.($r["isOutside"] ? "ja" : "nein").'</td>';
                    echo '<td class="m_numberOfSeats">'.$r["seats"].'</td>';

                    echo '<td><button type="submit" name="tableToDelete" id="deleteBtn_" value="'.$r["id"].'">Löschen</button>';

                    echo ' <button type="button" id="editBtn_'.$r["id"].'" class="edit" onclick="edit('.$r["id"].')">Bearbeiten</button>';
                    echo ' <button type="submit"  id="saveBtn_'.$r["id"].'" class="hidden edit" name="tableToEdit" value="'.$r["id"].'">Speichern</button>';
                    echo ' <button type="button"  id="cancelBtn_'.$r["id"].'" onclick="cancelEdit()" class="hidden edit">Abbrechen</button></td>';

                    echo '</tr>';
                }

              ?>
        </table>
    </form>
</div>
<?php
}
?>