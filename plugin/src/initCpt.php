<?php
     function registerTableReservationCpts() {

         /**
          * Post Type: Tische.
          */

         $labels = array(
             "name" => __( "Tische", "twentynineteen" ),
             "singular_name" => __( "Tisch", "twentynineteen" ),
         );

         $args = array(
             "label" => __( "Tische", "twentynineteen" ),
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
             "rewrite" => array( "slug" => "tables", "with_front" => true ),
             "query_var" => true,
             "supports" => array( "title" ),
         );

         register_post_type( "tables", $args );

         /**
          * Post Type: Reservierungen.
          */

         $labels = array(
             "name" => __( "Reservierungen", "twentynineteen" ),
             "singular_name" => __( "Reservierung", "twentynineteen" ),
         );

         $args = array(
             "label" => __( "Reservierungen", "twentynineteen" ),
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
             "rewrite" => array( "slug" => "reservations", "with_front" => true ),
             "query_var" => true,
             "supports" => array("custom_fields"),
         );

         /**
          * Post Type: Einstellungen.
          */

          $labels = array(
            "name" => __( "Optionen", "twentynineteen" ),
            "singular_name" => __( "Option", "twentynineteen" ),
            );

            $args = array(
                "label" => __( "Optionen", "twentynineteen" ),
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
                "rewrite" => array( "slug" => "options", "with_front" => true ),
                "query_var" => true,
                "supports" => array("title", "editor"),
            );


         register_post_type( "options", $args );
 }

 add_action('init', 'registerTableReservationCpts');


?>