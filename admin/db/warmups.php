<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

if ( class_exists( 'BHWorkoutPlugin_WarmupsDB' ) == FALSE ) {
    class BHWorkoutPlugin_WarmupsDB {

        public static function table_name() : string {
            return BHWorkoutPlugin_DatabaseManager::prefix() . "warmups";
        }

        public static function create_tables() {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            $warmups_table_name = self::table_name();

            // Warmups table
            $sql = "CREATE TABLE IF NOT EXISTS $warmups_table_name (
                ID VARCHAR(36) NOT NULL , 
                Name VARCHAR(512) NOT NULL , 
                Description TEXT NULL , 
                PRIMARY KEY (ID)
                ) $charset_collate;";
            dbDelta($sql);
        }

        public static function create_stored_procedures() {
            global $wpdb;
            $warmups_table_name = self::table_name();

            // Equipment stored procedures
            $sql = "DROP PROCEDURE add_warmup;";
            $wpdb->query($sql);
            
            $sql = "CREATE DEFINER=`root`@`localhost` PROCEDURE `add_warmup`(
                IN `_name` VARCHAR(512) CHARSET utf8, 
                IN `_description` TEXT
                ) NOT DETERMINISTIC MODIFIES SQL DATA SQL SECURITY DEFINER 
                BEGIN 
                    INSERT INTO $warmups_table_name(ID, Name, Description) values (UUID(), _name, _description); 
                END";
            $wpdb->query($sql);
        }
    }
}
?>