<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

if ( class_exists( 'BHWorkoutPlugin_WarmupsDB' ) == FALSE ) {
    class BHWorkoutPlugin_WarmupsDB {

        public static function table_name() : string {
            return BHWorkoutPlugin_DatabaseManager::prefix() . "warmups";
        }

        public static function equipment_table_name() : string {
            return BHWorkoutPlugin_DatabaseManager::prefix() . "warmup_equipment";
        }

        public static function get_all_query() : string {
            $table_name = self::table_name();
            return "SELECT * FROM $table_name ORDER BY Name ASC;";
        }

        public static function get_all_equipment_query() : string {
            $table_name = self::equipment_table_name();
            $equipment_table_name = BHWorkoutPlugin_EquipmentDB::table_name();
            return "SELECT e.* FROM $equipment_table_name as e INNER JOIN $table_name as w WHERE e.ID = w.EquipmentID AND w.WarmupID = '%s';";
        }

        public static function select_query() : string {
            $table_name = self::table_name();
            return "SELECT * FROM $table_name WHERE ID='%s';";
        }

        public static function delete_query() : string {
            return "CALL delete_warmup('%s');";
        }

        public static function add(BHWorkoutPlugin_Warmup $warmup) {
            global $wpdb;

            $result = $wpdb->get_results($warmup->db_insert());
            if (isset($result[0]->uuid)) {
                $warmup->id = $result[0]->uuid;
                $queries = $warmup->db_insert_equipment();
                foreach($queries as $query) {
                    $wpdb->query($query);
                }
            }

            if ($result === FALSE) {
                throw new Exception("Unable to insert warmup.");
            }
        }

        public static function delete(?string $warmup_id) {
            global $wpdb;

            if (is_null($warmup_id)) {
                throw new Exception("Nothing to delete.");
            }

            $delete_query = self::delete_query();
            
            $result = $wpdb->query($wpdb->prepare($delete_query, array($warmup_id)));
            if ($result === FALSE) {
                throw new Exception("Unable to delete warmup.");
            }
        }

        public static function update(BHWorkoutPlugin_Warmup $warmup) {
            global $wpdb;

            $queries = $warmup->db_update();
            $result = TRUE;
            foreach($queries as $query) {
                $result &= $wpdb->query($query);
            }
            
            if ($result === FALSE) {
                // TODO: Use transactions to undo if it fails.
                throw new Exception("Unable to update equipment.");
            }
        }

        public static function get_all() : ?array {
            global $wpdb;

            $results = $wpdb->get_results(self::get_all_query());
            if (($results === FALSE) || ($results == 0)) {
                throw new Exception("Unable to retrieve equipment.");
            }

            $warmups = array();
            foreach ($results as $result) {
                $warmup = BHWorkoutPlugin_Warmup::from_db_query($result);
                $warmup->equipment = self::get_all_warmup_equipment($warmup);

                $warmups[] = $warmup;
            }
            return $warmups;
        }

        public static function get(?string $warmup_id) : ?BHWorkoutPlugin_Warmup {
            global $wpdb;

            if (is_null($warmup_id)) {
                throw new Exception("Nothing to get.");
            }

            $select_query = self::select_query();
            
            $result = $wpdb->get_results($wpdb->prepare($select_query, array($warmup_id)));
            if (($result === FALSE) || ($result == 0) || (count($result) == 0)) {
                throw new Exception("Unable to retrieve warmup.");
            }

            $warmup = BHWorkoutPlugin_Warmup::from_db_query($result[0]);
            $warmup->equipment = self::get_all_warmup_equipment($warmup);

            return $warmup;
        }

        private static function get_all_warmup_equipment(BHWorkoutPlugin_Warmup $warmup) : ?array {
            global $wpdb;

            $sql = self::get_all_equipment_query();
            $results = $wpdb->get_results($wpdb->prepare($sql, [$warmup->id]));
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

            // Warmups table
            $warmups_table_name = self::table_name();

            $sql = "CREATE TABLE IF NOT EXISTS $warmups_table_name (
                ID VARCHAR(36) NOT NULL,
                Name VARCHAR(512) NOT NULL,
                Description TEXT NULL,
                PRIMARY KEY (ID)
                ) $charset_collate;";
            dbDelta($sql);

            // Warmup Equipment table
            $warmup_equipment_table_name = self::equipment_table_name();

            $sql = "CREATE TABLE IF NOT EXISTS $warmup_equipment_table_name (
                WarmupID VARCHAR(36) NOT NULL,
                EquipmentID VARCHAR(36) NOT NULL
                ) $charset_collate;";
            dbDelta($sql);
        }

        public static function create_stored_procedures() {
            global $wpdb;
            $warmups_table_name = self::table_name();
            $warmups_equipment_table_name = self::equipment_table_name();

            // Warmup stored procedures
            $sql = "DROP FUNCTION IF EXISTS add_warmup;";
            $wpdb->query($sql);
            
            $sql = "CREATE DEFINER=`root`@`localhost` FUNCTION `add_warmup`(
                `_name` VARCHAR(512) CHARSET utf8, 
                `_description` TEXT
                ) RETURNS VARCHAR(36) NOT DETERMINISTIC MODIFIES SQL DATA SQL SECURITY DEFINER 
                BEGIN 
                    DECLARE _id VARCHAR(36); 
                    SET _id = UUID();
                    INSERT INTO $warmups_table_name(ID, Name, Description) values (_id, _name, _description);
                    RETURN _id;
                END;";
            $wpdb->query($sql);

            $sql = "DROP PROCEDURE IF EXISTS delete_warmup;";
            $wpdb->query($sql);
            
            $sql = "CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_warmup`(
                `_id` VARCHAR(36) CHARSET utf8
                ) NOT DETERMINISTIC MODIFIES SQL DATA SQL SECURITY DEFINER 
                BEGIN 
                    DELETE FROM $warmups_equipment_table_name WHERE WarmupID = _id;
                    DELETE FROM $warmups_table_name WHERE ID = _id;
                END;";
            $wpdb->query($sql);
        }
    }
}
?>