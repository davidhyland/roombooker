<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Room Booker
 *
 * @wordpress-plugin
 * Plugin Name:       Room Booker
 * Plugin URI:        #
 * Description:       A custom room booking plugin for JHub. Activate and add the following shortcode to a page: [jhub_roombooker]
 * Version:           1.4.4
 * Author:            David Hyland
 * Author URI:        http://dhyland.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       roombooker
 * Domain Path:       /languages
 */

/*
UPDATE NOTES
 - Change version x 2 here
 - Add version notes in readme.txt and readme.md
 - Commit to git
 - Create release in git "Roombooker vX.X.X"
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ROOMBOOKER_VERSION', '1.4.4' );
define( 'ROOMBOOKER_TABLE', 'jhub_roombooker' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-roombooker-activator.php
 */
function activate_roombooker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-roombooker-activator.php';
	roombooker_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-roombooker-deactivator.php
 */
function deactivate_roombooker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-roombooker-deactivator.php';
	roombooker_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_roombooker' );
register_deactivation_hook( __FILE__, 'deactivate_roombooker' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-roombooker.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_roombooker() {

	$plugin = new Roombooker();
	$plugin->run();

}
run_roombooker();


// PLUGIN UPDATER
require 'includes/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/davidhyland/roombooker/',
	__FILE__,
	'roombooker'
);