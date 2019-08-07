<?php
/**
 * Plugin Name: Tischverwaltung
 * Plugin URI: 
 * Description: 
 * Version: 1.0
 * Author: 
 * Author URI: 
 */

// Setup von CPT und ACF
require_once("initCpt.php");
require_once("initAcf.php");

add_action("admin_menu", "setup_admin_menu");

function setup_admin_menu() {
    add_menu_page("Tischverwaltung", "Tischverwaltung", "manage_options", "tablev", "admin_menu_init");
    add_submenu_page("tablev", "Tisch erstellen", "Tisch erstellen", "manage_options", "", "create_table");
}

function admin_menu_init() {?>
<h1>Hallo Welt!</h1>

<?php
}

function create_table(){
/*    // bestehende Tische ausgeben
    $query = new WP_Query(array(
        'post_type' => 'table',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ));
    
    
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        echo $post_id." ".get_the_title()." ".get_field("seats");
        echo "<br>";
    }
    
    wp_reset_query();

    // Neuen Tisch einfügen
    $id = wp_insert_post(array(
        'post_title'=>'random3', 
        'post_type'=>'table', 
        'post_content'=>'demo text',
        'post_status'=>'publish'
      ));
    if($id) {
        add_post_meta($id, "outside", 1);
        add_post_meta($id, "seats", 5);
    }
    echo "Successfully inserted!";*/
}
?>