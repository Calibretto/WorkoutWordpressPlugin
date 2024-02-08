<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once __DIR__ . "/equipment.php";

if ( class_exists( 'BHWorkoutPlugin_DatabaseManager' ) == FALSE ) {
    class BHWorkoutPlugin_DatabaseManager {

        private static function prefix() : string {
            global $wpdb;
            return $wpdb->prefix . "bhworkout_";
        }

        private static function equipment_table() : string {
            return self::prefix() . "equipment";
        }

        public static function activation_setup() {
            self::create_tables();
            self::create_stored_procedures();
        }

        private static function create_tables() {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            // Equipment table
            $equipment_table_name = self::equipment_table();
            
            $sql = "CREATE TABLE IF NOT EXISTS $equipment_table_name (
                ID varchar(36) NOT NULL,
                Name varchar(512) NOT NULL,
                ValueMin decimal(10,2) DEFAULT NULL,
                ValueMax decimal(10,2) DEFAULT NULL,
                ValueStep decimal(10,2) DEFAULT NULL,
                Units enum('none', 'kg') DEFAULT NULL,
                PRIMARY KEY  (ID)
              ) $charset_collate;";
            dbDelta($sql);
        }

        private static function create_stored_procedures() {
            global $wpdb;
            $equipment_table_name = self::equipment_table();

            // Equipment stored procedures
            $sql = "DROP PROCEDURE add_equipment;";
            $wpdb->query($sql);
            
            $sql = "CREATE DEFINER=`root`@`localhost` PROCEDURE `add_equipment`(
                IN `_name` VARCHAR(512) CHARSET utf8, 
                IN `_value_min` DECIMAL(10,2) UNSIGNED, 
                IN `_value_max` DECIMAL(10,2) UNSIGNED, 
                IN `_value_step` DECIMAL(10,2) UNSIGNED, 
                IN `_units` ENUM('none', 'kg') CHARSET utf8
                ) NOT DETERMINISTIC MODIFIES SQL DATA SQL SECURITY DEFINER 
                BEGIN 
                    INSERT INTO $equipment_table_name(ID, Name, ValueMin, ValueMax, ValueStep, Units) values (UUID(), _name, _value_min, _value_max, _value_step, _units); 
                END";
            $wpdb->query($sql);
        }

        public static function add_equipment(BHWorkoutPlugin_Equipment $equipment) {
            global $wpdb;

            $result = $wpdb->query($equipment->db_insert());

            if ($result === FALSE) {
                throw new Exception("Unable to insert equipment.");
            }
        }

        public static function update_equipment(BHWorkoutPlugin_Equipment $equipment) {
            global $wpdb;
            $equipment_table_name = self::equipment_table();

            $result = $wpdb->query($equipment->db_update($equipment_table_name));

            if ($result === FALSE) {
                throw new Exception("Unable to update equipment.");
            }
        }

        public static function delete_equipment(?string $equipment_id) {
            global $wpdb;
            $equipment_table_name = self::equipment_table();

            if (is_null($equipment_id)) {
                throw new Exception("Nothing to delete.");
            }

            $delete_query = BHWorkoutPlugin_Equipment::delete_query($equipment_table_name);
            
            $result = $wpdb->query($wpdb->prepare($delete_query, array($equipment_id)));
            if (($result === FALSE) || ($result == 0)) {
                throw new Exception("Unable to delete equipment.");
            }
        }

        public static function get_equipment(?string $equipment_id) : ?BHWorkoutPlugin_Equipment {
            global $wpdb;
            $equipment_table_name = self::equipment_table();

            if (is_null($equipment_id)) {
                throw new Exception("Nothing to get.");
            }

            $select_query = BHWorkoutPlugin_Equipment::select_query($equipment_table_name);
            
            $result = $wpdb->get_results($wpdb->prepare($select_query, array($equipment_id)));
            if (($result === FALSE) || ($result == 0) || (count($result) == 0)) {
                throw new Exception("Unable to retrieve equipment.");
            }

            return BHWorkoutPlugin_Equipment::from_db_query($result[0]);
        }

        public static function get_all_equipment() : ?array {
            global $wpdb;
            $equipment_table_name = self::equipment_table();

            $results = $wpdb->get_results(BHWorkoutPlugin_Equipment::get_all_query($equipment_table_name));
            if (($results === FALSE) || ($results == 0) || (count($results) == 0)) {
                throw new Exception("Unable to retrieve equipment.");
            }

            $equipment = array();
            foreach ($results as $result) {
                $equipment[] = BHWorkoutPlugin_Equipment::from_db_query($result);
            }
            return $equipment;
        }
    }
}
?>