<?php
/*
Plugin Name: XllenTech English Islamic Calendar
Plugin URI: http://www.xllentech.com
Description: Display Calendar on your website with English and Islamic dates.
Version: 2.0.1
Author: Abbas Momin
Author URI: http://www.xllentech.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
// Database creation on activation 
	function xllentech_calendar_install() {
		global $wpdb;

		$table1_name = $wpdb->prefix . 'month_days'; 
		$table2_name = $wpdb->prefix . 'month_firstdate'; 
		
		$charset_collate = $wpdb->get_charset_collate();

			$sql1 = "CREATE TABLE $table1_name (
				month_number int(2) NOT NULL,
	  			year_number int(5) NOT NULL,
	  			days int(2) NOT NULL,
	  			PRIMARY KEY  (month_number,year_number)
			) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql1);

			$sql2 = "CREATE TABLE $table2_name (
				english_month int(2) NOT NULL,
				english_year int(4) NOT NULL,
				islamic_day int(2) NOT NULL,
				islamic_month int(2) NOT NULL,
				islamic_year int(4) NOT NULL,
				PRIMARY KEY  (english_month,english_year)
			) $charset_collate;";
				dbDelta($sql2);
	}
	function xllentech_calendar_install_data() {
		global $wpdb;

		$english_month=6;
		$english_year=2016;
		$islamic_day=25;
		$islamic_month=8;
		$islamic_year=1437;
		
		$table2_name = $wpdb->prefix."month_firstdate";
		
		$wpdb->insert( 
			$table2_name, 
				array( 
					'english_month' => $english_month, 
					'english_year' => $english_year, 
					'islamic_day' => $islamic_day, 
					'islamic_month' => $islamic_month, 
					'islamic_year' => $islamic_year,
				) 
		);
	}
	register_activation_hook( __FILE__, 'xllentech_calendar_install' );
	register_activation_hook( __FILE__, 'xllentech_calendar_install_data' );

/** Clear options, databse tables if UNINSTALL */
register_uninstall_hook(__FILE__, 'xllentech_calendar_uninstall');
function xllentech_calendar_uninstall(){
	global $wpdb;

	$month_days_table = $wpdb->prefix . 'month_days'; 
	$month_firstdate_table = $wpdb->prefix . 'month_firstdate';
	delete_option('xc_options');
 
	$wpdb->query("DROP TABLE IF EXISTS $month_days_table");
	$wpdb->query("DROP TABLE IF EXISTS $month_firstdate_table");
	if($wpdb->last_error != '') {
		$wpdb->print_error();
	}
}


