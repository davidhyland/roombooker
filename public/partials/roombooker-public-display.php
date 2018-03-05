<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
*/

// get current user role
$user = wp_get_current_user();
$role = $user->roles ? $user->roles[0] : false;
$is_admin_user = ($role == 'administrator' || $role == 'editor') ? true : false;

global $wpdb;
$table_name = $wpdb->prefix . ROOMBOOKER_TABLE;

// EDIT?
$do_edit = 0;
$edit_id = 0;
$edit_booking = false;
if(! empty($_GET['edit'])){
	$do_edit = 1;
	$id_key = trim($_GET['edit']);
	$edit_booking = $wpdb->get_row( "SELECT * FROM $table_name WHERE id_key = '".sanitize_text_field($id_key)."'" );
	if($edit_booking === null){
		$edit_error = 1;
	}
	else{
		$edit_error = 0;
		$edit_date = date('c', strtotime($edit_booking->time_start));
		$edit_id = $edit_booking->id;
	}

}
?>

<div id="roombooker">

	<h1>JHub Room Booker</h1>

	<div class="infobox">
		<h3>Instructions</h3>
		<h4><a href="#" title="How to book a room - click to open">Create a booking</a></h4>
		<ul style="display:none;">
			<li><i>NOTE: You cannot make a booking in Month view. This is only for viewing bookings.</i></li>
			<li>Select the room and date then drag to select the timeslot required (<i>Touch Devices: long-hold and drag</i>).</li>
			<li>Complete the details requested and click Save.</li>
			<li>You will be emailed a confirmation with a link to edit this booking.</li>
		</ul>
		<h4><a href="#" title="Update a booking - click to open">Update a booking</a></h4>
		<ul style="display:none;">
			<li><i>NOTE: You can only edit your own bookings and once you leave this page you will need to click the edit link in the confirmation email to enable editing.</i></li>
			<li>To edit the details of a booking you have made click on the booking slot to open the popup form.</li>
			<li>To change the time/room drag the booking slot to another available time/room.</li>
			<li>You can also change the end time by dragging the bottom of the booking slot.</li>
			<li>You can remove a booking by clicking on the booking slot and clicking DELETE.</li>
		</ul>
		<?php
		if($is_admin_user):
		?>
		<p class="important">As an admin user you have full editing capabilities on all bookings six months either side of today.</p>
		<?php
		endif;
		?>
	</div>

	<div id="roombooker-calendar"></div>

	<svg display="none" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="768" height="800" viewBox="0 0 768 800"><defs><g id="icon-close"><path class="path1" d="M31.708 25.708c-0-0-0-0-0-0l-9.708-9.708 9.708-9.708c0-0 0-0 0-0 0.105-0.105 0.18-0.227 0.229-0.357 0.133-0.356 0.057-0.771-0.229-1.057l-4.586-4.586c-0.286-0.286-0.702-0.361-1.057-0.229-0.13 0.048-0.252 0.124-0.357 0.228 0 0-0 0-0 0l-9.708 9.708-9.708-9.708c-0-0-0-0-0-0-0.105-0.104-0.227-0.18-0.357-0.228-0.356-0.133-0.771-0.057-1.057 0.229l-4.586 4.586c-0.286 0.286-0.361 0.702-0.229 1.057 0.049 0.13 0.124 0.252 0.229 0.357 0 0 0 0 0 0l9.708 9.708-9.708 9.708c-0 0-0 0-0 0-0.104 0.105-0.18 0.227-0.229 0.357-0.133 0.355-0.057 0.771 0.229 1.057l4.586 4.586c0.286 0.286 0.702 0.361 1.057 0.229 0.13-0.049 0.252-0.124 0.357-0.229 0-0 0-0 0-0l9.708-9.708 9.708 9.708c0 0 0 0 0 0 0.105 0.105 0.227 0.18 0.357 0.229 0.356 0.133 0.771 0.057 1.057-0.229l4.586-4.586c0.286-0.286 0.362-0.702 0.229-1.057-0.049-0.13-0.124-0.252-0.229-0.357z"></path></g></defs></svg>


	<div class="modal">
	  <div class="modal-overlay modal-toggle"></div>
	  <div class="modal-wrapper modal-transition">
	    <div class="modal-header">
	      <button class="modal-close modal-toggle"><svg class="icon-close icon" viewBox="0 0 32 32"><use xlink:href="#icon-close"></use></svg></button>
	      <h2 class="modal-heading">JHub Room Booker</h2>
	    </div>
	    <div class="modal-body">
	      <div class="modal-content clearfix">
	        <form id="formRoomBooker">
	        	<div style="display:none;">
	        		<input type="hidden" name="fldId" value=">">
	        		<input type="hidden" name="fldRoom" value="">
	        	</div>
	        	<div>
	        		<label>Room:</label>
	        		<select id="roomSelect" style="display:none;">
	        			<option value="">Select room...</option>
	        		</select>
	        	</div>
	        	<div>
	        		<label>Date:</label>
	        		<span id="txtDate"></span>
	        	</div>
	        	<div class="time">
	        		<label>Time:</label>
	        		<select name="fldStartTime" validation="required">
	        			<?php echo getTimeSlots('start'); ?>
	        		</select>
	        		<span> to </span>
	        		<select name="fldEndTime" validation="required">
	        			<?php echo getTimeSlots('end'); ?>
	        		</select>
	        		<!--<span id="txtStart"></span> &ndash; <span id="txtEnd"></span>-->
	        	</div>
	        	<div>
	        		<span class="important">* All fields are required</span>
	        	</div>
	        	<div>
	        		<label>Event Type:</label>
	        		<select name="fldType" validation="required">
	        			<option value="">Select...</option>
	        			<option value="Internal mtg">Internal team meeting</option>
	        			<option value="Visitor mtg">External visitor meeting</option>
	        			<option value="Phone call">Phone call</option>
	        			<option value="Video conf">Video Conference</option>
	        			<option value="Other">Other</option>
	        		</select>
	        	</div>
	        	<div class="other" style="display:none;">
	        		<label>Other type: </label>
	        		<input type="text" name="fldTypeOther" maxlength="50">
	        	</div>
	        	<div>
	        		<label>Your Name:</label>
	        		<input type="text" name="fldName" validation="required" maxlength="50" value="">
	        	</div>
	        	<div>
	        		<label>Organisation:</label>
	        		<input type="text" name="fldOrganisation" validation="required" maxlength="50" >
	        	</div>
	        	<div>
	        		<label>Num. people:</label>
	        		<select name="fldNumPeople" validation="required">
	        			<option value="">Select...</option>
	        		</select>
	        	</div>
	        	<div>
	        		<label>Your Email:</label>
	        		<input type="text" name="fldEmail" validation="required email"  maxlength="50">
	        	</div>
	        	<div>
	        		<label>Confirm Email:</label>
	        		<input type="text" name="fldEmailConfirm" validation="required email_confirm"  maxlength="50">
	        	</div>
	        	<div class="buttons">
	        		<input type="reset" id="btnCancel" value="Cancel">
	        		<input type="submit" id="btnSave" value="Save" style="display:none;">
	        		<input type="button" id="btnDelete" value="Delete" style="display:none;">
	        		<input type="submit" id="btnUpdate" value="Update" style="display:none;">
	        	</div>
	        </form>
	      </div>
	    </div>
	  </div>
	</div>

