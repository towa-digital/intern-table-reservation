<?php

require_once(__DIR__."/../options.php");

function initSettings() {
    register_setting("tischverwaltung", "defaultReservationDuration", array(
            "sanitize_callback" => function($value) {
                return validateInteger($value, "defaultReservationDuration");
            },
            "type" => "integer"
        )
    );
    add_option("defaultReservationDuration", 60);

    register_setting("tischverwaltung", "maxAmountOfPersons", array(
            "sanitize_callback" => function($value) {
                return validateInteger($value, "maxAmountOfPersons", 1);
            },
            "type" => "integer"
        )       
    ); 
    add_option("maxAmountOfPersons", 5);

    register_setting("tischverwaltung", "maxUnusedSeatsPerReservation", array(
            "sanitize_callback" => function($value) {
                return validateInteger($value, "maxUnusedSeatsPerReservation");
            },
            "type" => "integer"
        )
    );
    add_option("maxUnusedSeatsPerReservation", 2);

    /**
     * Hiermit ist einstellbar, dass der Benutzer nur eine 
     */
    register_setting("tischverwaltung", "canReservateInMinutes", array(
            "sanitize_callback" => function($value) {
                return validateInteger($value, "canReservateInMinutes");
            },
            "type" => "integer"
        )
    );
    add_option("canReservateInMinutes", 30);

    register_setting("tischverwaltung", "tooManyPersonsError", array(
            "default" => ""
    ));

    register_setting("tischverwaltung", "openingHours", array(
            "default" => genDefaultOpeningHours(),
            "sanitize_callback" => "sanitizeOpeningHours",

    ));
    add_option("openingHours", genDefaultOpeningHours());
    error_log("update done");
}

function genDefaultOpeningHours() {
    $toReturn = array();
    for($c = 0; $c < 7; $c++) {
        $toReturn[] = array(
            array(
                "from" => 11 * 60 * 60,
                "to" => 13 * 60 * 60
            )
        );
    }

    return $toReturn;
}

function validateInteger($value, $slug, $minValue = 0) {
    $value = intval($value);

    if($value < $minValue) {
        add_settings_error($slug, "lowerThanOne", "$slug darf nicht kleiner als $minValue sein");
        return get_option($slug);
    }

    return $value;
}

function sanitizeOpeningHours($value) {
    error_log("Sanitize");
    error_log(print_r($value, true));

    foreach($value as $dayKey => $day) {
        foreach($day as $elemKey => $elem) {
            $from = $elem["from"];
            $to = $elem["to"];

            if(intval($from) > 0 && intval($to) > 0) continue;

            $fromSplit = explode(":", $from);
            $toSplit = explode(":", $to);

            if(intval($fromSplit[0]) != $fromSplit[0] || intval($fromSplit[1]) != $fromSplit[1] || 
                intval($toSplit[0]) != $toSplit[0] || intval($toSplit[1]) != $toSplit[1] ||
                count($fromSplit) != 2 || count($toSplit) != 2) {
                    add_settings_error("openingHours", "invalidTimes", "Bitte gib die Öffnungszeiten im Fomat HH:mm");
                    return get_option("openingHours");
            }

            $value[$dayKey][$elemKey]["from"] = intval($fromSplit[0]) * 60 + intval($fromSplit[1]);
            $value[$dayKey][$elemKey]["to"] = intval($toSplit[0] * 60 + $toSplit[1]);
        }
    }

    error_log(print_r($value, true));

    return $value;
}

function secondsToValueString($seconds) {
    $h = floor($seconds / (60 * 60));
    $m = floor(($seconds / 60) - ($h * 60));

    return str_pad($h, 2, '0', STR_PAD_LEFT).":".str_pad($m, 2, '0', STR_PAD_LEFT);
}


function applyStyle_optionsPage() {
    wp_enqueue_style("form_style", plugins_url("style/form.css", __FILE__));
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
}

