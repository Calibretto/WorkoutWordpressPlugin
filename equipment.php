<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

enum EquipmentUnit: String {
    case NONE = "none";
    case KG = 'kg';
}

if ( class_exists( 'BHWorkoutPlugin_Equipment' ) == FALSE ) {
    class BHWorkoutPlugin_Equipment {
        public ?string $id = NULL;
        public string $name = "Unknown";
        public ?string $value_min = NULL;
        public ?string $value_max = NULL;
        public ?string $value_step = NULL;
        public ?EquipmentUnit $units = NULL;

        public static function get_all_query(string $table_name) : string {
            return "SELECT * FROM $table_name ORDER BY Name ASC;";
        }

        public static function select_query(string $table_name) : string {
            return "SELECT * FROM $table_name WHERE ID='%s';";
        }

        public static function delete_query(string $table_name) : string {
            return "DELETE FROM $table_name WHERE ID='%s'";
        }

        public static function from_db_query($query_result) : BHWorkoutPlugin_Equipment {
            $equipment = new BHWorkoutPlugin_Equipment;
            $equipment->id = $query_result->ID;
            $equipment->name = stripslashes($query_result->Name);
            $equipment->value_min = $query_result->ValueMin;
            $equipment->value_max = $query_result->ValueMax;
            $equipment->value_step = $query_result->ValueStep;
            $equipment->units = EquipmentUnit::tryFrom($query_result->Units);
            
            return $equipment;
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

            if ($this->has_value()) {
                $obj['value_max'] = $this->value_max;
                $obj['value_min'] = $this->value_min;
                $obj['value_step'] = $this->value_step;
                $obj['units'] = $this->units->value;
            }

            return $obj;
        }

        public function db_insert() : string {
            $obj = $this->to_array();

            $sql = "CALL add_equipment(";
            $sql .= "'".$obj['name']."',";
            $sql .= isset($obj['value_min']) ? $obj['value_min']."," : "NULL,";
            $sql .= isset($obj['value_max']) ? $obj['value_max']."," : "NULL,";
            $sql .= isset($obj['value_step']) ? $obj['value_step']."," : "NULL,";
            $sql .= isset($obj['units']) ? "'".$obj['units']."'" : "NULL";
            $sql .= ");";

            error_log($sql);
            return $sql;
        }

        public function db_update(string $table_name) : string {
            $obj = $this->to_array();

            $sql = "UPDATE $table_name SET ";
            $sql .= "Name='".$obj['name']."', ";
            $sql .= "ValueMin=".(isset($obj['value_min']) ? $obj['value_min'].", " : "NULL, ");
            $sql .= "ValueMax=".(isset($obj['value_max']) ? $obj['value_max'].", " : "NULL, ");
            $sql .= "ValueStep=".(isset($obj['value_step']) ? $obj['value_step'].", " : "NULL, ");
            $sql .= "Units=".(isset($obj['units']) ? "'".$obj['units']."'" : "NULL");
            $sql .= " WHERE ID='".$obj['id']."'";

            return $sql;
        }

        public function display_unit() : string {
            $unit_value = $this->units->value;
            if ($unit_value == "none") {
                $unit_value = "";
            }

            return $unit_value;
        }

        public function display_value() : string {
            if ($this->has_value()) {
                $unit_value = $this->display_unit();
                if ($this->value_min == $this->value_max) {
                    return $this->value_min . $unit_value;
                } else {
                    return $this->value_min . $unit_value." - ".$this->value_max . $unit_value;
                }
            }

            return "-";
        }

        public function display_value_step() : string {
            if ($this->has_value() && ($this->value_step != "0.00")) {
                return "$this->value_step ".$this->display_unit();
            }

            return "-";
        }

        private function has_value() : bool {
            return (is_null($this->value_min) == FALSE) && 
                (is_null($this->value_max) == FALSE) &&
                (is_null($this->value_step) == FALSE) &&
                (is_null($this->units) == FALSE);
        }
    }
}