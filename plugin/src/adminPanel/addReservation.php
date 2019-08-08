<?php
require_once(__DIR__."/../queryDatabase.php");


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

            $errorMsg = null;
            // stelle sicher, dass gülitge Datumsangaben eingegeben wurden
            if($from < time() + (30 * 60) || $from > time() + ((365 / 2) * 24 * 60 * 60)) {
                $errorMsg = "Das Beginndatum darf nicht weniger als 30 Minuten und nicht mehr als ein halbes Jahr in der Zukunft liegen.";
            } else if ($from < time() + (30 * 60) || $from > time() + ((365 / 2) * 24 * 60 * 60)) {
                $errorMsg = "Das Enddatum darf nicht weniger als 30 Minuten und nicht mehr als ein halbes Jahr in der Zukunft liegen.";
            }

            // stelle sicher, dass die Reservierung beginnt, bevor sie aufhört
            else if ($from > $to) {
                $errorMsg = "Das Beginndatum darf nicht vor dem Enddatum liegen.";
            }

            // stelle sicher, dass die Reservierung nicht länger als eine Woche dauert
            else if (($to - $from) > (7 * 24 * 60 * 60)) {
                $errorMsg = "Die Reservierdauer darf nicht größer als eine Woche sein.";
            }

            // stelle sicher, dass die übergebene ID des Tisches tatsächlich ein Tisch ist
            else if (get_post_type($table) != "tables") {
                $errorMsg = "Die ID des übergebenenen Tisch ist keinem Tisch zugeordnet.";
            }

            // stelle sicher, dass Tisch frei ist
            else if (! isTableFree($table)) {
                $errorMsg = "Der gewünschte Tisch ist zum angegebenen Zeitpunkt leider nicht frei.";
            }

            // stelle sicher, dass eine gültige E-Mail-Adresse eingegeben wurde
            else if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMsg = "Die E-Mail-Adresse ist nicht gültig.";
            }

            // stelle sicher, dass eine gültige Telefonnummer eingegeben wurde
            
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