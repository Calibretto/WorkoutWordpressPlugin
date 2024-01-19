<?php
defined( 'ABSPATH' ) OR exit;

require_once __DIR__ . "/controller.php";

/*
 * Plugin Name:       Workout Tracker
 * Description:       Assign and track workouts.
 * Version:           0.0.1
 * Author:            Brian Hackett
 * Author URI:        https://www.brianhackett.co.uk
 */

register_activation_hook(
	__FILE__,
	array('BHWorkoutPlugin_Controller', 'activation_setup')
);

?>