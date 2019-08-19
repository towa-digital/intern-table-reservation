<?php

require_once(__DIR__."/../options.php");

function initSettings() {

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