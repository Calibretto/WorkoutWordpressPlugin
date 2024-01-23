<?php
require_once __DIR__ . "/database.php";

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

            $root_menu = 'pages/admin.php';
            $page_title = 'Equipment';
            $menu_title = 'Equipment';
            $capability = 'manage_options';
            $menu_slug  = 'pages/equipment.php';
            $function   = array('BHWorkoutPlugin_Admin', 'workouts_admin_equipment_page_load');

            $hookname = add_submenu_page($root_menu, $page_title, $menu_title, $capability, $menu_slug, $function);
            add_action( 'load-' . $hookname, array('BHWorkoutPlugin_Admin', 'workouts_admin_equipment_page_submit'));
        }

        static function workouts_admin_page_load(){
            require_once plugin_dir_path( __FILE__ ) . 'pages/admin.php';
        }

        static function workouts_admin_equipment_page_load(){
            require_once plugin_dir_path( __FILE__ ) . 'pages/equipment.php';
        }

        static function workouts_admin_equipment_page_submit() {
            if (isset($_POST['equipment_submit']) == FALSE) {
                return;
            }

            $equipment = new BHWorkoutPlugin_Equipment;
            $equipment->name = $_POST['equipment_name'];
            $equipment->value_min = number_format((float)$_POST['equipment_value_min'], 2, '.', '');
            $equipment->value_max = number_format((float)$_POST['equipment_value_max'], 2, '.', '');
            $equipment->value_step = number_format((float)$_POST['equipment_value_step'], 2, '.', '');
            $equipment->units = EquipmentUnit::tryFrom($_POST['equipment_units']);

            try {
                BHWorkoutPlugin_DatabaseManager::add_equipment($equipment);
            } catch (Exception $e) {
                error_log($e);
            }
        }

        public static function load_resources($hook) {
            if ($hook == 'workouts_page_pages/equipment') {
                wp_register_style('equipment.css', plugin_dir_url( __FILE__ ) . 'css/equipment.css');
                wp_enqueue_style('equipment.css');

                wp_register_script('equipment.js', plugin_dir_url( __FILE__ ) . 'js/equipment.js');
                wp_enqueue_script('equipment.js');
            }
        }
    }
}
?>