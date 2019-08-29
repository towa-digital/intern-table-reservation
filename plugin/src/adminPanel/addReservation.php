<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../verification.php");
require_once(__DIR__."/../options.php");



function applyStyle_addReservation()
{
    wp_enqueue_style("form_style", plugins_url("style/form.css", __FILE__));
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
}


function show_addReservation()
{
    //AJAX
    wp_enqueue_script("ajax", "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js");
    wp_enqueue_script("loadAvailableTables_script", plugins_url("script/loadAvailableTables.js", __FILE__));
    wp_enqueue_script("reservationsClientVerification_script", plugins_url("script/reservationsClientVerification.js", __FILE__));


    $required = ["table", "from", "numberOfSeats", "firstname", "lastname", "mail", "phonenumber"];

    $isset = true;
    $error = false;
    foreach ($required as $field) {
        // prüfe, ob mindestens ein Feld nicht gesetzt ist
        if (! isset($_POST[$field])) {
            $isset = false;
            break;
        }

        // Prüfe, ob mindestens ein Feld leer ist
        if (empty($_POST[$field])) {
            $error = true;
            break;
        }
    }

    if ($isset) {
        if (! $empty) {
            $tables = $_POST["table"];
            $from = strtotime($_POST["from"]);
            $useDefaultEndTime = $_POST["useDefaultEndTime"] === null ? false : true;

            if (! $useDefaultEndTime && ! isset($_POST["to"])) {
                echo '<p class="error">Bitte fülle alle Pflichtfelder aus!</p>';
            }

            $to = $useDefaultEndTime ? $from + (getDefaultReservationDuration() * 60) : strtotime($_POST["to"]);
            $numberOfSeats = ($_POST["numberOfSeats"] == "" ? 0 : $_POST["numberOfSeats"]);
            $firstname = ($_POST["firstname"] === null) ? "" : $_POST["firstname"];
            $lastname = ($_POST["lastname"] === null) ? "" : $_POST["lastname"];
            $mail = ($_POST["mail"] === null) ? "" : $_POST["mail"];
            $phonenumber = ($_POST["phonenumber"] === null) ? "" : $_POST["phonenumber"];
            $remarks = ($_POST["remarks"] === null) ? "" : $_POST["remarks"];


            $errorMsg = verifyReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber, $remarks);
            
            if ($errorMsg === null) {
                addReservation($tables, $from, $to, $numberOfSeats, $firstname, $lastname, $mail, $phonenumber, $remarks);
            } else {
                echo '<p class="error">'.$errorMsg.'</p>';
            }
        } else {
            echo '<p class="error">Bitte fülle alle Pflichtfelder aus!</p>';
        }
    } ?>
<script>
    const DEFAULT_RESERVATION_DURATION = <?php echo getDefaultReservationDuration(); ?>;
    const CAN_RESERVATE_IN_MINUTES = <?php echo getCanReservateInMinutes(); ?>;
</script>

<div id="main">
    <p id="jsError" class="hidden error"></p>
    <h1>Reservierung hinzufügen</h1>
    <form method="post" class="flexForm">
        <div class="formContent">
            <table class="data formData">
                <tr><td>
                    <h2>Reservierungsdaten</h2>
                </td></tr>
                <tr><td>
                    <h3 class="inline">Beginn der Reservierung</h3><span class="required">*</span>
                    <input type="datetime-local" name="from" id="from" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}" placeholder="Beginn der Reservierung" oninput="onDateChange(this, document.getElementById('to'), 0)" />
                </td></tr>
                <tr><td>
                    <h3 class="inline">Ende der Reservierung</h3><span class="required">*</span><br />
                    <input type="checkbox" name="useDefaultEndTime" id="useDefaultEndTime" onchange="onDateChange(document.getElementById('from'), document.getElementById('to'), 0); $('#to').prop('disabled', this.checked)" checked />
                    <label for="useDefaultEndTime">Soll die standardmäßige Reservierungsdauer verwendet werden?</label>
                    <input type="datetime-local" name="to" id="to" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}" placeholder="Ende der Reservierung" oninput="onDateChange(document.getElementById('from'), this, 0)" style="margin-top: 15px;margin-bottom: 15px;" disabled />
                </td></tr>
                <tr><td>
                    <h3>Anzahl Plätze</h3>
                    <input type="number" name="numberOfSeats" />
                </td></tr>
                <tr><td>
                    <h3 class="inline">Tisch</h3><span class="required">*</span>
                    <div class="selectTableParent">
                        <select name="table[]" class="selectTable hidden"></select>
                    </div>
                    <button type="button" class="add_selectTable hidden" onclick="addElement()">Hinzufügen</button>
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
                <tr><td>
                    <h3>Anmerkungen</h3>
                    <input type="text" name="remarks" />
                </td></tr>
            </table>   
        </div>
        <table class="data publish">
            <tr><td>
                <h2>Speichern</h2>
            </td></tr>
            <tr><td>
                <button id="publishBtn" disabled>Speichern</button>
            </td></tr>   
        </table> 
    </form>
</div>

<?php
}
?>