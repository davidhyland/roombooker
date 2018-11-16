<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.

 */

global $wpdb;
$table_name = $wpdb->prefix . ROOMBOOKER_TABLE;

//$postdata = (! empty($_POST['month'])) ? trim($_POST['month']) : false;
$postdata = (! empty($_POST['month'])) ? trim($_POST['month']) : 'last30';

if($postdata !== false && $postdata == 'last30'){
  //$sql_where = "AND time_start >= (NOW() - INTERVAL 1 MONTH) AND time_start < NOW()";
  $sql_where = "AND time_start >= (NOW() - INTERVAL 30 DAY) AND time_start < NOW()";
  $graph_title = "Last 30 Days";
}
elseif($postdata !== false && strpos($postdata, '|') !== false){
  $date = explode('|', $postdata);
  $year = $date[0];
  $month = $date[1];
  $sql_where = "AND MONTH(time_start) = ".sanitize_text_field($month)." AND YEAR(time_start) = ".sanitize_text_field($year);
  $graph_title = date("F Y", mktime(0, 0, 0, $month, 1, $year));
}
else{
  $sql_where = "";
  $graph_title = "Total";
}
// get months
$sql = "SELECT YEAR(time_start) as yy, MONTH(time_start) as mm
              FROM $table_name
              WHERE active = 1
              GROUP BY yy ASC, mm ASC";
$result = $wpdb->get_results( $sql );
$months = array();
if($result){
  foreach($result as $row){
    $months[] = array('y' => $row->yy, 'm' => $row->mm);
  }
}

// HOURS ROOMS BOOKED
$sql = "SELECT room, ROUND((SUM(TIME_TO_SEC(TIMEDIFF(time_end, time_start)) / 60) / 60), 1) as val
        FROM $table_name 
        WHERE active = 1 [SQLWHERE]
        GROUP by room";
$result = $wpdb->get_results( str_replace('[SQLWHERE]', $sql_where, $sql) );
$data1 = array();
if($result){
  foreach($result as $row){
    $data1[] = $row->val;
  }
}

// NUM TIMES ROOMS BOOKED
$sql = "SELECT room, COUNT(id) as val
        FROM $table_name 
        WHERE active = 1 [SQLWHERE]
        GROUP BY room 
        ORDER BY room";
$result = $wpdb->get_results( str_replace('[SQLWHERE]', $sql_where, $sql) );
$data2 = array();
if($result){
  foreach($result as $row){
    $data2[] = $row->val;
  }
}

// TOTAL PEOPLE PER ROOM
$sql = "SELECT room, SUM(numpeople) as val
        FROM $table_name 
        WHERE active = 1 [SQLWHERE]
        GROUP BY room 
        ORDER BY room";
$result = $wpdb->get_results( str_replace('[SQLWHERE]', $sql_where, $sql) );
$data3 = array();
if($result){
  foreach($result as $row){
    $data3[] = $row->val;
  }
}

// BOOKINGS PER ORGANISATION
$sql = "SELECT organisation as label, COUNT(id) as val
        FROM $table_name
        WHERE active = 1 [SQLWHERE]
        GROUP BY organisation
        ORDER BY organisation";
$result = $wpdb->get_results( str_replace('[SQLWHERE]', $sql_where, $sql) );
$data4 = array();
$data4_labels = array();
if($result){
  foreach($result as $row){
    $data4_labels[] = $row->label;
    $data4[] = $row->val;
  }
}

// BOOKINGS PER EVENT TYPE
$sql = "SELECT title as label, COUNT(id) as val
        FROM $table_name
        WHERE active = 1 [SQLWHERE]
        GROUP BY title
        ORDER BY title";
$result = $wpdb->get_results( str_replace('[SQLWHERE]', $sql_where, $sql) );
$data5 = array();
$data5_labels = array();
if($result){
  foreach($result as $row){
    $data5_labels[] = $row->label;
    $data5[] = $row->val;
  }
}

