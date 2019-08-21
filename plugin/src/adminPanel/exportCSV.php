<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../csv.php");



function applyStyle_exportCSV() {
    wp_enqueue_style("export_style", plugins_url("style/export.css", __FILE__));
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));

}


function show_exportCSV() {
    if(isset($_POST["isClicked"])){
        exportCSV();
    }


?>

<table>
    <tr><td>
        <h3 class="inline">Exportieren Sie Ihre CSV</h3>                     
    </td></tr>
    <tr><td>
        <form method="post">
            <input name="isClicked" type="submit" class="btn" value="Exportieren"  />
        </form>
    </td></tr>
</table>

<?php
}
?>