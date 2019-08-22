<?php
require_once(__DIR__."/../queryDatabase.php");
require_once(__DIR__."/../csv.php");

if($_GET["page"] == "exportcsv" && isset($_POST["isClicked"])) {
    add_action('init', 'downloadCsv');
    function downloadCsv() {
        $str = getCsvAsString();

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
        header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
        header('Content-Length: ' . strlen($str));
        header("Content-Disposition: attachment;filename=reservations.csv ");
        header('Connection: close');
        
        echo $str;
    }
}


function applyStyle_exportCSV()
{
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("export_style", plugins_url("style/export.css", __FILE__));

}

function show_exportCSV()
{
?>

<h1>Reservierungsliste als CSV exportieren</h1>
<input name="isClicked" type="submit" class="btn" value="neue Liste erstellen"  />



<?php
}
?>