function show_optionsPage() {
    //AJAX
    wp_enqueue_script("ajax", "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js");

    // Script für Öffnungszeiten
    wp_enqueue_script("openingHours_script", plugins_url("script/openingHours.js", __FILE__));

    $required = array("defaultReservationDuration", "maxAmountOfPersons", "maxUnusedSeatsPerReservation", "canReservateInMinutes");
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
            echo storeOptions($_POST["defaultReservationDuration"], $_POST["maxAmountOfPersons"], $_POST["maxUnusedSeatsPerReservation"], $_POST["canReservateInMinutes"], $_POST["tooManyPersonsError"],$_POST["noFreeTablesError"],  $_POST["openingHours"]);
        } else {
            echo '<p class="formError">Bitte fülle alle Pflichtfelder aus!</p>';
        }
    }
    
?>
<div id="main">
    <h1>Tischverwaltung</h1>
    <form method="post">
        <div id="formContent">

            <table class="data formData">
                <tr><td>
                    <h2>Allgemeine Optionen</h2>
                </td></tr>
                <tr><td>
                    <h3 class="inline">Dauer einer Reservierung in Minuten</h3>
                    <input type="number" name="defaultReservationDuration" value="<?php echo getDefaultReservationDuration(); ?>" />                
                </td></tr>
                <tr><td>
                    <h3 class="inline">Anzahl Plätze, die pro Reservierung maximal gebucht werden können</h3>
                    <input type="number" name="maxAmountOfPersons" value="<?php echo getMaxAmountOfPersons(); ?>" />  
                </td></tr>
                <tr><td>
                    <h3 class="inline">Anzahl Plätze, die pro Reservierung maximal ungenutzt bleiben dürfen (damit nicht eine einzelne Person Tisch für 10 Personen bucht)</h3>
                    <input type="number" name="maxUnusedSeatsPerReservation" value="<?php echo getMaxUnusedSeatsPerReservation(); ?>" />                
                </td></tr>
                <tr><td>
                    <h3 class="inline">Dauer in Minuten, die zwischen Reservierung und Beginnzeit der Reservierung mindestens liegen muss</h3>
                    <input type="number" name="canReservateInMinutes" value="<?php echo getCanReservateInMinutes(); ?>" />                
                </td></tr>
            </table>
            <table class="data formData">
                <tr><td>
                    <h2>Fehlermeldungen</h2>
                </td></tr>               
                <tr><td>
                    <h3 class="inline">Fehlermeldung bei zu vielen Personen</h3>
                    <input type="text" name="tooManyPersonsError" value="<?php echo getTooManyPersonsError(); ?>" />                
                </td></tr>
                <tr><td>
                    <h3 class="inline">Fehlermeldung, wenn keine freien Tische verfügbar sind</h3>
                    <input type="text" name="noFreeTablesError" value="<?php echo getNoFreeTablesError(); ?>" />                
                </td></tr>
            </table>
            <table id="openingHours" class="data formData">
                <tr><td>
                    <h2>Öffnungszeiten</h2>
                </td></tr>
                <?php

                    $weekDays = array("Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag");
                    
                    $openingHoursArr = getOpeningHours();

                    for($key = 0; $key < 7; $key++) {
                        $day = $openingHoursArr[$key];
                        
                        
                        echo '<tr><td>';
                        echo '<h3 class="inline">'.$weekDays[$key].'</h3>';
                        echo '<div id="timePickerParent_'.$key.'">';
                        foreach($day as $entryKey => $entry) {
                            echo '<div>';
                            echo "<input type='time' name='openingHours[$key][$entryKey][from]' value='".secondsToValueString($entry["from"])."'>";
                            echo '<span>-</span>';
                            echo "<input type='time' name='openingHours[$key][$entryKey][to]' value='".secondsToValueString($entry["to"])."'>";
                            echo '<button type="button" onclick="removeTimePicker(this)">Remove</button>';
                            echo '</div>';
                        }
                        echo "</div><button type='button' onclick='addTimePicker($key)'>Add</button></td></tr>";
                    }

                ?>
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
<?php } ?>