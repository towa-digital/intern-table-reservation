<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../verification.php");

function applyStyle_tableList() {
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("list_style", plugins_url("style/list.css", __FILE__));
}

function show_tableList() {
    // Quick-Edit-Feature hinzufügen
    wp_enqueue_script("quickEdit_script", plugins_url("script/quickEdit.js", __FILE__));
    wp_enqueue_script("quickEdit_tableList_script", plugins_url("script/quickEdit_tableList.js", __FILE__));

    if(isset($_POST["tableToDelete"])) {
        deleteTable($_POST["tableToDelete"]);
    }
    var_dump($_POST);
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
    <h1>Alle Tische</h1>
    <button>Neuen Tisch erstellen</button>
    <form method="post">
        <table class="content">
            <tr>
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

                    echo '<td><button type="submit" name="tableToDelete" value="'.$r["id"].'">Löschen</button>';

                    echo ' <button type="button" id="editBtn_'.$r["id"].'" onclick="edit('.$r["id"].')">Bearbeiten</button>';
                    echo ' <button type="submit" id="saveBtn_'.$r["id"].'" class="hidden" name="tableToEdit" value="'.$r["id"].'">Speichern</button>';
                    echo ' <button type="button" id="cancelBtn_'.$r["id"].'" onclick="cancelEdit()" class="hidden">Abbrechen</button></td>';

                    echo '</tr>';
                }

              ?>
        </table>
    </form>
</div>
<?php
}
?>