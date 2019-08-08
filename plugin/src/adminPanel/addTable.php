<?php
require_once(__DIR__."/../queryDatabase.php");


function applyStyle_addTable() {
    wp_enqueue_style("stylesheet_name", plugins_url("style/form.css", __FILE__));
}

function show_addTable() {
    $required = array("title", "numberOfSeats", "isOutside");

    $isset = true;
    $empty = false;

    foreach($required as $field) {
        // prüfe, ob mindestens ein Feld nicht gesetzt ist
        if(! isset($_POST[$field])) {
            $isset = false;
            break;
        }

        // Prüfe, ob mindestens ein Feld leer ist
        if(empty($_POST[$field])) {
            $empty = true;
            break;
        }
    }

    if($isset) {
        if(! $empty) {
            $title = $_POST["title"];
            $isOutside = $_POST["isOutside"];
            $numberOfSeats = $_POST["numberOfSeats"];

            // stelle sicher, dass title noch nicht existiert

            // stelle sicher, dass isOutside ein boolescher Wert ist

            // stelle sicher, dass numberOfSeats eine Zahl ist

            addTable($title, $isOutside, $numberOfSeats);
        } else {
            echo '<p class="formError">Bitte fülle alle Pflichtfelder aus!</p>';
        }
    }
    
?>
<div id="main">
    <h1>Tisch hinzufügen</h1>
    <form method="post">
        <div id="formContent">
            <div class="titleData">
                <input type="text" name="title" class="title" />
            </div>
            <table class="data formData">
                <tr><td>
                    <h2>Tischdaten</h2>
                </td></tr>
                <tr><td>
                    <input type="checkbox" name="isOutside" id="isOutside" /><label for="isOutside">Ist der Tisch im Außenbereich?</label>
                </td></tr>
                <tr><td>
                    <h3>Anzahl Sitzplätze</h3>
                    <input type="number" name="numberOfSeats" />
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