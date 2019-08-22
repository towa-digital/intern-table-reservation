<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../csv.php");



function applyStyle_exportCSV()
{
    wp_enqueue_style("export_style", plugins_url("style/export.css", __FILE__));
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
}


function show_exportCSV()
{
    if (isset($_POST["isClicked"])) {
        exportCSV();
    }

    echo __DIR__ .'/../csv/daten.csv'; ?>

<table>
    <tr><td>
        <h3 class="inline">Exportieren Sie Ihre CSV</h3>                     
    </td></tr>
    <tr><td>
        <form method="post">
            <input name="isClicked" type="submit" class="btn" value="neue Liste erstellen"  />
        </form>
    </td></tr>
    <tr><td>
        <h4><a href="<?php echo __DIR__ .'\..\csv\daten.csv' ?>" download="Reservationsliste">Ihr Downloadlink</a></h4>
    </td></tr>
</table>

<?php
}
?>