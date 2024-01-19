<?php
require_once __DIR__ . "/database.php";

if ( class_exists( 'BHWorkoutPlugin_Controller' ) == FALSE ) {
    class BHWorkoutPlugin_Controller {

        public static function activation_setup() {
            BHWorkoutPlugin_DatabaseManager::activation_setup();
        }

    }
}
?>