class Xllentech_Calendar_Plugin extends WP_Widget {
	// constructor
	public function __construct() {
		/* ... */
		parent::__construct('xllentech_calendar_plugin',__('XllenTech Calendar', 'xllentech_widget_plugin'),
		array( 'description' => __( 'XllenTech Calendar', 'xllentech_widget_plugin' ), ) ); // Args
	}
	// widget form creation
function form($instance) {
			// Check values
			if( $instance) {
			     $title = esc_attr($instance['title']);
			 //    $text = esc_attr($instance['text']);
			  //   $textarea = esc_textarea($instance['textarea']);
			} else {
			     $title = '';
			  //   $text = '';
			  //   $textarea = '';
			}
?><p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p><?php
		}
	
// update widget
function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	// display widget
function widget($args, $instance) {
	global $wpdb;
	
   extract( $args );
   // these are the widget options
   $title = apply_filters('widget_title', $instance['title']);
  // $text = $instance['text'];
  // $textarea = $instance['textarea'];
   echo $before_widget;
   // Display the widget
   
  // echo '<div class="widget-text wp_widget_plugin_box">';

   // Check if title is set
   if ( $title ) {
      echo $before_title . $title . $after_title;
   }
//include php file with islamic month names and days
//include( plugin_dir_path( __FILE__ ) . 'xllentech-calendar-data.php');

	$month_days_table = $wpdb->prefix . 'month_days'; 
	$month_firstdate_table = $wpdb->prefix . 'month_firstdate';
	
	$xc_options = get_option("xc_options");
	if (!is_array($xc_options)) {
		$calendar_admin_email=get_option('admin_email');
		$xc_options = array(
		"islamic_months" => "Islamic Months,Muharram,Safar,Rabi'al Awwal,Rabi'al Thani,Jamaada'al Ula,Jamaada'al Thani,Rajab,Sha'ban,Ramadhan,Shawaal,Zul Qa'dah,Zul Hijjah",
		"islamic_month_days" => "12,30,29,30,29,30,29,30,29,30,29,30,29",
		"calendar_email_choice" => "No",
		"calendar_admin_email" => $calendar_admin_email,
		"days_email_sent" => "0",
		"xc_time_zone" => "America/Denver",
		"xc_page_pin" => "1234" );
		update_option("xc_options",$xc_options);
	}

	if(!isset($xc_options['xc_page_pin'])) {
		$calendar_admin_email=get_option('admin_email');
		$xc_options_new = array(
		"islamic_months" => $xc_options['islamic_months'],
		"islamic_month_days" => $xc_options['islamic_month_days'],
		"calendar_email_choice" => "No",
		"calendar_admin_email" => $calendar_admin_email,
		"days_email_sent" => "0",
		"xc_time_zone" => "America/Denver",
		"xc_page_pin" => "1234" );
		update_option("xc_options",$xc_options_new);
	}


$islamic_months = explode(",", $xc_options['islamic_months']);
$islamic_month_days = explode(",", $xc_options['islamic_month_days']);

	$english_currentdate=new DateTime('NOW', new DateTimeZone($xc_options["xc_time_zone"]));
	$english_currentmonth=date_format($english_currentdate,'n');
	$english_currentyear=date_format($english_currentdate,'Y');
	
	$english_currentdate=date_create('1-'.$english_currentmonth.'-'.$english_currentyear);
	
	$query="SELECT islamic_day,islamic_month,islamic_year FROM ".$month_firstdate_table." WHERE english_year=".$english_currentyear." and english_month=".$english_currentmonth;
	
	$islamic_date_data = $wpdb->get_results($query);

	if(count($islamic_date_data)<=0) {

//If existing english month has no islamic first date in database, make new from previous month
$english_last_month = clone $english_currentdate;
$english_last_month->modify( '-1 month' );

$english_previous_monthdays=date_format($english_last_month,'t');
$english_previousyear=date_format($english_last_month,'Y');
$english_previousmonth=date_format($english_last_month,'n');

				
		$islamic_date_data = $wpdb->get_results("SELECT islamic_day,islamic_month,islamic_year FROM $month_firstdate_table WHERE english_year=".$english_previousyear." and english_month=".$english_previousmonth);

		foreach( $islamic_date_data as $result_data ) {
			$islamic_previousday=$result_data->islamic_day;
			$islamic_previousmonth=$result_data->islamic_month;
			$islamic_previousyear=$result_data->islamic_year;
		}
			$month_data = $wpdb->get_results("SELECT days FROM $month_days_table " .
    "WHERE year_number=".$islamic_previousyear ." and month_number=".$islamic_previousmonth);
			if(count($month_data)>0) {    
			   	foreach( $month_data as $islamic_date_data ) {
				$islamic_month_days[$islamic_previousmonth]=$islamic_date_data->days;
				}
 			}

		//NEW FORMULA START ************
		
		$newday=$islamic_previousday+$english_previous_monthdays-$islamic_month_days[$islamic_previousmonth];
		$newmonth=$islamic_previousmonth+1;
		$newyear=$islamic_previousyear;
			if($newmonth>12){
					$newyear++;
					$newmonth=1;
			}
		if($newday>28){
			$month_data = $wpdb->get_results("SELECT days FROM $month_days_table " .
    "WHERE year_number=".$newyear ." and month_number=".$newmonth);
					if(count($month_data)>0) {    
					   	foreach( $month_data as $islamic_date_data ) {
						$islamic_month_days[$newmonth]=$islamic_date_data->days;
						}
		 			}
		}
		if($newday>$islamic_month_days[$newmonth]) {
			$newday=$newday-$islamic_month_days[$newmonth];
			$newmonth++;
			if($newmonth>12){
				$newyear++;
				$newmonth=1;
			}
		}
		//NEW FORMULA END ************

		$wpdb->insert($month_firstdate_table, array("english_month" => $english_currentmonth, "english_year" => $english_currentyear, "islamic_day" => $newday, "islamic_month" => $newmonth, "islamic_year" => $newyear ), array("%d", "%d", "%d"));
			$islamic_day=$newday;
			$islamic_month=$newmonth;
			$islamic_year=$newyear;
	}
	else {
		foreach( $islamic_date_data as $result_data ) {
			$islamic_firstday=$result_data->islamic_day;
			$islamic_firstmonth=$result_data->islamic_month;
			$islamic_firstyear=$result_data->islamic_year;
		}
	}   

$english_current_monthname = date_format($english_currentdate,'F');
$english_current_monthdays=date_format($english_currentdate,'t');

$english_current_dayname=date_format($english_currentdate,'D');
$english_current_firstday=date_format($english_currentdate,'N');
$english_current_firstday=$english_current_firstday-1;

$english_last_month = clone $english_currentdate;
$english_last_month->modify( '-1 month' );

$english_previous_monthdays=date_format($english_last_month,'t');

$l=$english_previous_monthdays-$english_current_firstday+1;

//Find Islamic date first day by rewinding by english first day count
	$islamic_grid_firstday=$islamic_firstday;
	$islamic_grid_firstmonth=$islamic_firstmonth;
	$islamic_grid_firstyear=$islamic_firstyear;
	
// NEW FORMULA START
$islamic_grid_firstday=$islamic_firstday-$english_current_firstday;
if($islamic_grid_firstday<=0) {
	$islamic_grid_firstmonth--;
	if($islamic_grid_firstmonth<0){
		$islamic_grid_firstmonth=12;
		$islamic_grid_firstyear--;
	}
	$month_data = $wpdb->get_results("SELECT days FROM $month_days_table " .
   "WHERE year_number=".$islamic_grid_firstyear ." and month_number=".$islamic_grid_firstmonth);
	if(count($month_data)>0) {    
	   	foreach( $month_data as $islamic_date_data ) {
		$islamic_month_days[$islamic_grid_firstmonth]=$islamic_date_data->days;
		}
	}
$islamic_grid_firstday=$islamic_grid_firstday+$islamic_month_days[$islamic_grid_firstmonth];
}
else {
	$month_data = $wpdb->get_results("SELECT days FROM $month_days_table " .
    "WHERE year_number=".$islamic_firstyear ." and month_number=".$islamic_firstmonth);
		if(count($month_data)>0) {    
		   	foreach( $month_data as $islamic_date_data ) {
			$islamic_month_days[$islamic_firstmonth]=$islamic_date_data->days;
			}
		}
}
//NEW FORMULA END
// End of the search

	$k1=$islamic_grid_firstday;
	$islamic_currentmonth=$islamic_grid_firstmonth;
	$islamic_currentyear=$islamic_grid_firstyear;
	$islamic_css_current="xllentech-islamic-1";
			
for ($i=0; $i<$english_current_firstday; $i++) {
	
	$xllentech_english_css[$i]="xllentech-english-previous";
	$english_day_sequence[$i]=$l;
	$l++;
		
	If ($k1>$islamic_month_days[$islamic_currentmonth]){
		$k1=1;
		$islamic_currentmonth=$islamic_currentmonth+1;
		
		if ($islamic_currentmonth>12){
			$islamic_currentmonth=1;
			$islamic_currentyear=$islamic_currentyear+1;
		}
		$month_data = $wpdb->get_results("SELECT days FROM $month_days_table " .
    "WHERE year_number=".$islamic_currentyear ." and month_number=".$islamic_currentmonth);
		if(count($month_data)>0) {    
		   	foreach( $month_data as $islamic_date_data ) {
			$islamic_month_days[$islamic_currentmonth]=$islamic_date_data->days;
			}
		}
 			
		if ($islamic_css_current=="xllentech-islamic-1") {
			$islamic_css_current="xllentech-islamic-2";	
		}
		else {
			$islamic_css_current="xllentech-islamic-1";
		}
		
		$xllentech_islamic_css[$i]="xllentech-islamic-3";
		
		$islamic_day_sequence[$i]=$islamic_months[$islamic_currentmonth]." " .$k1;
		
	}
	else{
		
		$xllentech_islamic_css[$i]=$islamic_css_current;
		$islamic_day_sequence[$i]=$k1;
	}

	$k1++;
	
}

$n=$english_current_monthdays+$english_current_firstday;
$j=1; 

for ($i=$english_current_firstday; $i<$n; $i++) {
	
	$xllentech_english_css[$i]="xllentech-english-current";
 	$english_day_sequence[$i]=$j;
	$j++;
	
	If ($k1>$islamic_month_days[$islamic_currentmonth]){
		$k1=1;
		$islamic_currentmonth=$islamic_currentmonth+1;
			if ($islamic_currentmonth>12){
				$islamic_currentmonth=1;
				$islamic_currentyear=$islamic_currentyear+1;
			}
			$month_data = $wpdb->get_results("SELECT days FROM $month_days_table " .
    "WHERE year_number=".$islamic_currentyear ." and month_number=".$islamic_currentmonth);
			if(count($month_data)>0) {    
			   	foreach( $month_data as $islamic_date_data ) {
				$islamic_month_days[$islamic_currentmonth]=$islamic_date_data->days;
				}
 			}
		if ($islamic_css_current=="xllentech-islamic-1") {
			$islamic_css_current="xllentech-islamic-2";	
		}
		else {
			$islamic_css_current="xllentech-islamic-1";
		}
		
		$xllentech_islamic_css[$i]="xllentech-islamic-3";	
		
		$islamic_day_sequence[$i]=$islamic_months[$islamic_currentmonth]." " .$k1;
	}
	else{
		
		$xllentech_islamic_css[$i]=$islamic_css_current;
		$islamic_day_sequence[$i]=$k1;
	}

	$k1++;

}

$m=1;

for ($q=$n; $q<35; $q++) {
	
	$xllentech_english_css[$q]="xllentech-english-next";
	$english_day_sequence[$q]=$m;
	$m++;

	If ($k1>$islamic_month_days[$islamic_currentmonth]){
		$k1=1;
		$islamic_currentmonth=$islamic_currentmonth+1;
			if ($islamic_currentmonth>12){
				$islamic_currentmonth=1;
				$islamic_currentyear=$islamic_currentyear+1;
			}
		if ($islamic_css_current=="xllentech-islamic-1") {
			$islamic_css_current="xllentech-islamic-2";	
		}
		else {
			$islamic_css_current="xllentech-islamic-1";
		}
		
		$xllentech_islamic_css[$i]="xllentech-islamic-3";		
			
		$islamic_day_sequence[$q]=$islamic_months[$islamic_currentmonth]." " .$k1;
	}
	else{
		
		$xllentech_islamic_css[$q]=$islamic_css_current;
		$islamic_day_sequence[$q]=$k1;
	}

	$k1++;

}

// OPTIONAL BOTTOM ROW 6TH
if ( ($english_current_firstday==5 && $english_current_monthdays>30) || ($english_current_firstday==6 && $english_current_monthdays>=30)) {

	$m=1;

	for ($i=$n; $i<42; $i++) {
		
		$xllentech_english_css[$i]="xllentech-english-next";
		$english_day_sequence[$i]=$m;
		$m++;
		
		If ($k1>$islamic_month_days[$islamic_currentmonth]){
			$k1=1;
			$islamic_currentmonth=$islamic_currentmonth+1;
				if ($islamic_currentmonth>12){
					$islamic_currentmonth=1;
					$islamic_currentyear=$islamic_currentyear+1;
				}
			
			if ($islamic_css_current=="xllentech-islamic-1") {
			$islamic_css_current="xllentech-islamic-2";	
		}
		else {
			$islamic_css_current="xllentech-islamic-1";
		}
		
			$xllentech_islamic_css[$i]="xllentech-islamic-3";
			
			$islamic_day_sequence[$i]=$islamic_months[$islamic_currentmonth]." " .$k1;
		}
		else{
			$xllentech_islamic_css[$i]=$islamic_css_current;
			$islamic_day_sequence[$i]=$k1;
		}
		$k1++;
	}
}
  
   			echo "<div class='xllentech-calendar-widget'>
		<table border='1'>
		<thead>
			<tr class='xllentech-main-nav'>
				<td colspan='7'>
					<div class='xllentech-month-names'>
						<span class='xllentech-english-month'>".$english_current_monthname ." ".$english_currentyear."</span>
						<span class='xllentech-islamic-month'>".$islamic_months[$islamic_firstmonth]." ".$islamic_firstyear."</span>
					</div>
				</td>
			</tr>
			<tr class='xllentech-daynames'>
					<th width='14%'>Mon</th>
					<th width='14%'>Tue</th>
					<th width='14%'>Wed</th>
					<th width='14%'>Thu</th>
					<th width='14%'>Fri</th>
					<th width='14%'>Sat</th>
					<th width='14%'>Sun</th>
			</tr>
		</thead>
	<tbody>
		<tr>
			<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[0].">".$english_day_sequence[0]."</span>
			<span class=".$xllentech_islamic_css[0].">".$islamic_day_sequence[0]."</span></div></td>
			
			<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[1].">".$english_day_sequence[1]."</span>
			<span class=".$xllentech_islamic_css[1].">".$islamic_day_sequence[1]."</span></div></td>
			
			<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[2].">".$english_day_sequence[2]."</span>
			<span class=".$xllentech_islamic_css[2].">".$islamic_day_sequence[2]."</span></div></td>

		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[3].">".$english_day_sequence[3]."</span>
			<span class=".$xllentech_islamic_css[3].">".$islamic_day_sequence[3]."</span> </div></td>

		<td><div class='xllentech-daybox'>
		<span class=".$xllentech_english_css[4].">".$english_day_sequence[4]."</span>
		<span class=".$xllentech_islamic_css[4].">".$islamic_day_sequence[4]."</span></div></td>

		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[5].">".$english_day_sequence[5]."</span>
			<span class=".$xllentech_islamic_css[5].">".$islamic_day_sequence[5]."</span></div></td>

		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[6].">".$english_day_sequence[6]."</span>
			<span class=".$xllentech_islamic_css[6].">".$islamic_day_sequence[6]."</span></div></td>
			
</tr>
			
<tr>
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[7].">".$english_day_sequence[7]."</span>
			<span class=".$xllentech_islamic_css[7].">".$islamic_day_sequence[7]."</span></div></td>

		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[8].">".$english_day_sequence[8]."</span>
			<span class=".$xllentech_islamic_css[8].">".$islamic_day_sequence[8]."</span></div></td>

		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[9].">".$english_day_sequence[9]."</span>
			<span class=".$xllentech_islamic_css[9].">".$islamic_day_sequence[9]."</span></div></td>
		
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[10].">".$english_day_sequence[10]."</span>
			<span class=".$xllentech_islamic_css[10].">".$islamic_day_sequence[10]."</span></div></td>
				
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[11].">".$english_day_sequence[11]."</span>
			<span class=".$xllentech_islamic_css[11].">".$islamic_day_sequence[11]."</span></div></td>
		
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[12].">".$english_day_sequence[12]."</span>
			<span class=".$xllentech_islamic_css[12].">".$islamic_day_sequence[12]."</span></div></td>
						
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[13].">".$english_day_sequence[13]."</span>
			<span class=".$xllentech_islamic_css[13].">".$islamic_day_sequence[13]."</span></div></td>
										
</tr>
<tr>
								
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[14].">".$english_day_sequence[14]."</span>
			<span class=".$xllentech_islamic_css[14].">".$islamic_day_sequence[14]."</span></div></td>
			
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[15].">".$english_day_sequence[15]."</span>
			<span class=".$xllentech_islamic_css[15].">".$islamic_day_sequence[15]."</span></div></td>
			
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[16].">".$english_day_sequence[16]."</span>
			<span class=".$xllentech_islamic_css[16].">".$islamic_day_sequence[16]."</span></div></td>
			
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[17].">".$english_day_sequence[17]."</span>
			<span class=".$xllentech_islamic_css[17].">".$islamic_day_sequence[17]."</span></div></td>
						
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[18].">".$english_day_sequence[18]."</span>
			<span class=".$xllentech_islamic_css[18].">".$islamic_day_sequence[18]."</span></div></td>
			
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[19].">".$english_day_sequence[19]."</span>
			<span class=".$xllentech_islamic_css[19].">".$islamic_day_sequence[19]."</span></div></td>
			
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[20].">".$english_day_sequence[20]."</span>
			<span class=".$xllentech_islamic_css[20].">".$islamic_day_sequence[20]."</span></div></td>

</tr>
<tr>
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[21].">".$english_day_sequence[21]."</span>
			<span class=".$xllentech_islamic_css[21].">".$islamic_day_sequence[21]."</span></div></td>
						
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[22].">".$english_day_sequence[22]."</span>
			<span class=".$xllentech_islamic_css[22].">".$islamic_day_sequence[22]."</span></div></td>
						
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[23].">".$english_day_sequence[23]."</span>
			<span class=".$xllentech_islamic_css[23].">".$islamic_day_sequence[23]."</span></div></td>
			
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[24].">".$english_day_sequence[24]."</span>
			<span class=".$xllentech_islamic_css[24].">".$islamic_day_sequence[24]."</span></div></td>
					
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[25].">".$english_day_sequence[25]."</span>
			<span class=".$xllentech_islamic_css[25].">".$islamic_day_sequence[25]."</span></div></td>
					
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[26].">".$english_day_sequence[26]."</span>
			<span class=".$xllentech_islamic_css[26].">".$islamic_day_sequence[26]."</span></div></td>
					
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[27].">".$english_day_sequence[27]."</span>
			<span class=".$xllentech_islamic_css[27].">".$islamic_day_sequence[27]."</span></div></td>
						
</tr>
<tr>
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[28].">".$english_day_sequence[28]."</span>
			<span class=".$xllentech_islamic_css[28].">".$islamic_day_sequence[28]."</span></div></td>
									
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[29].">".$english_day_sequence[29]."</span>
			<span class=".$xllentech_islamic_css[29].">".$islamic_day_sequence[29]."</span></div></td>
					
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[30].">".$english_day_sequence[30]."</span>
			<span class=".$xllentech_islamic_css[30].">".$islamic_day_sequence[30]."</span></div></td>
					
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[31].">".$english_day_sequence[31]."</span>
			<span class=".$xllentech_islamic_css[31].">".$islamic_day_sequence[31]."</span></div></td>
					
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[32].">".$english_day_sequence[32]."</span>
			<span class=".$xllentech_islamic_css[32].">".$islamic_day_sequence[32]."</span></div></td>
					
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[33].">".$english_day_sequence[33]."</span>
			<span class=".$xllentech_islamic_css[33].">".$islamic_day_sequence[33]."</span></div></td>
						
		<td><div class='xllentech-daybox'>
			<span class=".$xllentech_english_css[34].">".$english_day_sequence[34]."</span>
			<span class=".$xllentech_islamic_css[34].">".$islamic_day_sequence[34]."</span></div></td></tr>";

if ( ($english_current_firstday==5 && $english_current_monthdays>30) || ($english_current_firstday==6 && $english_current_monthdays>=30)) {


	echo '<tr><td>
		<div class="xllentech-daybox">
		<span class="'.$xllentech_english_css[35] .'">'.$english_day_sequence[35].'</span>
		<span class="'.$xllentech_islamic_css[35] .'">'.$islamic_day_sequence[35].'</span>	</div>
		</td><td><div class="xllentech-daybox">
		<span class="'.$xllentech_english_css[36] .'">'.$english_day_sequence[36].'</span>
		<span class="'.$xllentech_islamic_css[36] .'">'.$islamic_day_sequence[36].'</span>	</div>	
		</td><td><div class="xllentech-daybox">
		<span class="'.$xllentech_english_css[37] .'">'.$english_day_sequence[37].'</span>
		<span class="'.$xllentech_islamic_css[37] .'">'.$islamic_day_sequence[37].'</span>	</div>
		</td><td><div class="xllentech-daybox">
		<span class="'.$xllentech_english_css[38] .'">'.$english_day_sequence[38].'</span>
		<span class="'.$xllentech_islamic_css[38] .'">'.$islamic_day_sequence[38].'</span>	</div>
		</td><td><div class="xllentech-daybox">
		<span class="'.$xllentech_english_css[39].'">'.$english_day_sequence[39].'</span>
		<span class="'.$xllentech_islamic_css[39].'">'.$islamic_day_sequence[39].'</span>	</div>	
		</td><td><div class="xllentech-daybox">
		<span class="'.$xllentech_english_css[40] .'">'.$english_day_sequence[40].'</span>
		<span class="'.$xllentech_islamic_css[40] .'">'.$islamic_day_sequence[40].'</span>	</div>
		</td><td><div class="xllentech-daybox">
		<span class="'.$xllentech_english_css[41] .'">'.$english_day_sequence[41].'</span>
		<span class="'.$xllentech_islamic_css[41] .'">'.$islamic_day_sequence[41].'</span>	</div>
		</td></tr>';
}
echo "</tbody></table></div>";


   echo $after_widget;
	}
}
/*Shortcode code Starts */
	function xllentech_calendar_shortcode(){
		global $wp_widget_factory;
		$my_widget_name="Xllentech_Calendar_Plugin";
		$my_widget_id="";
		
		if (!is_a($wp_widget_factory->widgets[$my_widget_name], 'WP_Widget')):
        	$wp_class = 'WP_Widget_'.ucwords(strtolower($class));
	        if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):
	            return '<p>'.sprintf(__("%s: Widget class not found. Make sure this widget exists and the class name is correct"),'<strong>'.$class.'</strong>').'</p>';
	        else:
	            $class = $wp_class;
	        endif;
    	endif;
    
	    ob_start();
	    the_widget($my_widget_name, $instance, array('widget_id'=>'arbitrary-instance-'.$id,
	        'before_widget' => '',
	        'after_widget' => '',
	        'before_title' => '',
	        'after_title' => ''
	    ));
	    $output = ob_get_contents();
	}
	add_shortcode('xcalendar', 'xllentech_calendar_shortcode');
