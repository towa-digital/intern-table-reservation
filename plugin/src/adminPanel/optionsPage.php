<?php

require_once(__DIR__."/../options.php");

function initSettings()
{
}

function genDefaultOpeningHours()
{
    $toReturn = [];
    for ($c = 0; $c < 7; $c++) {
        $toReturn[] = [
            [
                "from" => 11 * 60 * 60,
                "to" => 13 * 60 * 60,
            ],
        ];
    }

    return $toReturn;
}


function applyStyle_optionsPage()
{
    wp_enqueue_style("form_style", plugins_url("style/form.css", __FILE__));
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("options_stype", plugins_url("style/options.css", __FILE__));
}

function show_optionsPage()
{
    //AJAX
    wp_enqueue_script("ajax", "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js");

    // Script für Öffnungszeiten
    wp_enqueue_script("openingHours_script", plugins_url("script/openingHours.js", __FILE__));

    //FontAwesome
    wp_enqueue_script("fontawesome", "https://kit.fontawesome.com/2c9e55113a.js");

    $required = ["defaultReservationDuration", "maxAmountOfPersons", "maxUnusedSeatsPerReservation", "userConfirmationMail", "adminConfirmationMail", "canReservateInMinutes"];
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
            $errorMsg = storeOptions($_POST["defaultReservationDuration"], $_POST["maxAmountOfPersons"], $_POST["maxUnusedSeatsPerReservation"], $_POST["canReservateInMinutes"], $_POST["tooManyPersonsError"], $_POST["noFreeTablesError"], $_POST["userConfirmationMail"], $_POST["adminConfirmationMail"], $_POST["adminAddress"], $_POST["openingHours"], $_POST["holidays"]);
            if($errorMsg != null) {
                echo '<p class="error">'.$errorMsg.'</p>';
            }
        } else {
            echo '<p class="error">Bitte fülle alle Pflichtfelder aus!</p>';
        }
    } ?>
