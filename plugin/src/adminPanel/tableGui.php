<?php

require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../roomDimensions.php");


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
     * Bearbeiten der Raumabmessungen
     */
    if(isset($_POST["roomToEdit"])) {
        if(isset($_POST["roomWidth"]) && isset($_POST["roomDepth"]) && !empty($_POST["roomWidth"]) && !empty($_POST["roomDepth"])) {
            $errorMsg = storeRoomDimensions($_POST["roomToEdit"], $_POST["roomWidth"], $_POST["roomDepth"]);

            if($errorMsg !== null) {
                echo $errorMsg;
            }
        } else {
            echo "Bitte fülle alle Pflichtfelder aus!";
        }

        $_POST = array();
    }

    /**
     * Löschen eines Tisches
     */
    if (isset($_POST["tableToDelete"])) {
        if(current_user_can("tv_deleteTables")) {
            deleteTable($_POST["tableToDelete"]);

            // POST-Array zurückzusetzen, damit kein neuer Tisch angelegt wird
            $_POST = array();
        } else {
            echo '<p class="error">Du hast nicht die notwendigen Rechte, um diesen Vorgang abzuschließen.</p>';
        }
        
    }



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
 
             if(($tableToEdit == 0 && current_user_can("tv_addTables")) || ($tableToEdit != 0 && current_user_can("tv_editTables"))) {
                $errorMsg = verifyTable($title, $isOutside, $numberOfSeats, $isDisabled, $posX, $posY, $width, $height, $tableToEdit);
                if ($errorMsg === null) {
                    addTable($title, $isOutside, $numberOfSeats, $isDisabled, $posX, $posY, $width, $height, $tableToEdit);
                } else {
                    echo '<p class="error">'.$errorMsg.'</p>';
                }
             } else {
                 echo '<p class="error">Du hast nicht die notwendigen Rechte, um diesen Vorgang abzuschließen.</p>';
             }

         } else {
             echo '<p class="error">Bitte fülle alle Pflichtfelder aus!</p>';
         }
    }

   


?>
<script>
    const allTables = <?php echo json_encode(getTables());?>;
    const width_inside = <?php echo getWidth("inside");?>;
    const depth_inside = <?php echo getDepth("inside");?>;
    const width_outside = <?php echo getWidth("outside");?>;
    const depth_outside = <?php echo getDepth("outside");?>;

</script>

