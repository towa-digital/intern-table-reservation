<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../verification.php");

function applyStyle_tableList()
{
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("list_style", plugins_url("style/list.css", __FILE__));
}

function show_tableList()
{
    //AJAX
    wp_enqueue_script("ajax", "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js");

    //FontAwesome
    wp_enqueue_script("fontawesome", "https://kit.fontawesome.com/2c9e55113a.js");

    // Quick-Edit-Feature hinzufügen
    wp_enqueue_script("quickEdit_script", plugins_url("script/quickEdit.js", __FILE__));
    wp_enqueue_script("quickEdit_tableList_script", plugins_url("script/quickEdit_tableList.js", __FILE__));

    if (isset($_POST["tableToDelete"])) {
        deleteTable($_POST["tableToDelete"]);
    }

    if (isset($_POST["tableToEdit"]) && isset($_POST["title"]) && isset($_POST["numberOfSeats"])) {
        if (empty($_POST["tableToEdit"]) || empty($_POST["title"]) || empty($_POST["numberOfSeats"])) {
            echo '<p class="formError">Bitte fülle alle Pflichtfelder aus!</p>';
        } else {
            $id = $_POST["tableToEdit"];
            $title = $_POST["title"];
            $isOutside = $_POST["isOutside"] === null ? false : true;
            $numberOfSeats = $_POST["numberOfSeats"];
            $isDisabled = $_POST["isDisabled"] === null ? false : true;


            $errorMsg = verifyTable($title, $isOutside, $numberOfSeats, $isDisabled, $id);
            if ($errorMsg === null) {
                addTable($title, $isOutside, $numberOfSeats, $isDisabled , $id);
            } else {
                echo '<p class="formError">'.$errorMsg.'</p>';
            }
        }
    } ?>

<div id="main">
    <h1 class="inline">Alle Tische</h1>
    <a href="admin.php?page=addtable" class="btn">Neuen Tisch erstellen</a>
    <form method="post">
        <table class="content">
            <tr id="head">
                <th style="width: 40%">Titel</th>
                <th style="width: 16.6%">ist draußen?</th>
                <th style="width: 16.6%">Platzanzahl</th>
                <th style="width: 16.6%">nicht reservierbar?</th>
                <th style="width: 10%"></th>
            </tr>
            <?php
                $allTables = getTables();
    foreach ($allTables as $r) {
        echo '<tr id="row_'.$r["id"].'">';
        echo '<td class="m_title">'.$r["title"].'</td>';
        echo '<td class="m_isOutside">'.($r["isOutside"] ? "ja" : "nein").'</td>';
        echo '<td class="m_numberOfSeats">'.$r["seats"].'</td>';
        echo '<td class="m_isDisabled">'.($r["isDisabled"] ? "ja" : "nein").'</td>';

        echo '<td><button type="submit" name="tableToDelete" class="edit" id="deleteBtn_'.$r["id"].'" value="'.$r["id"].'" onclick="return confirm(\'Willst du diesen Eintrag wirklich löschen?\');"><i class="fa fa-trash"></i></button>';

        echo ' <button type="button" id="editBtn_'.$r["id"].'" class="edit" onclick="edit('.$r["id"].')"><i class="fa fa-pencil"></i></button>';
        echo ' <button type="submit"  id="saveBtn_'.$r["id"].'" class="hidden edit" name="tableToEdit" value="'.$r["id"].'"><i class="fa fa-floppy-o"></i></button>';
        echo ' <button type="button"  id="cancelBtn_'.$r["id"].'" onclick="cancelEdit()" class="hidden edit"><i class="fa fa-times"></i></button></td>';

        echo '</tr>';
    } ?>
        </table>
    </form>
</div>
<?php
}
?>