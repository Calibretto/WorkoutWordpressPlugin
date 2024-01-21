<?php
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

            self::$initiated = TRUE;
        }

        static function workouts_admin_page(){
            $page_title = 'Workouts';
            $menu_title = 'Workouts';
            $capability = 'manage_options';
            $menu_slug  = 'pages/admin.php';
            $function   = array('BHWorkoutPlugin_Admin', 'workouts_admin_page_load');

            add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function );
        }

        static function workouts_admin_page_load(){
            require_once plugin_dir_path( __FILE__ ) . 'pages/admin.php';
        }
    }
}
?>