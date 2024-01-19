<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

if ( class_exists( 'BHWorkoutPlugin_DatabaseManager' ) == FALSE ) {
    class BHWorkoutPlugin_DatabaseManager {

        private static function prefix() {
            global $wpdb;
            return $wpdb->prefix . "bhworkout_";
        }

        public static function activation_setup() {
            self::create_tables();
            self::create_stored_procedures();
        }

        private static function create_tables() {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            // Equipment table
            $equipment_table_name = self::prefix() . "equipment";
            
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                ID varchar(36) NOT NULL,
                Name varchar(512) NOT NULL,
                Value decimal(10,3) NOT NULL,
                Units enum('kg') DEFAULT NULL,
                Multiplier decimal(10,2) NOT NULL DEFAULT 1.00,
                PRIMARY KEY  (ID)
              ) $charset_collate;";
            dbDelta($sql);
        }

        private static function create_stored_procedures() {
            global $wpdb;

            // Equipment stored procedures
            $equipment_table_name = self::prefix() . "equipment";
            
            $sql = "DROP PROCEDURE add_equipment;";
            $wpdb->query($sql);
            
            $sql = "CREATE DEFINER=`root`@`localhost` PROCEDURE `add_equipment`(
                IN `_name` VARCHAR(512) CHARSET utf8, 
                IN `_value` DECIMAL(10,3) UNSIGNED, 
                IN `_units` ENUM('kg') CHARSET utf8, 
                IN `_multiplier` DECIMAL(10,2) UNSIGNED, 
                OUT `_id` VARCHAR(36) CHARSET utf8
                ) NOT DETERMINISTIC MODIFIES SQL DATA SQL SECURITY DEFINER 
                BEGIN 
                    SET _id = UUID(); 
                    INSERT INTO $equipment_table_name(ID, Name, Units, Value, Multiplier) values (_id, _name, _value, _units, _multiplier); 
                END";
            $wpdb->query($sql);
        }
    }
}
?>