<?php
require_once(__DIR__."/../queryDatabase.php");

function applyStyle_tableList() {
    wp_enqueue_style("stylesheet_name", plugins_url("style/main.css", __FILE__));
    wp_enqueue_style("stylesheet_name", plugins_url("style/list.css", __FILE__));
}

function show_tableList() {
    if(isset($_POST["tableToDelete"])) {
        deleteTable($_POST["tableToDelete"]);
    }
?>

<div id="main">
    <h1>Alle Tische</h1>
    <button>Neuen Tisch erstellen</button>
    <form method="post">
        <table class="content">
            <tr>
                <th style="width=50%">Titel</th>
                <th style="width=16%">ist draußen?</th>
                <th style="width=16%">Platzanzahl</th>
            </tr>
            <?php
                $allTables = getTables();
                foreach($allTables as $r) {
                    echo '<tr>';
                    echo '<td>'.$r["title"].'</td>';
                    echo '<td>außen? '.($r["isOutside"] ? "ja" : "nein").'</td>';
                    echo '<td>Sitzplätze: '.$r["seats"].'</td>';
                    echo '<td><button type="submit" name="tableToDelete" value="'.$r["id"].'">Löschen</button></td>';
                    echo '</tr>';
                }

              ?>
        </table>
    </form>
</div>
<?php
}
?>