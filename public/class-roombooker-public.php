<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */
class Roombooker_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'fullcalendar', plugin_dir_url( __DIR__) . 'includes/fullcalendar/fullcalendar.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'scheduler', plugin_dir_url( __DIR__) . 'includes/fullcalendar/scheduler.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'smoke', plugin_dir_url( __DIR__) . 'includes/smoke/smoke.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'qtip', plugin_dir_url( __DIR__ ) . 'includes/qtip/jquery.qtip.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/roombooker-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'moment', plugin_dir_url( __DIR__) . 'includes/fullcalendar/lib/moment.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'qtip', plugin_dir_url( __DIR__ ) . 'includes/qtip/jquery.qtip.min.js', array( 'jquery' ), $this->version, false );
		//wp_enqueue_script( 'fullcalendar', plugin_dir_url( __DIR__) . 'fullcalendar/fullcalendar.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'fullcalendar', plugin_dir_url( __DIR__) . 'includes/fullcalendar/fullcalendar.min.js', array( 'moment' ), $this->version, false );
		wp_enqueue_script( 'scheduler', plugin_dir_url( __DIR__) . 'includes/fullcalendar/scheduler.min.js', array( 'fullcalendar' ), $this->version, false );
		wp_enqueue_script( 'locale', plugin_dir_url( __DIR__) . 'includes/fullcalendar/locale/en-gb.js', array( 'fullcalendar' ), $this->version, false );
		wp_enqueue_script( 'validation', plugin_dir_url( __FILE__ ) . 'js/validation.js', array( 'fullcalendar' ), $this->version, false );
		wp_enqueue_script( 'smoke', plugin_dir_url( __DIR__ ) . 'includes/smoke/smoke.min.js', array( 'fullcalendar' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/roombooker-public.js', array( 'fullcalendar' ), $this->version, false );

		// set up ajax url 
		wp_localize_script( $this->plugin_name, 'roombookerajax', array('ajaxurl' => admin_url('admin-ajax.php')));
	}


	/**
	 * Processes shortcode 
	 *
	 * @param   array	$atts		The attributes from the shortcode
	 *
	 * @uses	get_option
	 * @uses	get_layout
	 *
	 * @return	mixed	$output		Output of the buffer
	 */
	public function do_jhub_roombooker( $atts = array() ) {

		ob_start();

		include ( 'partials/roombooker-public-display.php' );

		$output = ob_get_contents();

		ob_end_clean();

		return $output;

	} // do_roombooker()


	/**
	 * Registers all shortcodes at once
	 *
	 * @return [type] [description]
	 */
	public function register_shortcodes() {

		add_shortcode( 'jhub_roombooker', array( $this, 'do_jhub_roombooker' ) );

	} // register_shortcodes()


	private function checkEventClashes($event, $id = false){
		if(is_array($event)){
			global $wpdb;
			$table_name = $wpdb->prefix . ROOMBOOKER_TABLE;
			$sql = "SELECT * FROM $table_name
							WHERE (%s BETWEEN DATE_SUB(time_start, INTERVAL 1 MINUTE) AND DATE_SUB(time_end, INTERVAL 1 MINUTE) 
							OR %s BETWEEN DATE_ADD(time_start, INTERVAL 1 MINUTE) AND DATE_ADD(time_end, INTERVAL 1 MINUTE))
							AND room = %d AND active = 1";
			if($id !== false && is_numeric($id)){
				$sql .= " AND id <> %d";
				$sql = $wpdb->prepare($sql, $event['time_start'], $event['time_end'], $event['room'], $id);
			}
			else{
				$sql = $wpdb->prepare($sql, $event['time_start'], $event['time_end'], $event['room']);
			}
			//logthis($sql);
			$result = $wpdb->get_results($sql);

			if($result !== false && is_array($result) && count($result) > 0) {
					//log result
					logthis_db('check', $id, 'clash', array(
						'id' => $result[0]->id,
						'room' => $result[0]->room,
						'start' => $result[0]->time_start, 
						'end' => $result[0]->time_end,
						'sql' => $sql
					)
				);
			}

			return $result ? $result : false;
		}
	}


	public function roombooker_save_event() {

		if ( ! empty( $_POST ) ) {


			global $wpdb;
			$table_name = $wpdb->prefix . ROOMBOOKER_TABLE;

			$data = getPostData();
			$clashes = $this->checkEventClashes($data);

			if($clashes === false){
				
				//logthis('NO CLASH');

				$data['id_key'] = generateUniqueKey(10);

				$result = $wpdb->insert( 
					$table_name, 
					$data
				);

				if($result !== false){

					// SEND CONFIRMATION EMAIL
					if(! empty($_POST['jhubUrl'])) {
						// room name for email
						if(! empty($_POST['room_name'])) $data['room_name'] = sanitize_text_field($_POST['room_name']);
						$email_sent = sendConfirmationEmail($data, $_POST['jhubUrl']);
						$output['email'] = $email_sent;
					}

					$output['result'] = 'success';
					$output['id'] = $wpdb->insert_id;

					//log result
					logthis_db('save', $wpdb->insert_id, 'success', array(
						'room' => $data['room'], 
						'start' => $data['time_start'], 
						'end' => $data['time_end'])
					);
				}
				else{
					$output['result'] = 'insert-error';
				}

			}
			else {
				// CLASHES
				$output['result'] = 'clash';
				$temp = array();
				foreach($clashes as $key => $event){
					$temp[$key]['room'] = $event->room;
					$temp[$key]['time_start'] = $event->time_start;
					$temp[$key]['time_end'] = $event->time_end;
					$temp[$key]['organisation'] = $event->organisation;
				}
				$output['clashes'] = $temp;
				//logthis(json_encode($temp));
			}

		}
		else{
			$output['result'] = 'no-data';
		}

		echo json_encode($output);
  	die();


	} // roombooker_save_event()



	public function roombooker_update_event() {

		if ( ! empty( $_POST ) && ! empty( $_POST['id'] )) {

			global $wpdb;
			$table_name = $wpdb->prefix . ROOMBOOKER_TABLE;

			$data = getPostData();
			if(! empty($_POST['delete'])){
				$data['active'] = 0; // for deletion
				$clashes = false;
				$logtype = 'delete';
			}
			else{
				$clashes = $this->checkEventClashes($data, $_POST['id']);	
				$logtype = 'update';
			}


			if($clashes === false){

				$result = $wpdb->update( 
					$table_name, 
					$data,
					array( 'id' => sanitize_text_field($_POST['id']) )
				);

				if($result !== false){
					$output['result'] = 'success';

					//log result
					$log_data = ($logtype == 'update') ?  array(
						'room' => $data['room'], 
						'start' => $data['time_start'], 
						'end' => $data['time_end']) : false;
					logthis_db($logtype, sanitize_text_field($_POST['id']), 'success', $log_data);

				}
				else{
					$output['result'] = 'insert-error';
				}

			}
			else{
				// CLASHES
				$output['result'] = 'clash';
				$temp = array();
				foreach($clashes as $key => $event){
					$temp[$key]['room'] = $event->room;
					$temp[$key]['time_start'] = $event->time_start;
					$temp[$key]['time_end'] = $event->time_end;
					$temp[$key]['organisation'] = $event->organisation;
				}
				$output['clashes'] = $temp;
			}

		}
		else{
			$output['result'] = 'no-data';
		}

		echo json_encode($output);
  	die();
		

	} // roombooker_update_event()


}

