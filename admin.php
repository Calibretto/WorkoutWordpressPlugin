<?php
require_once plugin_dir_path( __FILE__ ) . "database.php";
require_once plugin_dir_path( __FILE__ ) . "admin/db/equipment.php";
require_once plugin_dir_path( __FILE__ ) . "admin/db/warmups.php";
require_once plugin_dir_path( __FILE__ ) . "utils/notices.php";

if ( class_exists( 'BHWorkoutPlugin_Admin' ) == FALSE ) {
    class BHWorkoutPlugin_Admin {

        private static $initiated = false;

        public static function init() {
            if (self::$initiated == FALSE) {
                self::init_hooks();
            }
        }

        public static function init_hooks() {
            add_action('admin_menu', array('BHWorkoutPlugin_Admin', 'workouts_admin_page'));
            add_action('admin_enqueue_scripts', array('BHWorkoutPlugin_Admin', 'load_resources'));

            self::$initiated = TRUE;
        }

        static function workouts_admin_page(){
            $page_title = 'Workouts';
            $menu_title = 'Workouts';
            $capability = 'manage_options';
            $menu_slug  = 'pages/admin.php';
            $function   = array('BHWorkoutPlugin_Admin', 'workouts_admin_page_load');

            add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function );

            // Equipment
            $root_menu = 'pages/admin.php';
            $page_title = 'Equipment';
            $menu_title = 'Equipment';
            $capability = 'manage_options';
            $menu_slug  = 'pages/equipment.php';
            $function   = array('BHWorkoutPlugin_Admin', 'workouts_admin_equipment_page_load');

            $hookname = add_submenu_page($root_menu, $page_title, $menu_title, $capability, $menu_slug, $function);
            add_action( 'load-' . $hookname, array('BHWorkoutPlugin_Admin', 'workouts_admin_equipment_page_submit'));

            // Warmups
            $root_menu = 'pages/admin.php';
            $page_title = 'Warmups';
            $menu_title = 'Warmups';
            $capability = 'manage_options';
            $menu_slug  = 'pages/warmups.php';
            $function   = array('BHWorkoutPlugin_Admin', 'workouts_admin_warmups_page_load');

            $hookname = add_submenu_page($root_menu, $page_title, $menu_title, $capability, $menu_slug, $function);
            add_action( 'load-' . $hookname, array('BHWorkoutPlugin_Admin', 'workouts_admin_warmups_page_submit'));
        }

        static function workouts_admin_page_load(){
            require_once plugin_dir_path( __FILE__ ) . 'pages/admin.php';
        }

        // Equipment
        static function workouts_admin_equipment_page_load(){
            if (isset($_POST['equipment_edit'])) {
                require_once plugin_dir_path( __FILE__ ) . "pages/equipment-edit.php";
            } else {
                require_once plugin_dir_path( __FILE__ ) . 'pages/equipment.php';
            }
        }

        static function workouts_admin_equipment_page_submit() {
            if (isset($_POST['equipment_submit'])) {
                BHWorkoutPlugin_Admin::workouts_admin_add_equipment();
            } elseif (isset($_POST['equipment_delete'])) {
                BHWorkoutPlugin_Admin::workouts_admin_delete_equipment();
            } elseif (isset($_POST['equipment_save'])) {
                BHWorkoutPlugin_Admin::workouts_admin_update_equipment();
            }
        }

        private static function workouts_admin_parse_equipment() : BHWorkoutPlugin_Equipment {
            $equipment = new BHWorkoutPlugin_Equipment;
            $equipment->id = isset($_POST['equipment_id']) ? $_POST['equipment_id'] : NULL;
            $equipment->name = $_POST['equipment_name'];
            $equipment->value_min = number_format((float)$_POST['equipment_value_min'], 2, '.', '');
            $equipment->value_max = number_format((float)$_POST['equipment_value_max'], 2, '.', '');
            $equipment->value_step = number_format((float)$_POST['equipment_value_step'], 2, '.', '');
            $equipment->units = EquipmentUnit::tryFrom($_POST['equipment_units']);
            return $equipment;
        }

        private static function workouts_admin_add_equipment() {
            $equipment = BHWorkoutPlugin_Admin::workouts_admin_parse_equipment();

            try {
                BHWorkoutPlugin_EquipmentDB::add_equipment($equipment);
                BHWorkoutPlugin_Notice::success("Added equipment");
            } catch (Exception $e) {
                BHWorkoutPlugin_Notice::error($e->getMessage());
                error_log($e);
            }
        }

        private static function workouts_admin_delete_equipment() {
            try {
                BHWorkoutPlugin_EquipmentDB::delete_equipment($_POST['equipment_delete']);
                BHWorkoutPlugin_Notice::success("Deleted equipment");
            } catch (Exception $e) {
                BHWorkoutPlugin_Notice::error($e->getMessage());
                error_log($e);
            }
        }

        private static function workouts_admin_update_equipment() {
            $equipment = BHWorkoutPlugin_Admin::workouts_admin_parse_equipment();

            try {
                BHWorkoutPlugin_EquipmentDB::update_equipment($equipment);
                BHWorkoutPlugin_Notice::success("Updated equipment");
            } catch (Exception $e) {
                BHWorkoutPlugin_Notice::error($e->getMessage());
                error_log($e);
            }
        }

        // Warmups
        static function workouts_admin_warmups_page_load(){
            if (isset($_POST['warmup_edit'])) {
                require_once plugin_dir_path( __FILE__ ) . "pages/warmup-edit.php";
            } else {
                require_once plugin_dir_path( __FILE__ ) . 'pages/warmups.php';
            }
        }

        static function workouts_admin_warmups_page_submit() {
            if (isset($_POST['warmup_submit'])) {
                BHWorkoutPlugin_Admin::workouts_admin_add_warmup();
            } elseif (isset($_POST['warmup_delete'])) {
                BHWorkoutPlugin_Admin::workouts_admin_delete_warmup();
            } elseif (isset($_POST['warmup_save'])) {
                BHWorkoutPlugin_Admin::workouts_admin_update_warmup();
            }
        }

        private static function workouts_admin_parse_warmup() : BHWorkoutPlugin_Warmup {
            $warmup = new BHWorkoutPlugin_Warmup;
            $warmup->id = isset($_POST['warmup_id']) ? $_POST['warmup_id'] : NULL;
            $warmup->name = $_POST['warmup_name'];
            $warmup->description = isset($_POST['warmup_description']) ? $_POST['warmup_description'] : NULL;

            if (isset($_POST['warmup_equipment'])) {
                $equipment_uuids = $_POST['warmup_equipment'];
                foreach($equipment_uuids as $uuid) {
                    try {
                        $equipment = BHWorkoutPlugin_EquipmentDB::get_equipment($uuid);
                        if (is_null($equipment) == FALSE) {
                            $warmup->equipment[] = $equipment;
                        }
                    } catch (Exception $e) {
                        error_log("[Warmup] Tried to load equipment ($uuid) and failed: $e");
                    }
                }
            }

            return $warmup;
        }

        private static function workouts_admin_add_warmup() {
            $warmup = BHWorkoutPlugin_Admin::workouts_admin_parse_warmup();

            try {
                BHWorkoutPlugin_WarmupsDB::add_warmup($warmup);
                BHWorkoutPlugin_Notice::success("Added warmup");
            } catch (Exception $e) {
                BHWorkoutPlugin_Notice::error($e->getMessage());
                error_log($e);
            }
        }

        private static function workouts_admin_delete_warmup() {
            // TBD
        }

        private static function workouts_admin_update_warmup() {
            // TBD
        }

        // General
        public static function load_resources($hook) {
            wp_register_script('common.js', plugin_dir_url( __FILE__ ) . 'js/common.js');
            wp_enqueue_script('common.js');

            if ($hook == 'workouts_page_pages/equipment') {
                wp_register_style('equipment.css', plugin_dir_url( __FILE__ ) . 'css/equipment.css');
                wp_enqueue_style('equipment.css');

                wp_register_script('equipment.js', plugin_dir_url( __FILE__ ) . 'js/equipment.js');
                wp_enqueue_script('equipment.js');
            }

            if ($hook == 'workouts_page_pages/warmups') {
                wp_register_style('warmups.css', plugin_dir_url( __FILE__ ) . 'css/warmups.css');
                wp_enqueue_style('warmups.css');

                wp_register_script('warmups.js', plugin_dir_url( __FILE__ ) . 'js/warmups.js');
                wp_enqueue_script('warmups.js');
            }
        }
    }
}
?>