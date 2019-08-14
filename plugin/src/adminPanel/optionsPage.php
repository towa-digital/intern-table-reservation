<?php

function initSettings() {
    register_setting("tischverwaltung", "defaultReservationDuration", array(
            "sanitize_callback" => function($value) {
                return validateInteger($value, "defaultReservationDuration");
            },
            "default" => 60,
            "type" => "integer"
        )
    );
    register_setting("tischverwaltung", "maxAmountOfPersons", array(
            "sanitize_callback" => function($value) {
                return validateInteger($value, "maxAmountOfPersons", 1);
            },
            "default" => 5,
            "type" => "integer"
        )       
    );
    register_setting("tischverwaltung", "maxUnusedSeatsPerReservation", array(
            "sanitize_callback" => function($value) {
                return validateInteger($value, "maxUnusedSeatsPerReservation");
            },
            "default" => 2,
            "type" => "integer"
        )
    );
    /**
     * Hiermit ist einstellbar, dass der Benutzer nur eine 
     */
    register_setting("tischverwaltung", "canReservateInMinutes", array(
            "sanitize_callback" => function($value) {
                return validateInteger($value, "canReservateInMinutes");
            },
            "default" => 30,
            "type" => "integer"
        )
    );
}

function validateInteger($value, $slug, $minValue = 0) {
    $value = intval($value);

    if($value < $minValue) {
        add_settings_error($slug, "lowerThanOne", "$slug darf nicht kleiner als $minValue sein");
        return get_option($slug);
    }

    return $value;
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

?>
<div id="main">
    <h1>Tischverwaltung</h1>
    <form method="post" action="options.php">
        <div id="formContent">
            <?php settings_fields( 'tischverwaltung' ); ?>
            <?php do_settings_sections( 'tischverwaltung' ); ?>

            <table class="data formData">
                <tr><td>
                    <h2>Allgemeine Optionen</h2>
                </td></tr>
                <tr><td>
                    <h3 class="inline">Dauer einer Reservierung in Minuten</h3>
                    <input type="number" name="defaultReservationDuration" value="<?php echo esc_attr(get_option('defaultReservationDuration')); ?>" />                
                </td></tr>
                <tr><td>
                    <h3 class="inline">Anzahl Plätze, die pro Reservierung maximal gebucht werden können</h3>
                    <input type="number" name="maxAmountOfPersons" value="<?php echo esc_attr(get_option('maxAmountOfPersons')); ?>" />  
                </td></tr>
                <tr><td>
                    <h3 class="inline">Anzahl Plätze, die pro Reservierung maximal ungenutzt bleiben dürfen (damit nicht eine einzelne Person Tisch für 10 Personen bucht)</h3>
                    <input type="number" name="maxUnusedSeatsPerReservation" value="<?php echo esc_attr(get_option('maxUnusedSeatsPerReservation')); ?>" />                
                </td></tr>
                <tr><td>
                    <h3 class="inline">Dauer in Minuten, die zwischen Reservierung und Beginnzeit der Reservierung mindestens liegen muss</h3>
                    <input type="number" name="canReservateInMinutes" value="<?php echo esc_attr(get_option('canReservateInMinutes')); ?>" />                
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

     <!--   <h2>Öffnungszeiten</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Montag</th>
                <td>
                    <div id="timePickerParent_0"> 
                        <div>
                            <input type="time" name="openingHours[0][0][from]">
                            <span>-</span>
                            <input type="time" name="openingHours[0][0][to]">
                        </div>
                    </div>
                    <button type="button" onclick="addTimePicker(0)">+</button>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Dienstag</th>
            </tr>
            <tr valign="top">
                <th scope="row">Mittwoch</th>
            </tr>
            <tr valign="top">
                <th scope="row">Donnerstag</th>
            </tr>
            <tr valign="top">
                <th scope="row">Freitag</th>
            </tr>
            <tr valign="top">
                <th scope="row">Samstag</th>
            </tr>
            <tr valign="top">
                <th scope="row">Sonntag</th>
            </tr>-->
            
    </form>
</div>
<?php } ?>