/*
FUNCTIONS
*/

function sendConfirmationEmail($data, $url){

	$test = false;

	if(is_array($data) && $url && isset($data['id_key']) && isset($data['email'])){

		$options = get_option('jhub_options');
		$email_to = ($test) ? 'code@dhyland.com' : $data['email'];
		//if(!$test) $email_bcc = 'code@dhyland.com';
		$email_fromemail = (null !== $options['email_fromemail']) ? $options['email_fromemail'] : get_option('admin_email');
		$email_fromname = (null !== $options['email_fromname']) ? $options['email_fromname'] : get_option('blog_name');
		$email_subject = $options['email_subject'];
		$link = $url . '?edit=' . $data['id_key'];

		$headers[] = "From: $email_fromname <$email_fromemail>";
		if(isset($email_bcc)) $headers[] = "Bcc: $email_bcc";
		$headers[] = "Content-Type: text/html; charset=UTF-8";

		$email_msg  = '<p><b>JHub Room Booker</b></p>';
		$email_msg .= '<p>Thank you for booking a room. Here are the details:</p>';
		$email_msg .= '<p>';
		$email_msg .= '<b>Date</b>: '.date('l jS F Y', strtotime($data['time_start'])).'<br>';
		$email_msg .= '<b>Time:</b> '.date('H:i', strtotime($data['time_start'])).' to '.date('H:i', strtotime($data['time_end'])).'<br>';
		$email_msg .= '<b>Room:</b> '.$data['room_name'].'<br>';
		$email_msg .= '<b>Event:</b> '.$data['title'].'<br>';
		$email_msg .= '<b>Name:</b> '.$data['name'].'<br>';
		$email_msg .= '<b>Organisation:</b> '.$data['organisation'].'<br>';
		$email_msg .= '<b>Number of people:</b> '.$data['numpeople'];
		$email_msg .= '</p>';
		$email_msg .= '<p>You can change or delete this booking via the following link.</p>';
		$email_msg .= '<p><a href="'.$link.'"><b>Edit Booking</b></a></p>';
		$email_msg .= '<p><i>Please note that this is the only way to change or delete this booking so do not delete this email.</i></p>';
		$email_msg .= '<p>Many thanks,</p>';
		$email_msg .= '<p><b>The JHub Team</b></p>';

		$result = false;
		$result = $_SERVER['SERVER_ADDR'] == '127.0.0.1' ? true : wp_mail( $email_to, $email_subject, $email_msg, $headers );
		//$result = wp_mail( 'code@dhyland.com', 'JHub Room booking confirmation', $email_msg );
		return $result;

	}

}

