<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../verification.php");



function applyStyle_addReservation() {
    wp_enqueue_style("stylesheet_name", plugins_url("style/form.css", __FILE__));

}


function show_addReservation() {
    $required = array("table", "from", "to", "firstname", "lastname", "mail", "phonenumber");

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
            $table = $_POST["table"];
            $from = strtotime($_POST["from"]);
            $to = strtotime($_POST["to"]);
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $mail = $_POST["mail"];
            $phonenumber = $_POST["phonenumber"];

            $errorMsg = verifyReservation($table, $from, $to, $firstname, $lastname, $mail, $phonenumber);
            
            if($errorMsg === null) {
                addReservation(array($table), $from, $to, $firstname, $lastname, $mail, $phonenumber);
            } else {
                echo '<p class="formError">'.$errorMsg.'</p>';
            }
        } else {
            echo '<p class="formError">Bitte fülle alle Pflichtfelder aus!</p>';
        }
    }
?>
<div id="main">
    <h1>Reservierung hinzufügen</h1>
    <form method="post">
        <div id="formContent">
            <table class="data formData">
                <tr><td>
                    <h2>Reservierungsdaten</h2>
                </td></tr>
                <tr><td>
                    <h3>Beginn der Reservierung</h3>
                    <input type="datetime-local" name="from" />
                </td></tr>
                <tr><td>
                    <h3>Ende der Reservierung</h3>
                    <input type="datetime-local" name="to" />
                </td></tr>
                <tr><td>
                    <h3>Tisch</h3>
                    <select name="table">
                        <?php
                            $allTables = getTables();
                            foreach($allTables as $table) {
                                echo '<option value="'.$table["id"].'">'.$table["title"].'</option>';
                            }
                        ?>
                    </select>
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