</div> <!-- // roombooker -->

<script>
<?php
$iau = ($is_admin_user) ? '642105684' : '20588664';
echo "var jhub = '$iau'; ";
echo "var jhubUrl = '".get_permalink()."'; ";
echo "var jhubEdit = $do_edit; ";
if($do_edit){
	echo "var jhubEditError = $edit_error; ";
	if($edit_error == 0) {
		echo "var jhubEditId = $edit_id; ";
		echo "var jhubEditDate = '".date('Y-m-d', strtotime($edit_date))."'; ";
	}
}

$editable = ($is_admin_user) ? 'true' : 'false';
$sql_dates = ($is_admin_user) ? 'DATE(time_start) >= DATE_SUB(NOW(), INTERVAL 6 MONTH)' : 'DATE(time_start) >= DATE(NOW())';
$results = $wpdb->get_results("SELECT * FROM $table_name WHERE active = 1 AND $sql_dates ORDER BY time_start ASC");
if($results):
	$output = '';
	$output .= 'var eventData = [ ';
	foreach($results as $booking):
			$output .= '{ id: "'.$booking->id.'", ';
			//$output .= 'id_key: "'.$booking->id_key.'", ';
			$output .= 'resourceId: "'.$booking->room.'", ';
			$output .= ($do_edit && $edit_id == $booking->id) ? 'editable: true, ' : 'editable: '.$editable.', ';
			$output .= 'title: "'.$booking->title.'", ';
			$output .= 'longtitle: "'.getLongTitle($booking->title).'", ';
			$output .= 'start: "'.date('c', strtotime($booking->time_start)).'", ';
			$output .= 'end: "'.date('c', strtotime($booking->time_end)).'", ';
			$output .= 'name: "'.$booking->name.'", ';
			$output .= 'organisation: "'.$booking->organisation.'", ';
			$output .= 'numpeople: "'.$booking->numpeople.'", ';
			$output .= 'email: "'.$booking->email.'" },';
	endforeach;
	$output .= ' ];';
	echo $output;
else:
	echo 'var eventData = [];';
endif;
?>

</script>

