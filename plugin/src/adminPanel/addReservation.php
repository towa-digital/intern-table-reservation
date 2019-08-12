<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../verification.php");



function applyStyle_addReservation() {
    wp_enqueue_style("stylesheet_name", plugins_url("style/form.css", __FILE__));

}


function show_addReservation() {
    //AJAX
    wp_enqueue_script("ajax", "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js");
    wp_enqueue_script("loadAvailableTables_script", plugins_url("script/loadAvailableTables.js", __FILE__));
    wp_enqueue_script("reservationsClientVerification_script", plugins_url("script/reservationsClientVerification.js", __FILE__));


    $required = array("table", "from", "to", "numberOfSeats", "firstname", "lastname", "mail", "phonenumber");

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
            $tables = $_POST["table"];
            $from = strtotime($_POST["from"]);
            $to = strtotime($_POST["to"]);
            $numberOfSeats = $_POST["numberOfSeats"];
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $mail = $_POST["mail"];
            $phonenumber = $_POST["phonenumber"];

            $errorMsg = verifyReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber);
            
            if($errorMsg === null) {
                addReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber);
            } else {
                echo '<p class="formError">'.$errorMsg.'</p>';
            }
        } else {
            echo '<p class="formError">Bitte fülle alle Pflichtfelder aus!</p>';
        }
    }



?>
<div id="main">
    <p id="jsError" class="hidden"></h1>
    <h1>Reservierung hinzufügen</h1>
    <form method="post">
        <div id="formContent">
            <table class="data formData">
                <tr><td>
                    <h2>Reservierungsdaten</h2>
                </td></tr>
                <tr><td>
                    <h3 class="inline">Beginn der Reservierung</h3><span class="required">*</span>
                    <input type="datetime-local" name="from" id="from" oninput="onDateChange(this, document.getElementById('to'), 0)" />
                </td></tr>
                <tr><td>
                    <h3>Ende der Reservierung</h3><span class="required">*</span>
                    <input type="datetime-local" name="to" id="to" oninput="onDateChange(document.getElementById('from'), this, 0)" />
                </td></tr>
                <tr><td>
                    <h3>Anzahl Plätze</h3>
                    <input type="number" name="numberOfSeats" />
                </td></tr>
                <tr><td>
                    <h3>Tisch</h3>
                    <div class="selectTableParent">
                        <select name="table[]" class="selectTable hidden"></select>
                    </div>
                    <button type="button" class="add_selectTable hidden" onclick="addElement()">Add</button>
                    <p class="selectTableError">Bitte erst Datum wählen!</p>
                </td></tr>
                <tr><td>
                    <h3>Vorname</h3>
                    <input type="text" name="firstname" />
                </td></tr>
                <tr><td>
                    <h3>Nachname</h3>
                    <input type="text" name="lastname" />
                </td></tr>
                <tr><td>
                    <h3>E-Mail</h3>
                    <input type="email" name="mail" />
                </td></tr>
                <tr><td>
                    <h3>Telefonnummer</h3>
                    <input type="tel" name="phonenumber" />
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