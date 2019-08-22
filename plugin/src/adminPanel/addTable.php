<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../verification.php");



function applyStyle_addTable()
{
    wp_enqueue_style("form_style", plugins_url("style/form.css", __FILE__));
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
}

function show_addTable()
{
    $required = ["title", "numberOfSeats"];

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

            $errorMsg = verifyTable($title, $isOutside, $numberOfSeats, $isDisabled);
            if ($errorMsg === null) {
                addTable($title, $isOutside, $numberOfSeats, $isDisabled);
            } else {
                echo '<p class="formError">'.$errorMsg.'</p>';
            }
        } else {
            echo '<p class="formError">Bitte fülle alle Pflichtfelder aus!</p>';
        }
    } ?>
<div id="main">
    <h1>Tisch hinzufügen</h1>
    <form method="post">
        <div id="formContent">
            <div class="titleData">
                <input type="text" name="title" class="title" placeholder="Bezeichnung des Tisches" />
            </div>
            <table class="data formData">
                <tr><td>
                    <h2>Tischdaten</h2>
                </td></tr>
                <tr><td>
                    <input type="checkbox" name="isOutside" id="isOutside" /><label for="isOutside">Ist der Tisch im Außenbereich?</label>
                </td></tr>
                <tr><td>
                    <h3 class="inline">Anzahl Sitzplätze</h3><span class="required">*</span>
                    <input type="number" name="numberOfSeats" />
                </td></tr>
                <tr><td>
                    <input type="checkbox" name="isDisabled" id="isDisabled" /><label for="isDisabled">Tisch nicht reservierbar</label>
                </td></tr>
            </table>   
        </div>
        <table class="data" id="publish">
            <tr><td>
                <h2>Speichern</h2>
            </td></tr>
            <tr><td>
                <button>Speichern</button>
            </td></tr>   
        </table> 
    </form>
</div>


<?php
}


?>