function getPostData(){
	$data = array();
	if(! empty($_POST['room'])) $data['room'] = sanitize_text_field($_POST['room']);
	if(! empty($_POST['time_start'])) $data['time_start'] = sanitize_text_field($_POST['time_start']);
	if(! empty($_POST['time_end'])) $data['time_end'] = sanitize_text_field($_POST['time_end']);
	if(! empty($_POST['title'])) $data['title'] = sanitize_text_field($_POST['title']);
	if(! empty($_POST['name'])) $data['name'] = sanitize_text_field($_POST['name']);
	if(! empty($_POST['email'])) $data['email'] = sanitize_text_field($_POST['email']);
	if(! empty($_POST['organisation'])) $data['organisation'] = sanitize_text_field($_POST['organisation']);
	if(! empty($_POST['numpeople'])) $data['numpeople'] = sanitize_text_field($_POST['numpeople']);
	return $data;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function generateUniqueKey($length = 10){

		global $wpdb;
		$key = generateRandomString($length);
		$table_name = $wpdb->prefix . ROOMBOOKER_TABLE;
		$sql = "SELECT id FROM $table_name WHERE id_key = '$key'";
		$exists = $wpdb->get_var( $sql );
		if(!is_null($exists)) generateUniqueKey($table, $length);
	  return $key;

}

function getTimeSlots($time){
	$options = '<option value="">Select...</option>';
	for ($h = 8; $h <= 20; $h++){
		$m_start = ($time == 'end' && $h == 8) ? 30 : 0;
	  for ($m = $m_start; $m <= 30; $m+=30){
	  	$slot_time = str_pad($h, 2, '0', STR_PAD_LEFT).':'.str_pad($m, 2, '0', STR_PAD_LEFT);
	    $options .= '<option value="'.$slot_time.'">'.$slot_time.'</option>'; 
	  }
	}
	if($time != 'start'){
		$options .= '<option value="21:00">21:00</option>'; 
	}
	return $options;
}

function getLongTitle($title){
	if($title == 'Internal mtg') return 'Internal team meeting';
	elseif($title == 'Visitor mtg') return 'External visitor meeting';
	elseif($title == 'Video conf') return 'Video Conference';
	else return $title;
}

function logthis ( $log )  {
  if ( is_array( $log ) || is_object( $log ) ) {
     error_log( print_r( $log, true ) );
  } else {
     error_log( $log );
  }
}


function logthis_db ( $type, $event_id, $result, $value )  {

	global $wpdb;
	$table_name = $wpdb->prefix . ROOMBOOKER_TABLE . '_logs';

  $data['type'] = $type;
  $data['event_id'] = $event_id;
  $data['result'] = $result;
  $data['value'] = is_array($value) ? serialize($value) : $value;

	$result = $wpdb->insert( 
		$table_name, 
		$data
	);

}