<div id="main">
    <h1>Tischverwaltung</h1>
    <form method="post" class="flexForm">
        <div class="formContent">

            <table class="data formData">
                <tr><td>
                    <h2>Allgemeine Optionen</h2>
                </td></tr>
                <tr><td>
                    <h3 class="inline">Dauer einer Reservierung in Minuten</h3>
                    <input type="number" name="defaultReservationDuration" value="<?php echo esc_attr(getDefaultReservationDuration()); ?>" />                
                </td></tr>
                <tr><td>
                    <h3 class="inline">Anzahl Plätze, die pro Reservierung maximal gebucht werden können</h3>
                    <input type="number" name="maxAmountOfPersons" value="<?php echo esc_attr(getMaxAmountOfPersons()); ?>" />  
                </td></tr>
                <tr><td>
                    <h3 class="inline">Anzahl Plätze, die pro Reservierung maximal ungenutzt bleiben dürfen (damit nicht eine einzelne Person Tisch für 10 Personen bucht)</h3>
                    <input type="number" name="maxUnusedSeatsPerReservation" value="<?php echo esc_attr(getMaxUnusedSeatsPerReservation()); ?>" />                
                </td></tr>
                <tr><td>
                    <h3 class="inline">Dauer in Minuten, die zwischen Reservierung und Beginnzeit der Reservierung mindestens liegen muss</h3>
                    <input type="number" name="canReservateInMinutes" value="<?php echo esc_attr(getCanReservateInMinutes()); ?>" />                
                </td></tr>
            </table>
            <table class="data formData">
                <tr><td>
                    <h2>Fehlermeldungen</h2>
                </td></tr>               
                <tr><td>
                    <h3 class="inline">Fehlermeldung bei zu vielen Personen</h3>
                    <textarea name="tooManyPersonsError" rows="2"><?php echo esc_attr(getTooManyPersonsError()); ?></textarea>                
                </td></tr>
                <tr><td>
                    <h3 class="inline">Fehlermeldung, wenn keine freien Tische verfügbar sind</h3>
                    <textarea name="noFreeTablesError" rows="2"><?php echo esc_attr(getNoFreeTablesError()); ?></textarea>             
                </td></tr>
            </table>
            <table class="data formData">
                <tr><td>
                    <h2>E-Mail-Einstellungen</h2>
                </td></tr>               
                <tr><td>
                    <h3 class="inline">Bestätigungs-Mail an den Nutzer</h3>
                    <p>Du kannst die Variablen $FIRSTNAME, $LASTNAME, $TABLES, $BEGIN und $NUMBEROFSEATS verwenden.</p>
                    <textarea name="userConfirmationMail" rows="10"><?php echo esc_attr(getUserConfirmationMail()); ?></textarea>                
                </td></tr>
                <tr><td>
                    <h3 class="inline">Bestätigungs-Mail an den Admin</h3>
                    <p>Du kannst die Variablen $FIRSTNAME, $LASTNAME, $TABLES, $BEGIN und $NUMBEROFSEATS verwenden.</p>
                    <textarea name="adminConfirmationMail" rows="10"><?php echo esc_attr(getAdminConfirmationMail()); ?></textarea>                
                </td></tr>
                <tr><td>
                    <h3 class="inline">E-Mail, an die die Admin-Bestätigungsmail gesendet wird</h3>
                    <p>wenn dieses Feld leer ist, wird keine Bestätigungsmail gesendet</p>
                    <input type="mail" name="adminAddress" value="<?php echo esc_attr(getAdminAddress()); ?>" />                
                </td></tr>
            </table>
            <table id="openingHours" class="data formData">
                <tr><td>
                    <h2>Öffnungszeiten</h2>
                </td></tr>
                <?php

                    $weekDays = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag"];
                    
    $openingHoursArr = getOpeningHours();


    for ($key = 0; $key < 7; $key++) {
        $day = $openingHoursArr[$key];
                                                
        echo '<tr><td>';
        echo '<h3>'.$weekDays[$key].'</h3>';
        echo '<div class="timePickerParent" id="timePickerParent_'.$key.'">';
        foreach ($day as $entryKey => $entry) {
            echo '<div>';
            echo "<input type='time' name='openingHours[$key][$entryKey][from]' value='".secondsToValueString($entry["from"])."'>";
            echo '<span> - </span>';
            echo "<input type='time' name='openingHours[$key][$entryKey][to]' value='".secondsToValueString($entry["to"])."'>";
            echo '<button type="button" class="edit" onclick="removeTimePicker(this)"><i class="fas fa-minus"></i></button>';
            echo '</div>';
        }
        echo '</div>';
        echo "<button type='button' class='edit addBtn' onclick='addTimePicker($key)'><i class='fa fa-plus'></i></button></td></tr>";
    } ?>
            </table>
            <table id="holidays" class="data formData">
                <tr><td>
                    <h2>Urlaube</h2>
                </td></tr>
                <tr><td>
                    <div id="holidayParent">
                    <?php
                        $holidays = getHolidays();

    foreach ($holidays as $slotKey => $slot) {
        echo "<div class='timePickerParent' id='holidayPicker_$slotKey'>";
        echo "<input type='date' name='holidays[$slotKey][from]' value='".date("Y-m-d", $slot["from"])."' />";
        echo '<span> - </span>';
        echo "<input type='date' name='holidays[$slotKey][to]' value='".date("Y-m-d", $slot["to"])."' />";
        echo '<button type="button" class="edit" onclick="removeTimePicker(this)"><i class="fas fa-minus"></i></button>';
        echo "</div>";
    } ?>
                    </div>
                </td></tr>
                <tr><td>
                    <button type='button' class='edit addBtn' onclick='addHolidayPicker()'><i class='fa fa-plus'></i></button>
                </td></tr>
            </table>
        </div>
        <table class="data publish">
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
} ?>