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

    // Sortierfuntion
    wp_enqueue_script("sort_script", plugins_url("script/sort.js", __FILE__));


    if (isset($_POST["tableToDelete"]) && current_user_can("tv_deleteTables")) {
        deleteTable($_POST["tableToDelete"]);
    }

    if (current_user_can("tv_editTables") && isset($_POST["tableToEdit"]) && isset($_POST["title"]) && isset($_POST["numberOfSeats"])) {
        if (empty($_POST["tableToEdit"]) || empty($_POST["title"]) || empty($_POST["numberOfSeats"])) {
            echo '<p class="error">Bitte fülle alle Pflichtfelder aus!</p>';
        } else {
            $id = $_POST["tableToEdit"];
            $title = $_POST["title"];
            $isOutside = $_POST["isOutside"] === null ? false : true;
            $numberOfSeats = ($_POST["numberOfSeats"] === null) ? 0 : $_POST["numberOfSeats"];
            $isDisabled = $_POST["isDisabled"] === null ? false : true;
            $posX = ($_POST["posX"] === null) ? 0 : $_POST["posX"];
            $posY = ($_POST["posY"] === null) ? 0 : $_POST["posY"];
            $width = ($_POST["width"] === null) ? 0 : $_POST["width"];
            $height = ($_POST["height"] === null) ? 0 : $_POST["height"];





            $errorMsg = verifyTable($title, $isOutside, $numberOfSeats, $isDisabled, $posX, $posY, $width, $height, $id);
            if ($errorMsg === null) {
                addTable($title, $isOutside, $numberOfSeats, $isDisabled, $posX, $posY, $width, $height, $id);
            } else {
                echo '<p class="error">'.$errorMsg.'</p>';
            }
        }
    } ?>

<div id="main">
    <h1 class="inline">Alle Tische</h1>
    <?php if(current_user_can("tv_addTables")) echo '<a href="admin.php?page=managetablesgui" class="btn">Neuen Tisch erstellen</a>' ?>
    <form method="post">
        <table class="content">
            <tr id="head">
                <th style="width: 10%" id="m_title">
                    Titel
                    <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                    <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                </th>
                <th style="width: 16.6%" id="m_isOutside">
                    ist draußen?
                    <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                    <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                </th>
                <th style="width: 16.6%" id="m_numberOfSeats">
                    Platzanzahl
                    <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, 'number')"><i class="fas fa-chevron-up"></i></button>
                    <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, 'number')"><i class="fas fa-chevron-down"></i></button>
                </th>
                <th style="width: 16.6%" id="m_isDisabled">
                    nicht reservierbar?
                    <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true)"><i class="fas fa-chevron-up"></i></button>
                    <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false)"><i class="fas fa-chevron-down"></i></button>
                </th>
                <?php
                    if(current_user_can("tv_editTables")) {
                        echo '<th id="m_posX">
                        Position X
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, \'number\')"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, \'number\')"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th id="m_posY">
                        Position Y
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, \'number\')"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, \'number\')"><i class="fas fa-chevron-down"></i></button>
                    </th>
                    <th id="m_width">
                        Breite
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, \'number\')"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, \'number\')"><i class="fas fa-chevron-down"></i></button> 
                    </th>
                    <th id="m_height">
                        Höhe
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, true, \'number\')"><i class="fas fa-chevron-up"></i></button>
                        <button type="button" class="sortButton" onclick="sort(this, this.parentNode.parentNode.parentNode, false, \'number\')"><i class="fas fa-chevron-down"></i></button>
                    </th>';
                    }
                ?>
                <th style="width: 10%"></th>
            </tr>
            <?php
                $allTables = getTables();
    foreach ($allTables as $r) {
        echo '<tr class="toSort" id="row_'.$r["id"].'">';
        echo '<td class="m_title">'.$r["title"].'</td>';
        echo '<td class="m_isOutside">'.($r["isOutside"] ? "ja" : "nein").'</td>';
        echo '<td class="m_numberOfSeats">'.$r["seats"].'</td>';
        echo '<td class="m_isDisabled">'.($r["isDisabled"] ? "ja" : "nein").'</td>';

        if(current_user_can("tv_editTables")) {
            echo '<td class="m_posX">'.$r["position"]["posX"].'</td>';
            echo '<td class="m_posY">'.$r["position"]["posY"].'</td>';
            echo '<td class="m_width">'.$r["position"]["width"].'</td>';
            echo '<td class="m_height">'.$r["position"]["height"].'</td>';
        }
        

        echo '<td>';
        if(current_user_can("tv_deleteTables")) {
            echo '<button type="submit" name="tableToDelete" class="edit" id="deleteBtn_'.$r["id"].'" value="'.$r["id"].'" onclick="return confirm(\'Willst du diesen Eintrag wirklich löschen?\');"><i class="fa fa-trash"></i></button>';
        }

        if(current_user_can("tv_editTables")) {
            echo ' <button type="button" id="editBtn_'.$r["id"].'" class="edit" onclick="edit('.$r["id"].')"><i class="fa fa-pencil"></i></button>';
            echo ' <button type="submit"  id="saveBtn_'.$r["id"].'" class="hidden edit" name="tableToEdit" value="'.$r["id"].'"><i class="fa fa-floppy-o"></i></button>';
            echo ' <button type="button"  id="cancelBtn_'.$r["id"].'" onclick="cancelEdit()" class="hidden edit"><i class="fa fa-times"></i></button>';
        }
        echo '</td>';
        

        echo '</tr>';
    } ?>
        </table>
    </form>
</div>
<?php
}
?>