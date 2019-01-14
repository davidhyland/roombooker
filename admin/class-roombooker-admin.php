<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Roombooker
 * @subpackage Roombooker/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Roombooker
 * @subpackage Roombooker/admin
 * @author     Your Name <email@example.com>
 */
class Roombooker_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Roombooker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Roombooker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/roombooker-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Roombooker_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Roombooker_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'canvasjs', plugin_dir_url( __FILE__ ) . 'js/chart.min.js', array(), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/roombooker-admin.js', array( 'jquery' ), $this->version, true );

	}



	public function jhub_settings_init() {

	 // register a new setting for "jhub" page
	 register_setting( 'jhub', 'jhub_options' );
	 
	 // register a new section in the "wporg" page
	 add_settings_section(
	 'jhub_section_options',
	 __( 'Confirmation Email', 'jhub' ),
	 'jhub_section_options_cb',
	 'jhub'
	 );
	 
	 add_settings_field(
		 'email_fromname', 
		 __( 'From Name', 'jhub' ),
		 'jhub_field_fromname_cb',
		 'jhub',
		 'jhub_section_options',
		 [
		 'label_for' => 'email_fromname',
		 'class' => 'jhub_row'
		 ]
	 );

	 add_settings_field(
		 'email_fromemail', 
		 __( 'From Email', 'jhub' ),
		 'jhub_field_fromemail_cb',
		 'jhub',
		 'jhub_section_options',
		 [
		 'label_for' => 'email_fromemail',
		 'class' => 'jhub_row'
		 ]
	 );
	 
	 add_settings_field(
		 'email_subject', 
		 __( 'Email Subject', 'jhub' ),
		 'jhub_field_emailsubject_cb',
		 'jhub',
		 'jhub_section_options',
		 [
		 'label_for' => 'email_subject',
		 'class' => 'jhub_row'
		 ]
	 );
	 
	 
	}	





	 
	/**
	 * top level menu
	 */


	public function jhub_options_page() {
	 // add top level menu page

		add_menu_page(
        'JHub Roombooker',
        'Room Booker',
        'manage_categories',
        'jhub-roombooker',
        'jhub_stats_page_html',
        'dashicons-calendar-alt',
        26
    );

    add_submenu_page(
        'jhub-roombooker',
        'JHub Roombooker Settings',
        'Settings',
        'manage_categories',
        'jhub-roombooker-settings',
        'jhub_options_page_html'
    );


	}
	 

}
	 

	/**
	 * custom option and settings:
	 * callback functions
	 */
	 
	 
	// section callbacks can accept an $args parameter, which is an array.
	// $args have the following keys defined: title, id, callback.
	// the values are defined at the add_settings_section() function.
	function jhub_section_options_cb( $args ) {

	}


	function jhub_stats_page_html() {
		 // check user capabilities
		 if ( ! current_user_can( 'manage_categories' ) ) {
		 	return;
		 }
		 
		 include( plugin_dir_path( __FILE__ ) . 'partials/roombooker-admin-display.php' );
	}



	function jhub_options_page_html() {
		 // check user capabilities
		 if ( ! current_user_can( 'manage_categories' ) ) {
		 	return;
		 }
		 
		 // add error/update messages
		 
		 // check if the user have submitted the settings
		 // wordpress will add the "settings-updated" $_GET parameter to the url
		 if ( isset( $_GET['settings-updated'] ) ) {
		 	// add settings saved message with the class of "updated"
		 		add_settings_error( 'jhub_messages', 'jhub_message', __( 'Settings Saved', 'jhub' ), 'updated' );
		 }
		 
		 // show error/update messages
		 settings_errors( 'jhub_messages' );
		 ?>
		 <div class="wrap">
		 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		 <form action="options.php" method="post">
		 <?php
		 // output security fields for the registered setting "wporg"
		 settings_fields( 'jhub' );
		 // output setting sections and their fields
		 // (sections are registered for "wporg", each field is registered to a specific section)
		 do_settings_sections( 'jhub' );
		 // output save settings button
		 submit_button( 'Save Settings' );
		 ?>
		 </form>
		 </div>
		 <?php
	}


	 
	// field callbacks can accept an $args parameter, which is an array.
	// $args is defined at the add_settings_field() function.
	// wordpress has magic interaction with the following keys: label_for, class.
	// the "label_for" key value is used for the "for" attribute of the <label>.
	// the "class" key value is used for the "class" attribute of the <tr> containing the field.
	// you can add custom key value pairs to be used inside your callbacks.

	function jhub_field_fromname_cb( $args ) {
	 // get the value of the setting we've registered with register_setting()
	 $options = get_option( 'jhub_options' );
	 // output the field
	 ?>
	 <input id="<?php echo esc_attr( $args['label_for'] ); ?>" size="50" name="jhub_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset($options[$args['label_for']]) ? $options[$args['label_for']] : '' ;?>">
	 <p class="description">Enter the FROM name for the booking confirmation email.</p>
	 <?php
	}	

	function jhub_field_fromemail_cb( $args ) {
	 // get the value of the setting we've registered with register_setting()
	 $options = get_option( 'jhub_options' );
	 $default = get_option( 'admin_email' );
	 // output the field
	 ?>
	 <input id="<?php echo esc_attr( $args['label_for'] ); ?>" size="50" name="jhub_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset($options[$args['label_for']]) ? $options[$args['label_for']] : '' ;?>">
	 <p class="description">Enter the FROM email address for the booking confirmation email.</p>
	 <?php
	}

	function jhub_field_emailsubject_cb( $args ) {
	 // get the value of the setting we've registered with register_setting()
	 $options = get_option( 'jhub_options' );
	 // output the field
	 ?>
	 <input id="<?php echo esc_attr( $args['label_for'] ); ?>" size="50" name="jhub_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo isset($options[$args['label_for']]) ? $options[$args['label_for']] : '' ;?>">
	 <p class="description">Enter the subject for the confirmation email. [DATE] will be swapped for the event date.</p>
	 <?php
	}