/*Shortcode code Ends */

class Xllentech_Islamic_Today extends WP_Widget {

	// constructor
	public function __construct() {
		/* ... */
		parent::__construct('xllentech_islamic_today_plugin',__('XllenTech Today', 'xllentech_islamic_today_plugin'),
		array( 'description' => __( 'XllenTech Today', 'xllentech_islamic_today_plugin'), ) ); // Args
	}

	// widget form creation
function form($instance) {

			// Check values
			if( $instance) {
			     $title = esc_attr($instance['title']);
			} else {
			     $title = '';
			}
			?>

			<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>

			<?php
		}
	/*
	This code is simply adding 3 fields to the widget. The first one is the widget title, the second a text field, and the last one is a textarea. Let’s see now how to save and update each field value with the update() function.
	*/

// update widget
function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
     return $instance;
	}

	// display widget
function widget($args, $instance) {
	global $wpdb;
   extract( $args );
   // these are the widget options
   $title = apply_filters('widget_title', $instance['title']);
   
   echo $before_widget;
   // Display the widget

   // Check if title is set
   if ( $title ) {
      echo $before_title . $title . $after_title;
   }
	
//include php file with islamic month names and days
//include( plugin_dir_path( __FILE__ ) . 'xllentech-calendar-plugin-data.php');

	$xc_options = get_option("xc_options");
	if (!is_array($xc_options)) {
		$calendar_admin_email=get_option('admin_email');
		$xc_options = array(
		"islamic_months" => "Islamic Months,Muharram,Safar,Rabi'al Awwal,Rabi'al Thani,Jamaada'al Ula,Jamaada'al Thani,Rajab,Sha'ban,Ramadhan,Shawaal,Zul Qa'dah,Zul Hijjah",
		"islamic_month_days" => "12,30,29,30,29,30,29,30,29,30,29,30,29",
		"calendar_email_choice" => "No",
		"calendar_admin_email" => $calendar_admin_email,
		"days_email_sent" => "0",
		"xc_time_zone" => "America/Denver",
		"xc_page_pin" => "1234" );
		update_option("xc_options",$xc_options);
	}

	if(!isset($xc_options['xc_page_pin'])) {
		$calendar_admin_email=get_option('admin_email');
		$xc_options_new = array(
		"islamic_months" => $xc_options['islamic_months'],
		"islamic_month_days" => $xc_options['islamic_month_days'],
		"calendar_email_choice" => "No",
		"calendar_admin_email" => $calendar_admin_email,
		"days_email_sent" => "0",
		"xc_time_zone" => "America/Denver",
		"xc_page_pin" => "1234" );
		update_option("xc_options",$xc_options_new);
	}


$islamic_months = explode(",", $xc_options['islamic_months']);
$islamic_month_days = explode(",", $xc_options['islamic_month_days']);

	$english_currentdate=new DateTime('NOW', new DateTimeZone($xc_options["xc_time_zone"]));
	$english_currentday=date_format($english_currentdate,'j');
	$english_currentmonth=date_format($english_currentdate,'n');
	$english_currentyear=date_format($english_currentdate,'Y');

	$english_currentmonth_days=date_format($english_currentdate,'t');
	$english_currentmonth_name=date_format($english_currentdate,'M');
	
	$month_days_table = $wpdb->prefix . 'month_days'; 
	$month_firstdate_table = $wpdb->prefix . 'month_firstdate';
	
	$query="SELECT islamic_day,islamic_month,islamic_year FROM $month_firstdate_table WHERE english_year=".$english_currentyear." and english_month=".$english_currentmonth;
	
	$islamic_date_data = $wpdb->get_results($query);

	foreach( $islamic_date_data as $results ) {
			$islamic_day=$islamic_date_data[0]->islamic_day;
			$islamic_month=$islamic_date_data[0]->islamic_month;
			$islamic_year=$islamic_date_data[0]->islamic_year;
	}
		
		
 	
 	$day=$islamic_day+($english_currentday-1);
 	if($day>28) {
		$month_data = $wpdb->get_results("SELECT days FROM $month_days_table " .
    "WHERE year_number=".$islamic_year ." and month_number=".$islamic_month);
    		if(count($month_data)>0) {
				foreach( $month_data as $results ) {
					$islamic_month_days[$islamic_month]=$month_data[0]->days;
				}    
 			}
	}
 	if($day>$islamic_month_days[$islamic_month]){
		$day=$day-$islamic_month_days[$islamic_month];
		$islamic_month++;
		if($islamic_month>12){
			$islamic_month=1;
			$islamic_year++;
		}
		if($day>28) {
		$month_data = $wpdb->get_results("SELECT days FROM $month_days_table " .
    "WHERE year_number=".$islamic_year ." and month_number=".$islamic_month);
    		if(count($month_data)>0) {
				foreach( $month_data as $results ) {
					$islamic_month_days[$islamic_month]=$month_data[0]->days;
				}    
 			}
		}
		if($day>$islamic_month_days[$islamic_month]) {
			$day=$day-$islamic_month_days[$islamic_month];
			$islamic_month++;
			if($islamic_month>12){
				$islamic_month=1;
				$islamic_year++;
			}
		}
	}

	echo "<div class='xllentech-islamic-today'><span class='xllentech-english-date'>";
	echo "$english_currentday-$english_currentmonth_name-$english_currentyear</span>";
	echo "<span class='xllentech-islamic-date'>$day-".$islamic_months[$islamic_month]."-$islamic_year";
	echo "</span></div>";
	echo $after_widget;
	
	//echo $xc_options['days_email_sent'];
	
	if( $xc_options['days_email_sent']=="0" && $day>=29 ){
		$site_url=get_site_url();
		$email_body="<html><body><h2>Islamic Month Days Override Form</h2>
		<p>Please check default number of days for the current Islamic Month on the Calendar on your website, Wait for the Moon News, If moon sighting is witnessed and If the number of days are same as the default shown in the Calendar, No action is required. </p> <p>If the number of days of the Islamic Month is proven to be different, Click on the appropriate button below to override existing number of days shown in the Calendar.</p>
		<table style='width:100%;'><tr><td>
		<form name='form29days' action='$site_url/days?year_number=$islamic_year&month_number=$islamic_month&islamic_days=29' type='post'>
		<input name='days' type='hidden' value='29'>
		<input name='islamic_month' type='hidden' value='0'>
		<input name='islamic_year' type='hidden' value='0'>
		<button class='pure-button' type='submit' style='width:60%;height:35px;background:rgb(28,184,65);font-size: 125%;'>29 Days</button>
		</form></td><td></td>
		<td align><form action='$site_url/days?year_number=$islamic_year&month_number=$islamic_month&islamic_days=30' name='form30days' type='post'>
		<input name='days' type='hidden' value='30'>
		<input name='islamic_month' type='hidden' value='0'>
		<input name='islamic_year' type='hidden' value='0'>
		<button class='pure-button' type='submit' style='width:60%;height:35px;background:rgb(28,184,65);font-size: 125%;'>30 Days</button>
		</form></td></tr></table>
		<p>If you are not able to click on the links above, your email client doesn't allow you to view external link in your email. Please open this email in the browser to be able to click on it.</p>
		<p>Please note that you have received this email, because your email has been mentioned as the Calendar Admin on website: $site_url. If you are not the Admin, Please contact the Website Adminitrator to correct the information.</p>
		</body></html>";
		
		$to=$xc_options['calendar_admin_email'];
		
		$email_subject='Xllentech Calendar Islamic Month Days Override';
		// Always set content-type when sending HTML email
		$headers[] = "MIME-Version: 1.0";
		$headers[] = 'Content-type: text/html;charset=UTF-8';
					
		wp_mail($to,$email_subject,$email_body,$headers);
		$xc_options['days_email_sent']="1";
		update_option("xc_options",$xc_options);
	}
	elseif($day<=3 & $xc_options['days_email_sent']=="1") {
		$xc_options['days_email_sent']="0";
		update_option("xc_options",$xc_options);
	}
}
}
// register widget
add_action( 'widgets_init', 'xllentech_calendar_widget' );

function xllentech_calendar_widget() {
    register_widget( 'Xllentech_Calendar_Plugin' );
    register_widget( 'Xllentech_Islamic_Today' );
}
/**
 * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
 */
add_action( 'wp_enqueue_scripts', 'xllentech_calendar_css' );

/**
 * Enqueue plugin style-file
 */
function xllentech_calendar_css() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'xllentech-calendar-styles', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'xllentech-calendar-styles' );
    wp_enqueue_script('jquery');
}
if(is_admin())
    include 'xllentech-calendar-settings.php';
?>