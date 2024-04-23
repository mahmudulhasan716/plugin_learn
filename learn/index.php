<?php

/**
 * Plugin Name: WP Learn
 */




register_activation_hook(__FILE__,'crate_table');

function crate_table(){
    global $wpdb;

    $table_name = 'information';

    // $wpdb->query("CREATE table $table_name(
    //      id integer(9) NOT NULL AUTO_INCREMENT,
    //     name varchar(100) NOT NULL,
    //     email varchar(100) NOT NULL,
    //     phone text(12) NuLL
    // )");

   $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id int(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        phone   int(12) NULL,
        description varchar(255) NULL,
        zip_code int(10) NULL,
        link varchar(255) NULL,
        PRIMARY KEY  (id)
       
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


require_once('inc/crud.php');

require_once('short/form.php');
require_once('short/datalist.php');



?>
