<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Roombooker
 * @subpackage Roombooker/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Roombooker
 * @subpackage Roombooker/includes
 * @author     Your Name <email@example.com>
 */
class Roombooker_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . ROOMBOOKER_TABLE;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			id_key varchar(10) DEFAULT '' NOT NULL,
			room tinyint(1) NOT NULL,
			time_start datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			time_end datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			title varchar(255) DEFAULT '' NOT NULL,
			name varchar(255) DEFAULT '' NOT NULL,
			email varchar(255) DEFAULT '' NOT NULL,
			organisation varchar(255) DEFAULT '' NOT NULL,
			numpeople smallint(4) NOT NULL,
			date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			date_updated timestamp NOT NULL,
			active tinyint(1) NOT NULL DEFAULT '1',
			PRIMARY KEY  (id),
			INDEX id_key (id_key)
		) $charset_collate;";

		dbDelta( $sql );


		// LOGS TABLE
		$sql = "CREATE TABLE IF NOT EXISTS ".$table_name."_logs (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			type varchar(10) NOT NULL DEFAULT '0',
			event_id mediumint(9) NOT NULL DEFAULT '0',
			result varchar(10) NOT NULL DEFAULT '0',
			value text NOT NULL,
			logdate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE INDEX id (id)
		) $charset_collate;";

		dbDelta( $sql );

		/*
		$wpdb->insert( 
			$table_name, 
			array( 
				'id_key' => 'qwerty', 
				'room' => '1', 
				'time_start' => '2017-10-20 09:00:00', 
				'time_end' => '2017-10-20 11:30:00', 
				'title' => 'Event 1', 
				'name' => 'Bob Smith', 
				'email' => 'bob@test.com', 
				'organisation' => 'Acme Tools', 
				'numpeople' => '4'
			) 
		);

		$wpdb->insert( 
			$table_name, 
			array( 
				'id_key' => 'abcdef', 
				'room' => '1', 
				'time_start' => '2017-10-20 12:00:00', 
				'time_end' => '2017-10-20 15:30:00', 
				'title' => 'Event 2', 
				'name' => 'Bob Smith', 
				'email' => 'bob@test.com', 
				'organisation' => 'Acme Tools', 
				'numpeople' => '3'
			) 
		);

		$wpdb->insert( 
			$table_name, 
			array( 
				'id_key' => '1231231', 
				'room' => '2', 
				'time_start' => '2017-10-20 12:00:00', 
				'time_end' => '2017-10-20 15:30:00', 
				'title' => 'Event 3', 
				'name' => 'Bob Smith', 
				'email' => 'bob@test.com', 
				'organisation' => 'Acme Tools', 
				'numpeople' => '6'
			) 
		);

		$wpdb->insert( 
			$table_name, 
			array( 
				'id_key' => '234234', 
				'room' => '3', 
				'time_start' => '2017-10-23 12:00:00', 
				'time_end' => '2017-10-23 15:30:00', 
				'title' => 'Event 4', 
				'name' => 'Bob Smith', 
				'email' => 'bob@test.com', 
				'organisation' => 'Acme Tools', 
				'numpeople' => '4'
			) 
		);
		*/

		add_option( 'jhub_roombooker_version', ROOMBOOKER_VERSION );

		// settings defaults
		$defaults = array(
			'email_fromname' => 'JHub Room Booker',
			'email_fromemail' => get_option('admin_email'),
			'email_subject' => 'JHub Room booking confirmation'
		);
		add_option( 'jhub_options', $defaults );

	}




}
