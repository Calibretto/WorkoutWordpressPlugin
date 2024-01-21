<?php
/*
 * Plugin Name:       Workout Tracker
 * Description:       Assign and track workouts.
 * Version:           0.0.1
 * Author:            Brian Hackett
 * Author URI:        https://www.brianhackett.co.uk
 */

// Make sure we don't expose any info if called directly
defined( 'ABSPATH' ) OR exit;

if (function_exists('add_action') == FALSE) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// Setup
define( 'WORKOUTS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( WORKOUTS__PLUGIN_DIR . 'controller.php' );

// Activation
register_activation_hook(
	__FILE__,
	array('BHWorkoutPlugin_Controller', 'activation_setup')
);

if (is_admin()) {
	require_once( WORKOUTS__PLUGIN_DIR . 'admin.php' );
	add_action( 'init', array( 'BHWorkoutPlugin_Admin', 'init' ) );
}

?>