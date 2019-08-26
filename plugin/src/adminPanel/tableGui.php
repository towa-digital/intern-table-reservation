<?php

require_once(__DIR__."/../queryDatabase.php");

function applyStyle_tableGui() {
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("form_style", plugins_url("style/form.css", __FILE__));
    wp_enqueue_style("tableGui_style", plugins_url("style/tableGui.css", __FILE__));

}

function show_tableGui() {
    //AJAX
    wp_enqueue_script("ajax", "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js");
    
    // Sortierfuntion
    wp_enqueue_script("tableCanvas_script", plugins_url("script/tableCanvas.js", __FILE__));

    /**
     * Löschen eines Tisches
     */
    if (isset($_POST["tableToDelete"]) && current_user_can("tv_deleteTables")) {
        deleteTable($_POST["tableToDelete"]);

        // POST-Array zurückzusetzen, damit kein neuer Tisch angelegt wird
        $_POST = array();
    }

    /**
     * Bearbeiten eines Tisches
     */
    /*if (current_user_can("tv_editTables") && isset($_POST["tableToEdit"]) && isset($_POST["title"]) && isset($_POST["numberOfSeats"])) {
        if (empty($_POST["tableToEdit"]) || empty($_POST["title"]) || empty($_POST["numberOfSeats"])) {
            echo '<p class="formError">Bitte fülle alle Pflichtfelder aus!</p>';
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
                echo '<p class="formError">'.$errorMsg.'</p>';
            }
        }
    }*/

    /**
     * Erzeugen/Bearbeiten eines Tisches
     */
     $required = ["title", "numberOfSeats", "posX", "posY", "width", "height"];
     $isset = true;
     $empty = false;
     foreach ($required as $field) {
         // prüfe, ob mindestens ein Feld nicht gesetzt ist
         if (! isset($_POST[$field])) {
             $isset = false;
             break;
         }
 
         // Prüfe, ob mindestens ein Feld leer ist
         if (empty($_POST[$field])) {
             $empty = true;
             break;
         }
     }
 
     if ($isset) {
         if (! $empty) {
             $title = $_POST["title"];
             $isOutside = $_POST["isOutside"] === null ? false : true;
             $numberOfSeats = $_POST["numberOfSeats"];
             $isDisabled = $_POST["isDisabled"] === null ? false : true;
             $posX = round($_POST["posX"], 2);
             $posY = round($_POST["posY"], 2);
             $width = round($_POST["width"], 2);
             $height = round($_POST["height"], 2);
             $tableToEdit = isset($_POST["tableToEdit"]) ? $_POST["tableToEdit"] : 0;
 
             $errorMsg = verifyTable($title, $isOutside, $numberOfSeats, $isDisabled, $posX, $posY, $width, $height, $tableToEdit);
             if ($errorMsg === null) {
                 addTable($title, $isOutside, $numberOfSeats, $isDisabled, $posX, $posY, $width, $height, $tableToEdit);
             } else {
                 echo '<p class="formError">'.$errorMsg.'</p>';
             }
         } else {
             echo '<p class="formError">Bitte fülle alle Pflichtfelder aus!</p>';
         }
    }
?>
<script>const allTables = <?php echo json_encode(getTables());?>;</script>

<div id="main">
<form method="post">
    <div id="addTable" class="hidden overlay widget">
    <div class="overlayContent">
    <h1>Tisch hinzufügen</h1>
    <p id="jsError" class="hidden"></p>
        <div class="flexForm">
                <div id="formContent">
                   <div class="titleData">
                        <input type="text" name="title" class="title" id="title" placeholder="Bezeichnung des Tisches" />
                    </div>
                    <table class="data formData">
                        <tr><td>
                            <h2>Tischdaten</h2>
                        </td></tr>
                        <tr><td>
                            <h3 class="inline">Anzahl Sitzplätze</h3><span class="required">*</span>
                            <input type="number" name="numberOfSeats" id="numberOfSeats" />
                        </td></tr>
                        <tr><td>
                            <input type="checkbox" name="isDisabled" id="isDisabled" /><label for="isDisabled">Tisch nicht reservierbar</label>
                        </td></tr>
                    </table>   
                </div>
                <table id="addTable_publish" class="data publish hidden">
                    <tr><td>
                        <h2>Speichern</h2>
                    </td></tr>
                    <tr><td>
                        <button type="button" onclick="startPositioningOfNewTable()">Positionieren</button>
                        <button type="reset" onclick="closeWidgets()">Abbrechen</button>
                    </td></tr>   
                </table>
                <table id="editTable_publish" class="data publish hidden">
                    <tr><td>
                        <h2>Aktionen</h2>
                    </td></tr>
                    <tr><td>
                        <button type="submit" name="tableToEdit" id="editTable_saveBtn" onclick="submitNewTable()">Speichern</button>
                        <button type="button" onclick="startEditPositioning()">Neu positionieren</button>
                        <button type="submit" name="tableToDelete" id="editTable_deleteBtn" onclick="return confirm('Willst du diesen Eintrag wirklich löschen?')">Löschen</button>
                        <button type="reset" onclick="closeWidgets(); mouseUp = undefined; mouseDown = undefined;">Abbrechen</button>
                    </td></tr>  
                </table>
        </div>          
    </div>
    </div>
    <div id="mainBar">

        <h2>
            Graphische Tischverwaltung
        </h2>
        <table class="buttonOverview">
            <tr>
                <td>
                    <select id="insideOutside" class="bar">
                        <option value="inside">Innen</option>
                        <option value="outside" <?php if($_POST["isOutside"] == true) echo "selected"; ?>>Außen</option>
                    </select>   
                </td>
                <td>
                    <button type="button" onclick="addTable()" id="newTable">Tisch hinzufügen</button>
                </td>
            </tr>
        </table>
    </div>
    <div id="positioningBarOfNewTable" class="hidden bar">
        <button type="button" onclick="cancelPositioning()">Erstellen abbrechen</button>
        <button type="submit" id="submitTableButton" onclick="submitNewTable()" disabled>Tisch speichern</button>
    </div>
    <div id="positioningBarForEdit" class="hidden bar">
        <button type="button" onclick="discardNewPositioning_backToWdiget()">Abbrechen</button>
        <button type="button" onclick="saveNewPositioning_backToWidget()">Übernehmen</button>
    </div>
    <div class="canvasParent">
            <canvas id="canvas"></canvas>
        </div>
        </form>
</div>



<?php
}



?>