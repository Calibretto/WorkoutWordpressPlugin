<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
require_once plugin_dir_path( __FILE__ ) . "equipment.php";
require_once plugin_dir_path( __FILE__ ) . "admin/db/warmups.php";

if ( class_exists( 'BHWorkoutPlugin_Warmup' ) == FALSE ) {
    class BHWorkoutPlugin_Warmup {
        public ?string $id = NULL;
        public string $name = "Unknown";
        public ?string $description = NULL;
        public ?array $equipment = [];

        public static function from_db_query($query_result) : BHWorkoutPlugin_Warmup {
            $warmup = new BHWorkoutPlugin_Warmup;
            $warmup->id = $query_result->ID;
            $warmup->name = stripslashes($query_result->Name);
            $warmup->description = stripslashes($query_result->Description);
            
            return $warmup;
        }

        private function to_array() : array {
            global $wpdb;

            $obj = [];

            if(is_null($this->id) == FALSE) {
                $obj['id'] = $this->id;
            }

            $obj['name'] = $this->name;
            if(strlen($obj['name']) <= 0) {
                throw new Exception("Name length must be greater than zero.");
            }
            $obj['name'] = $wpdb->_real_escape($obj['name']);

            if(is_null($this->description) == FALSE) {
                $obj['description'] = $wpdb->_real_escape($this->description);
            }

            return $obj;
        }

        public function db_insert() : string {
            $obj = $this->to_array();

            $sql = "SELECT add_warmup(";
            $sql .= "'".$obj['name']."',";
            $sql .= isset($obj['description']) ? "'".$obj['description']."'" : "NULL";
            $sql .= ") AS uuid;";

            return $sql;
        }

        public function db_insert_equipment() : array {
            global $wpdb;
            $queries = [];

            $equipment_table_name = BHWorkoutPlugin_WarmupsDB::equipment_table_name();
            foreach($this->equipment as $equipment) {
                if ($equipment instanceof BHWorkoutPlugin_Equipment) {
                    $sql = "INSERT INTO $equipment_table_name(WarmupID, EquipmentID) VALUES('%s', '%s');";
                    $queries[] = $wpdb->prepare($sql, [$this->id, $equipment->id]);
                }
            }

            return $queries;
        }

        public function db_update() : array {
            global $wpdb;
            $table_name = BHWorkoutPlugin_WarmupsDB::table_name();
            $equipment_table_name = BHWorkoutPlugin_WarmupsDB::equipment_table_name();

            $obj = $this->to_array();
            $queries = [];

            // Delete equipment first.
            $queries[] = $wpdb->prepare("DELETE FROM $equipment_table_name WHERE WarmupID = '%s';", [$this->id]);

            // Update warmup.
            $sql = "UPDATE $table_name SET ";
            $sql .= "Name='".$obj['name']."', ";
            $sql .= "Description='".$obj['description']."', ";
            $sql .= " WHERE ID='".$obj['id']."'";

            // Add equipment.
            $queries = array_merge($queries, $this->db_insert_equipment());

            return $queries;
        }

        public function equipment_display_list() : string {
            if(count($this->equipment) == 0) {
                return "-";
            }

            return implode(", ", array_column($this->equipment, 'name'));
        }

        public function equipment_ids() : array {
            return array_column($this->equipment, 'id');
        }
    }
}
?>