?>

 <div class="wrap roombooker">

	 <h1>JHub Room Booker: Statistics</h1>

     <form method="post" action="" style="margin-top:1em;">
      <label>Showing:</label>
      <select name="month" id="changeMonth">
        <option value="totals"<?php if($postdata == 'totals') echo ' selected="selected"' ?>>Totals</option>';
        <option value="last30"<?php if($postdata == 'last30') echo ' selected="selected"' ?>>Last 30 Days</option>';
        <?php
        foreach($months as $row):
          $selected = ($year == $row['y'] && $month == $row['m']) ? ' selected="selected"' : '';
          echo '<option value="'.$row['y'].'|'.$row['m'].'"'.$selected.'>'.date("F Y", mktime(0, 0, 0, $row['m'], 1, $row['y'])).'</option>';
        endforeach;
        ?>
      </select>
     </form>

    <section class="doughnut">
       <div class="canvas-holder">
          <canvas id="jhub-chart-1"></canvas>
       </div>

       <div class="canvas-holder">
          <canvas id="jhub-chart-2"></canvas>
       </div>

       <div class="canvas-holder">
          <canvas id="jhub-chart-3"></canvas>
       </div>
    </section>

     <section class="bar<?php if($postdata == 'totals') echo ' totals';?>">
       <div class="canvas-holder" style="height:1000px;">
          <canvas id="jhub-chart-4"></canvas>
       </div>

       <div class="canvas-holder">
          <canvas id="jhub-chart-5"></canvas>
       </div>
    </section>



 </div>


 <script type="text/javascript">

  var configDoughnut = { // DEFAULTS
      type: 'doughnut',
      data: {
          datasets: [{
              data: [],         
              backgroundColor: [
                  'blue',
                  'green',
                  'orange',
                  'red'
              ],
              label: 'jhub'
          }],
          labels: [
              "Room 1",
              "Room 2",
              "Room 3",
              "Room 4"
          ]
      },
      options: {
          responsive: true,
          legend: {
              position: 'top',
              labels: {
                fontSize: 14
              }
          },
          tooltips: {
            bodyFontSize: 12,
            callbacks: {
              label: ''
            }
          },
          title: {
              display: true,
              fontSize: 22,
              text: ''
          },
          animation: {
              animateScale: true,
              animateRotate: true
          }
      }
  };


  var configBar = {
      type: 'horizontalBar',
      height:1000,
      data: {
          labels: [],
          datasets: [{
              label: '',
              data: [],
              fill: false,
              backgroundColor: '#999',
              borderColor: '#666',
              borderWidth: 1
          }]
      },
      maintainAspectRatio: false,
      options: {
          responsive: false,
          title: {
              display: true,
              fontSize: 22,
              text: ''
          },
          scales: {
              xAxes: [{
                  ticks: {
                      "beginAtZero": true
                  }
              }],
              yAxes: [{
                barPercentage: 1  
              }]
          },

      }
  };


  var config1 = jQuery.extend(true, {}, configDoughnut);
  config1.data.datasets[0].data = [ <?php echo implode(', ', $data1) ?> ];
  config1.options.tooltips.callbacks.label = function(tooltipItem, data){ 
    return data.labels[tooltipItem.index] + ': ' + data.datasets[0].data[tooltipItem.index] + ' Hrs';
  };
  config1.options.title.text = 'Hours Booked - <?php echo $graph_title ?>';


  var config2 = jQuery.extend(true, {}, configDoughnut);
  config2.data.datasets[0].data = [ <?php echo implode(', ', $data2) ?> ];
  config2.options.tooltips.callbacks.label = function(tooltipItem, data){ 
    return data.labels[tooltipItem.index] + ': Booked ' + data.datasets[0].data[tooltipItem.index] + ' times';
  };
  config2.options.title.text = 'Times Booked - <?php echo $graph_title ?>';


  var config3 = jQuery.extend(true, {}, configDoughnut);
  config3.data.datasets[0].data = [ <?php echo implode(', ', $data3) ?> ];
  config3.options.tooltips.callbacks.label = function(tooltipItem, data){ 
    return data.labels[tooltipItem.index] + ': ' + data.datasets[0].data[tooltipItem.index] + ' people';
  };
  config3.options.title.text = 'Total People - <?php echo $graph_title ?>';


  var config4 = jQuery.extend(true, {}, configBar);
  config4.data.datasets[0].data = [ <?php echo implode(', ', $data4) ?> ];
  <?php 
  if(count($data4_labels)) echo 'config4.data.labels = [ "'.implode('", "', $data4_labels).'" ];'
  ?>
  config4.options.title.text = 'Bookings per organisation - <?php echo $graph_title ?>';

  var config5 = jQuery.extend(true, {}, configBar);
  config5.data.datasets[0].data = [ <?php echo implode(', ', $data5) ?> ];
  <?php 
  if(count($data5_labels)) echo 'config5.data.labels = [ "'.implode('", "', $data5_labels).'" ];'
  ?>  config5.options.title.text = 'Bookings per event - <?php echo $graph_title ?>';



  window.onload = function () {

    <?php if(count($data1) > 0): ?>
    var ctx1 = document.getElementById("jhub-chart-1").getContext("2d");
    window.myChart1 = new Chart(ctx1, config1);
    <?php endif; ?>

    <?php if(count($data2) > 0): ?>
    var ctx2 = document.getElementById("jhub-chart-2").getContext("2d");
    window.myChart2 = new Chart(ctx2, config2);
    <?php endif; ?>

    <?php if(count($data3) > 0): ?>
    var ctx3 = document.getElementById("jhub-chart-3").getContext("2d");
    window.myChart3 = new Chart(ctx3, config3);
    <?php endif; ?>

    <?php if(count($data4) > 0): ?>
    var ctx4 = document.getElementById("jhub-chart-4").getContext("2d");
    window.myChart4 = new Chart(ctx4, config4);
    window.myChart4.resize();
    <?php endif; ?>

    <?php if(count($data5) > 0): ?>
    var ctx5 = document.getElementById("jhub-chart-5").getContext("2d");
    window.myChart5 = new Chart(ctx5, config5);
    <?php endif; ?>


  }
  </script>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
