<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once plugin_dir_path( __FILE__ ) . "/equipment.php";
require_once plugin_dir_path( __FILE__ ) . "/warmup.php";

if ( class_exists( 'BHWorkoutPlugin_DatabaseManager' ) == FALSE ) {
    class BHWorkoutPlugin_DatabaseManager {

        public static function prefix() : string {
            global $wpdb;
            return $wpdb->prefix . "bhworkout_";
        }

        public static function activation_setup() {
            self::create_tables();
            self::create_stored_procedures();
        }

        private static function create_tables() {
            BHWorkoutPlugin_EquipmentDB::create_tables();
            BHWorkoutPlugin_WarmupsDB::create_tables();

            // TODO: Validate database creation
        }

        private static function create_stored_procedures() {
            BHWorkoutPlugin_EquipmentDB::create_stored_procedures();
            BHWorkoutPlugin_WarmupsDB::create_stored_procedures();

            // TODO: Validate procedure creation
        }
    }
}
?>