<div id="main">
<form method="post">

        <div id="addTable" class="hidden overlay widget">
            <div class="overlayContent">
            <h1>Tisch hinzufügen</h1>
            <p class="hidden error jsError"></p>
                <div class="flexForm">
                        <div class="formContent">
                        <div class="titleData">
                                <input type="text" name="title" class="title" id="title" placeholder="Bezeichnung des Tisches" <?php if(! (current_user_can("tv_addTables") || current_user_can("tv_editTables"))) echo "disabled";?>/>
                            </div>
                            <table class="data formData">
                                <tr><td>
                                    <h2>Tischdaten</h2>
                                </td></tr>
                                <tr><td>
                                    <h3 class="inline">Anzahl Sitzplätze</h3><span class="required">*</span>
                                    <input type="number" name="numberOfSeats" id="numberOfSeats" <?php if(! (current_user_can("tv_addTables") || current_user_can("tv_editTables"))) echo "disabled";?>/>
                                </td></tr>
                                <tr><td>
                                    <input type="checkbox" name="isDisabled" id="isDisabled" <?php if(! (current_user_can("tv_addTables") || current_user_can("tv_editTables"))) echo "disabled";?>/><label for="isDisabled">Tisch nicht reservierbar</label>
                                </td></tr>
                            </table>   
                        </div>
                        <table id="addTable_publish" class="data publish multiplePublish hidden">
                            <tr><td>
                                <h2>Speichern</h2>
                            </td></tr>
                            <tr><td>
                                <button type="button" onclick="startPositioningOfNewTable()">Positionieren</button>
                                <button type="reset" onclick="closeWidgets()">Abbrechen</button>
                            </td></tr>   
                        </table>
                        <table id="editTable_publish" class="data publish multiplePublish hidden">
                            <tr><td>
                                <h2>Aktionen</h2>
                            </td></tr>
                            <tr><td>
                                <?php if(current_user_can("tv_editTables")) {?>
                                    <button type="submit" name="tableToEdit" id="editTable_saveBtn" onclick="submitNewTable()">Speichern</button>
                                    <button type="button" onclick="startEditPositioning()">Neu positionieren</button>
                                <?php } ?>
                                <?php if(current_user_can("tv_deleteTablesTables")) {?>
                                    <button type="submit" name="tableToDelete" id="editTable_deleteBtn" onclick="return confirm('Willst du diesen Eintrag wirklich löschen?')">Löschen</button>
                                <?php } ?>
                                <button type="reset" onclick="closeWidgets(); mouseUp = undefined; mouseDown = undefined;">Abbrechen</button>
                            </td></tr>  
                        </table>
                </div>          
            </div>
        </div>
    <?php if(current_user_can("tv_editOptions")) { ?>
        <div id="changeRoomDimensions" class="hidden overlay widget">
            <div class="overlayContent">
            <h1>Raumabmessungen ändern</h1>
            <p class="hidden error jsError"></p>
                <div class="flexForm">
                        <div class="formContent">
                            <table class="data formData">
                                <tr><td>
                                    <h2>Abmessungen</h2>
                                </td></tr>
                                <tr><td>
                                    <h3 class="inline">Breite (in m)</h3><span class="required">*</span>
                                    <input type="number" name="roomWidth" id="roomWidth" step="0.01" />
                                </td></tr>
                                <tr><td>
                                    <h3 class="inline">Tiefe (in m)</h3><span class="required">*</span>
                                    <input type="number" name="roomDepth" id="roomDepth" step="0.01" />
                                </td></tr>
                            </table>   
                        </div>
                        <table class="data publish">
                            <tr><td>
                                <h2>Speichern</h2>
                            </td></tr>
                            <tr><td>
                                <button id="saveRoomDimensionsBtn" name="roomToEdit">Speichern</button>
                                <button type="button" onclick="closeWidgets()">Abbrechen</button>
                            </td></tr>   
                        </table>
                </div>          
            </div>
        </div>
    <?php } ?>
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
                    <?php if(current_user_can("tv_addTables")) { ?>
                        <button type="button" onclick="addTable()" id="newTable">Tisch hinzufügen</button>
                    <?php } ?>
                    <?php if(current_user_can("tv_editOptions")) { ?>
                        <button type="button" onclick="changeRoomDimensions()" id="newTable">Raumabmessungen ändern</button>
                    <?php } ?>

                </td>
            </tr>
        </table>
    </div>
    <div id="positioningBarOfNewTable" class="hidden bar">
        <h2>Neuen Tisch positionieren</h2>
        <div class="buttonOverviewNewTable">
            <button type="button" onclick="cancelPositioning()" class="buttonNewTable">Erstellen abbrechen</button>
            <button type="submit" id="submitTableButton" class="buttonNewTable" onclick="submitNewTable()" disabled>Tisch speichern</button>
        </div>
    </div>
    <div id="positioningBarForEdit" class="hidden bar">
        <h2>Tisch neu positionieren</h2>
        <div class="buttonOverviewNewTable">
            <button type="button" class="buttonNewTable" onclick="discardNewPositioning_backToWdiget()">Abbrechen</button>
            <button type="button" class="buttonNewTable" onclick="saveNewPositioning_backToWidget()">Übernehmen</button>
        </div>
    </div>    
    <div class="canvasParent">
        <div class="backgroundImg" id="backgroundImg">
            <canvas id="canvas"></canvas>
        </div>
    </div>
</form>
</div>



<?php
}



?>