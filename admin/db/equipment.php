<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

if ( class_exists( 'BHWorkoutPlugin_EquipmentDB' ) == FALSE ) {
    class BHWorkoutPlugin_EquipmentDB {

        public static function table_name() : string {
            return BHWorkoutPlugin_DatabaseManager::prefix() . "equipment";
        }

        public static function get_all_query() : string {
            $table_name = self::table_name();
            return "SELECT * FROM $table_name ORDER BY Name ASC;";
        }

        public static function select_query() : string {
            $table_name = self::table_name();
            return "SELECT * FROM $table_name WHERE ID='%s';";
        }

        public static function delete_query() : string {
            $table_name = self::table_name();
            return "DELETE FROM $table_name WHERE ID='%s'";
        }

        public static function add(BHWorkoutPlugin_Equipment $equipment) {
            global $wpdb;

            $result = $wpdb->query($equipment->db_insert());

            if ($result === FALSE) {
                throw new Exception("Unable to insert equipment.");
            }
        }

        public static function update(BHWorkoutPlugin_Equipment $equipment) {
            global $wpdb;

            $result = $wpdb->query($equipment->db_update());

            if ($result === FALSE) {
                throw new Exception("Unable to update equipment.");
            }
        }

        public static function delete(?string $equipment_id) {
            global $wpdb;

            if (is_null($equipment_id)) {
                throw new Exception("Nothing to delete.");
            }

            $delete_query = self::delete_query();
            
            $result = $wpdb->query($wpdb->prepare($delete_query, array($equipment_id)));
            if (($result === FALSE) || ($result == 0)) {
                throw new Exception("Unable to delete equipment.");
            }
        }

        public static function get(?string $equipment_id) : ?BHWorkoutPlugin_Equipment {
            global $wpdb;

            if (is_null($equipment_id)) {
                throw new Exception("Nothing to get.");
            }

            $select_query = self::select_query();
            
            $result = $wpdb->get_results($wpdb->prepare($select_query, array($equipment_id)));
            if (($result === FALSE) || ($result == 0) || (count($result) == 0)) {
                throw new Exception("Unable to retrieve equipment.");
            }

            return BHWorkoutPlugin_Equipment::from_db_query($result[0]);
        }

        public static function get_all() : ?array {
            global $wpdb;

            $results = $wpdb->get_results(self::get_all_query());
            if (($results === FALSE) || ($results == 0)) {
                throw new Exception("Unable to retrieve equipment.");
            }

            $equipment = array();
            foreach ($results as $result) {
                $equipment[] = BHWorkoutPlugin_Equipment::from_db_query($result);
            }
            return $equipment;
        }

        public static function create_tables() {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            // Equipment table
            $equipment_table_name = self::table_name();
            
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

        public static function create_stored_procedures() {
            global $wpdb;
            $equipment_table_name = self::table_name();

            // Equipment stored procedures
            $sql = "DROP PROCEDURE IF EXISTS add_equipment;";
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

    }
}