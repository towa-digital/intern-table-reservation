<?php
     function registerTableReservationCpts()
     {

         /**
          * Post Type: Tische.
          */

         $labels = [
             "name" => __("Tische", "twentynineteen"),
             "singular_name" => __("Tisch", "twentynineteen"),
         ];

         $args = [
             "label" => __("Tische", "twentynineteen"),
             "labels" => $labels,
             "description" => "Tische für die Tischverwaltung erstellen",
             "public" => true,
             "publicly_queryable" => true,
             "show_ui" => true,
             "delete_with_user" => false,
             "show_in_rest" => true,
             "rest_base" => "tables",
             "rest_controller_class" => "WP_REST_Posts_Controller",
             "has_archive" => false,
             "show_in_menu" => true,
             "show_in_nav_menus" => true,
             "exclude_from_search" => false,
             "capability_type" => "post",
             "map_meta_cap" => true,
             "hierarchical" => false,
             "rewrite" => ["slug" => "tables", "with_front" => true],
             "query_var" => true,
             "supports" => ["title"],
         ];

         register_post_type("tables", $args);

         /**
          * Post Type: Reservierungen.
          */

         $labels = [
             "name" => __("Reservierungen", "twentynineteen"),
             "singular_name" => __("Reservierung", "twentynineteen"),
         ];

         $args = [
             "label" => __("Reservierungen", "twentynineteen"),
             "labels" => $labels,
             "description" => "",
             "public" => true,
             "publicly_queryable" => true,
             "show_ui" => true,
             "delete_with_user" => false,
             "show_in_rest" => true,
             "rest_base" => "reservations",
             "rest_controller_class" => "WP_REST_Posts_Controller",
             "has_archive" => false,
             "show_in_menu" => true,
             "show_in_nav_menus" => true,
             "exclude_from_search" => false,
             "capability_type" => "post",
             "map_meta_cap" => true,
             "hierarchical" => false,
             "rewrite" => ["slug" => "reservations", "with_front" => true],
             "query_var" => true,
             "supports" => ["custom_fields"],
         ];

         /**
          * Post Type: Einstellungen.
          */

         $labels = [
             "name" => __("Optionen", "twentynineteen"),
             "singular_name" => __("Option", "twentynineteen"),
         ];

         $args = [
             "label" => __("Optionen", "twentynineteen"),
             "labels" => $labels,
             "description" => "",
             "public" => true,
             "publicly_queryable" => true,
             "show_ui" => true,
             "delete_with_user" => false,
             "show_in_rest" => true,
             "rest_base" => "options",
             "rest_controller_class" => "WP_REST_Posts_Controller",
             "has_archive" => false,
             "show_in_menu" => true,
             "show_in_nav_menus" => true,
             "exclude_from_search" => false,
             "capability_type" => "post",
             "map_meta_cap" => true,
             "hierarchical" => false,
             "rewrite" => ["slug" => "options", "with_front" => true],
             "query_var" => true,
             "supports" => ["title", "editor"],
         ];


         register_post_type("options", $args);
     }

 add_action('init', 'registerTableReservationCpts');
