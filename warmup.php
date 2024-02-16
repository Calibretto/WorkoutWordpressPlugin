<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once plugin_dir_path( __FILE__ ) . "/admin/db/warmups.php";

if ( class_exists( 'BHWorkoutPlugin_Warmup' ) == FALSE ) {
    class BHWorkoutPlugin_Warmup {
    }
}
?>