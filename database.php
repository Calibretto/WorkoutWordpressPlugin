<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once __DIR__ . "/equipment.php";

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
        }

        private static function create_stored_procedures() {
            BHWorkoutPlugin_EquipmentDB::create_stored_procedures();
        }
    }
}
?>