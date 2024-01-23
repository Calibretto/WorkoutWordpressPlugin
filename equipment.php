<?php
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

enum EquipmentUnit: String {
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

        public static function get_all_query($table_name) : string {
            return "SELECT * FROM $table_name ORDER BY Name ASC;";
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

        public function db_insert() : string {
            global $wpdb;

            $name = $this->name;
            if(strlen($name) <= 0) {
                throw new Exception("Name length must be greater than zero.");
            }
            $name = $wpdb->_real_escape($name);

            $value_max = $this->value_max;
            $value_min = $this->value_min;
            $value_step = $this->value_step;

            $units = $this->units;
            if (is_null($units) == FALSE) {
                $units = $units->value;
            }

            if ($this->has_value() == FALSE) {
                $value_min = NULL;
                $value_max = NULL;
                $value_step = NULL;
                $units = NULL;
            }

            $sql = "CALL add_equipment(";
            $sql .= "'$name',";
            $sql .= is_null($value_min) ? "NULL," : "$value_min,";
            $sql .= is_null($value_max) ? "NULL," : "$value_max,";
            $sql .= is_null($value_step) ? "NULL," : "$value_step,";
            $sql .= is_null($units) ? "NULL" : "'$units'";
            $sql .= ");";

            return $sql;
        }

        public function display_value() : string {
            if ($this->has_value()) {
                if ($this->value_min == $this->value_max) {
                    return $this->value_min . $this->units->value;
                } else {
                    return $this->value_min . $this->units->value." - ".$this->value_max . $this->units->value;
                }
            }

            return "-";
        }

        public function display_value_step() : string {
            if ($this->has_value() && ($this->value_step != "0.00")) {
                return "$this->value_step ".$this->units->value;
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