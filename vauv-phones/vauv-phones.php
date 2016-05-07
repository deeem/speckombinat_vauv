<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           Vauv_Phones
 *
 * @wordpress-plugin
 * Plugin Name:       VAUV Phones
 * Plugin URI:        https://github.com/deeem/speckombinat_vauv
 * Description:       Справочник телефонов
 * Version:           1.0.0
 * Author:            deeem
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vauv-phones-activator.php
 */
function activate_vauv_phones() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vauv-phones-activator.php';
	Vauv_Phones_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vauv-phones-deactivator.php
 */
function deactivate_vauv_phones() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vauv-phones-deactivator.php';
	Vauv_Phones_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vauv_phones' );
register_deactivation_hook( __FILE__, 'deactivate_vauv_phones' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vauv-phones.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_vauv_phones() {

	$plugin = new Vauv_Phones();
	$plugin->run();

}
run_vauv_phones();
