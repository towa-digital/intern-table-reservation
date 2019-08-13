<?php

function initSettings() {
    register_setting("tischverwaltung", "defaultReservationDuration", array(
            "sanitize_callback" => "validateInteger",
            "default" => 60,
            "type" => "integer"
        )
    );
    register_setting("tischverwaltung", "maxAmountOfPersons", array(
        "sanitize_callback" => "validateInteger",
        "default" => 5,
        "type" => "integer"
    )
);}

function validateInteger($value) {
    $value = intval($value);

    if($value <= 0) $value = 1;
    return $value;
}

function show_optionsPage() {
?>
<div class="wrap">
    <h1>Tischverwaltung</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'tischverwaltung' ); ?>
        <?php do_settings_sections( 'tischverwaltung' ); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">Standarddauer der Reservierung (in min)</th>
                <td><input type="number" name="defaultReservationDuration" value="<?php echo esc_attr(get_option('defaultReservationDuration')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Maximale Anzahl Plätze pro Reservierung</th>
                <td><input type="number" name="maxAmountOfPersons" value="<?php echo esc_attr(get_option("maxAmountOfPersons")); ?>" /></td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    
    </form>
</div>
<?php } ?>