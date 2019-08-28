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
        header('Content-Type: text/plain');
        header('Content-Length: ' . strlen($str));
        header("Content-Disposition: attachment;filename=reservationss.csv ");
        header('Connection: close');
        
        echo $str;
    }
}


function applyStyle_export()
{
    wp_enqueue_style("main_style", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("export_style", plugins_url("style/export.css", __FILE__));

}

function show_export()
{
?>

<h1>Reservierungsliste als CSV exportieren</h1>
<form method="post">
    <input name="isClicked" type="submit" class="btn" value="neue Liste erstellen"  />
</form>


<?php
}
?>