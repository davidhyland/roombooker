<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Roombooker
 * @subpackage Roombooker/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Roombooker
 * @subpackage Roombooker/includes
 * @author     Your Name <email@example.com>
 */
class Roombooker_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		global $wpdb;
		$table_name = $wpdb->prefix . ROOMBOOKER_TABLE;
		//$wpdb->query('DROP TABLE IF EXISTS '.$table_name);
		delete_option( 'jhub-roombooker_version' );

	}

}
