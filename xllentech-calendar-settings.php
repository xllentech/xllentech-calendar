<?php
if (!defined("XC_PLUGIN_VERSION")) 	define("XC_PLUGIN_VERSION",  "2.0.1");
if (!defined("XC_PLUGIN_DIR")) 		define("XC_PLUGIN_DIR", plugins_url() .'/'. dirname(plugin_basename(__FILE__)));

/**
 * Add an admin submenu link under Settings
 */
 
register_activation_hook( __FILE__, 'xllentech_calendar_activate' );
function xllentech_calendar_activate() {
  set_transient( '_xllentech_calendar_activation_redirect', true, 30 );
}

add_action( 'admin_init', 'xllentech_calendar_do_activation_redirect' );
function xllentech_calendar_do_activation_redirect() {
  // Bail if no activation redirect
    if ( ! get_transient( '_xllentech_calendar_activation_redirect' ) ) {
    return;
	}

	// Delete the redirect transient
delete_transient( '_xllentech_calendar_activation_redirect' );

  // Bail if activating from network, or bulk
  if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
    return;
  }

  // Redirect to about page
wp_safe_redirect( add_query_arg( array( 'page' => 'xllentech_options' ), admin_url( 'options-general.php' ) ) );
}


function xllentech_add_options_submenu_page() {
     $xc_settings_page=add_submenu_page(
          'options-general.php',          // admin page slug
          __( 'Xllentech Calendar Settings', 'xllentech' ), // page title
          __( 'Xllentech Calendar', 'xllentech' ), // menu title
          'manage_options',               // capability required to see the page
          'xllentech_options',                // admin page slug, e.g. options-general.php?page=xllentech_options
          'xllentech_options_page'            // callback function to display the options page
     );
	 $xc_features_page=add_submenu_page(
          'options-general.php',          // admin page slug
          __( 'Xllentech Calendar Settings', 'xllentech' ), // page title
          __( 'Xllentech Calendar', 'xllentech' ), // menu title
          'manage_options',               // capability required to see the page
          'xllentech_options_tab2',                // admin page slug, e.g. options-general.php?page=xllentech_options
          'xllentech_options_tab2'            // callback function to display the options page
     );
	 $xc_features_page=add_submenu_page(
          'options-general.php',          // admin page slug
          __( 'Xllentech Calendar Settings', 'xllentech' ), // page title
          __( 'Xllentech Calendar', 'xllentech' ), // menu title
          'manage_options',               // capability required to see the page
          'xllentech_options_tab3',                // admin page slug, e.g. options-general.php?page=xllentech_options
          'xllentech_options_tab3'            // callback function to display the options page
     );
}
add_action( 'admin_menu', 'xllentech_add_options_submenu_page' );

add_action( 'admin_head', 'xllentech_remove_menus' );

function xllentech_remove_menus() {
    remove_submenu_page( 'options-general.php', 'xllentech_options_tab2' );
	remove_submenu_page( 'options-general.php', 'xllentech_options_tab3' );
}

/** Register stylesheet */

function xc_admin_style( $hook ) {
        wp_register_style( 'xc_settings_styles', XC_PLUGIN_DIR . '/includes/settings_style.css', false, '2.0.1' );
        wp_enqueue_style( 'xc_settings_styles' );
}
add_action( 'admin_enqueue_scripts', 'xc_admin_style' );

/**
 * Register the settings
 */
add_action( 'admin_init', 'xllentech_register_settings' );
function xllentech_register_settings() {
     register_setting(
          'xc_options',  // settings section
          'xc_options', // setting name
          'xllentech_options_validate'
     );
}
// validate our options
function xllentech_options_validate($input) {
return $input;
}

/**
 * Build the options page
 */
// display the admin options page
function xllentech_options_tab3() {
	global $wpdb;
	$month_days_table = $wpdb->prefix . 'month_days';
//settings_fields( 'xc_options_group' );
		
	$xc_options = get_option("xc_options");

if (isset($_POST["month_update"])) {
	?>
		<div class="updated fade"><p><strong><?php _e( 'You have successfully customized Islamic Month Names!', 'xllentech' ); ?></strong></p></div> <?php
	$new_islamic_months='Islamic Months,'.$_POST["islamic_months1"].','.$_POST["islamic_months2"].','.$_POST["islamic_months3"].
		','.$_POST["islamic_months4"].','.$_POST["islamic_months5"].','.$_POST["islamic_months6"].','
		.$_POST["islamic_months7"].','.$_POST["islamic_months8"].','.$_POST["islamic_months9"].
		','.$_POST["islamic_months10"].','.$_POST["islamic_months11"].','.$_POST["islamic_months12"];
	$new_islamic_months=stripslashes($new_islamic_months);
	$xc_options['islamic_months'] = $new_islamic_months;
	update_option("xc_options",$xc_options);
}

	$islamic_months = explode(",", $xc_options['islamic_months']);
	$islamic_month_days = explode(",", $xc_options['islamic_month_days']);
?>

<div class="wrap">
<h2>XllenTech Calendar Settings</h2>

<h2 class="nav-tab-wrapper">
	<a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=xllentech_options">Settings</a>
	<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>options-general.php?page=xllentech_options_tab3">Month Names</a>
	<a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=xllentech_options_tab2">Month Days</a>
	
</h2>
<div class="xllentech_sidebar">
	<?php include_once 'includes/sidebar.php'; ?>
</div>
<div class="xllentech-calendar-settings">
<h3>Islamic Month Names</h3>
<p>Change Islamic month names as you like here and save!</p>
<form name="months_name_update" method="post" action="#">
	<table class="xllentech-months-table">
		<tr>
	<td>1</td><td><input type="text" name="islamic_months1" value="<?php echo $islamic_months[1]?>" /> </td>
	<td>2</td><td><input type="text" name="islamic_months2" value="<?php echo $islamic_months[2]?>" /> </td>
		</tr>
		<tr>
	<td>3</td><td><input type="text" name="islamic_months3" value="<?php echo $islamic_months[3]?>" /> </td>
	<td>4</td><td><input type="text" name="islamic_months4" value="<?php echo $islamic_months[4]?>" /> </td>
		</tr>
		<tr>
	<td>5</td><td><input type="text" name="islamic_months5" value="<?php echo $islamic_months[5]?>" /> </td>
	<td>6</td><td><input type="text" name="islamic_months6" value="<?php echo $islamic_months[6]?>" /> </td>
		</tr>
		<tr>
	<td>7</td><td><input type="text" name="islamic_months7" value="<?php echo $islamic_months[7]?>" /> </td>
	<td>8</td><td><input type="text" name="islamic_months8" value="<?php echo $islamic_months[8]?>" /> </td>
		</tr>
		<tr>
	<td>9</td><td><input type="text" name="islamic_months9" value="<?php echo $islamic_months[9]?>" /> </td>
	<td>10</td><td><input type="text" name="islamic_months10" value="<?php echo $islamic_months[10]?>" /> </td>
		</tr>
		<tr>
	<td>11</td><td><input type="text" name="islamic_months11" value="<?php echo $islamic_months[11]?>" /> </td>
	<td>12</td><td><input type="text" name="islamic_months12" value="<?php echo $islamic_months[12]?>" /> </td>
		</tr>
		<tr>
	<td colspan="4">
	<input type="hidden" name="month_update" value="Y"/>
	<input type="submit" name="submit" value="<?php esc_attr_e('Update Names'); ?>" </td>
		</tr>
	</table>
</form>
</div>
 
<?php
}
function xllentech_options_tab2(){
	global $wpdb;
	$month_days_table = $wpdb->prefix . 'month_days';
	$month_firstdate_table=$wpdb->prefix . 'month_firstdate';

	if(isset($_POST["add_monthdays"])) {
		// Do the saving
	//    unset($_POST["add_database"]));
		$year_number=$_POST["year_number"];
		$month_number=$_POST["month_number"];
		$islamic_days=$_POST["islamic_days"];
		$wpdb->insert($month_days_table, array("month_number" => $month_number, "year_number" => $year_number, "days" => $islamic_days), array("%d", "%d", "%d"));
		//exit( var_dump( $wpdb->last_query ) );
		if($wpdb->last_error != '') {
			$wpdb->print_error();
		}
		else { ?>
			<div class="updated fade"><p><strong><?php _e( 'You have successfully overridden Islamic Month Days, You will see the Calendar dates move accordingly!', 'xllentech' ); ?></strong></p></div> <?php
		}
	}
	else if(isset($_POST["delete_monthdays"])) {
		$year_number=$_POST["year_number"];
		$month_number=$_POST["month_number"];
		$wpdb->query("delete from ".$month_days_table." where month_number=".$month_number." and year_number=".$year_number);
		if($wpdb->last_error != '') {
			$wpdb->print_error();
		}
		else { ?>
			<div class="updated fade"><p><strong><?php _e( 'You have successfully Deleted the data row!', 'xllentech' ); ?></strong></p></div> <?php
		}
	}
	else if(isset($_POST["delete_firstdate"])) {
		$year_number=$_POST["year_number"];
		$month_number=$_POST["month_number"];
		$wpdb->query("delete from ".$month_firstdate_table." where english_month=".$month_number." and english_year=".$year_number);
		if($wpdb->last_error != '') {
			$wpdb->print_error();
		}
		else { ?>
			<div class="updated fade"><p><strong><?php _e( 'You have successfully Deleted the data row!', 'xllentech' ); ?></strong></p></div> <?php
		}
	}
	else if(isset($_POST["add_firstdate"])) {
		// Do the saving
	//    unset($_POST["add_database"]));
		$wpdb->insert($month_firstdate_table, array("english_month" => $_POST["english_month"], "english_year" => $_POST["english_year"], "islamic_day" => $_POST["islamic_day"], "islamic_month" => $_POST["islamic_month"],"islamic_year" => $_POST["islamic_year"] ), array("%d", "%d", "%d", "%d", "%d" ));
		//exit( var_dump( $wpdb->last_query ) );
		if($wpdb->last_error != '') {
			$wpdb->print_error();
		}
		else { ?>
			<div class="updated fade"><p><strong><?php _e( 'You have successfully Added Data row!', 'xllentech' ); ?></strong></p></div> <?php
		}
	}
	
	$xc_options = get_option("xc_options");
	
	$islamic_months = explode(",", $xc_options['islamic_months']);
	$islamic_month_days = explode(",", $xc_options['islamic_month_days']);

?>
<div class="wrap">
<h2>XllenTech Calendar Settings</h2>

<h2 class="nav-tab-wrapper">
	<a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=xllentech_options">Settings</a>
	<a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=xllentech_options_tab3">Month Names</a>
	<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>options-general.php?page=xllentech_options_tab2">Month Days</a>
	
</h2>
<div class="xllentech_sidebar">
	<?php include_once 'includes/sidebar.php'; ?>
</div>
	<div class="xllentech-calendar-settings-tab2">

	<h3>Update Islamic Month Days</h3>
	<p>Use below form to override default number of days of islamic month on Calendar.</p>
	<form method="post" action="#">

	<table class="xllentech-settings-table">
		<tr>
			<td style="padding-right:5px"><label for="month_number">Month:</label></td>
			<td style="padding-left:5px"><select name="month_number">
				<option value="1"><?php echo $islamic_months[1] ?></option>
				<option value="2"><?php echo $islamic_months[2] ?></option>
				<option value="3"><?php echo $islamic_months[3] ?></option>
				<option value="4"><?php echo $islamic_months[4] ?></option>
				<option value="5"><?php echo $islamic_months[5] ?></option>
				<option value="6"><?php echo $islamic_months[6] ?></option>
				<option value="7"><?php echo $islamic_months[7] ?></option>
				<option value="8"><?php echo $islamic_months[8] ?></option>
				<option value="9"><?php echo $islamic_months[9] ?></option>
				<option value="10"><?php echo $islamic_months[10] ?></option>
				<option value="11"><?php echo $islamic_months[11] ?></option>
				<option value="12"><?php echo $islamic_months[12] ?></option>
			</select></td>
			<td style="padding-right:5px"><label for="year_number">Year:</label></td><td style="padding-left:5px"><input value="1437" style="width:60px;" type="number" name="year_number"/></td>
			<td style="padding-right:5px"><label for="islamic_days">Number of Days:</label></td>
			<td style="padding-left:5px"><select name="islamic_days" style="width:50px;">
				<option value="29">29</option>
				<option value="30">30</option></select> 
			</td>
		</tr>
		<tr>
			<td colspan="6" align="center"><input type="hidden" name="add_monthdays" value="Y"/>
			<input name="Submit" type="submit" value="<?php esc_attr_e('Add to Database'); ?>" />
			</td>
		</tr>
	</table>
	</form>
	</div>

	<div class="xllentech-calendar-settings">
	<h3>Islamic Month Days Existing Entries</h3>
	<p>Below entries currently exist in database.</p>
	<table class="xllentech-settings-table" style="border: 1px solid #7d7d7d;border-collapse: collapse;">
	<tr>
		<th>Month</th><th>Year</th><th>Days</th><th>Delete</th>
	</tr>
<?php 
	$month_data = $wpdb->get_results("SELECT * FROM $month_days_table");
   		foreach( $month_data as $islamic_date_data ) {
			echo "<tr>";
   			echo "<td>".$islamic_date_data->month_number."</td><td>".$islamic_date_data->year_number."</td><td>".$islamic_date_data->days."</td>";
			echo "<td><form method='post' action='#'><input type='hidden' name='month_number' value='".$islamic_date_data->month_number."'><input type='hidden' name='year_number' value='".$islamic_date_data->year_number."'><input type='hidden' name='delete_monthdays' value='Y'><button>Delete</button></form></td>";
			echo "</tr>";
		}
?>
	</table>
	</div>
	<h4>DO NOT ADD OR DELETE ANYTHING IN BELOW FORM. THE PURPOSE OF BELOW DATA IS ONLY FOR TROUBLESHOOTING. I RECOMMEND SUBMITTING A SUPPORT TICKET FOR ANY ISSUE YOU MAY HAVE. CHANGING BELOW DATA MAY BREAK DOWN THE CALENDAR.</h4>
	<div class="xllentech-calendar-settings">
	<table class="xllentech-settings-table" style="border: 1px solid #7d7d7d;border-collapse: collapse;">
	<tr>
		<th>Month</th><th>Year</th><th>Islamic Day</th><th>Islamic Month</th><th>Islamic Year</th><th>Delete</th>
	</tr>
<?php 
	$month_data = $wpdb->get_results("SELECT * FROM $month_firstdate_table");
   		foreach( $month_data as $islamic_date_data ) {
			echo "<tr>";
   			echo "<td>".$islamic_date_data->english_month."</td><td>".$islamic_date_data->english_year."</td><td>".$islamic_date_data->islamic_day."</td><td>".$islamic_date_data->islamic_month."</td><td>".$islamic_date_data->islamic_year."</td>";
			echo "<td><form method='post' action='#'><input type='hidden' name='month_number' value='".$islamic_date_data->english_month."'><input type='hidden' name='year_number' value='".$islamic_date_data->english_year."'><input type='hidden' name='delete_firstdate' value='Y'><button>Delete</button></form></td>";
			echo "</tr>";
		}
?>
	</table>
	<form method="post" action="#">
		<table class="xllentech-settings-table" style="border: 1px solid #7d7d7d;border-collapse: collapse;">
			<tr>
			<td style="padding-right:0px"><input style="width: 50px" type="number" name="english_month" id="english_month"></td>
			<td style="padding-right:0px"><input style="width: 70px" type="number" name="english_year" id="english_year"></td>
			<td><input style="width: 80px" type="number" name="islamic_day" id="islamic_day"></td>
			<td><input style="width: 80px" type="number" name="islamic_month" id="islamic_month"></td>
			<td><input style="width: 86px" type="number" name="islamic_year" id="islamic_year"></td>
			<td><input class="xc_textbox" type="hidden" name="add_firstdate" value="Y"><button>ADD</button></td>
			</tr>
		</table>
	</form>
	</div>
</div>
<?php	
}

function xllentech_options_page() {
	
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
		
	if(isset($_POST["update_xc_settings"])) {
		if(!empty($_POST["email_choice"]))
			$xc_options['calendar_email_choice']='Yes';
		else
			$xc_options['calendar_email_choice']='No';
		
		$xc_options['calendar_admin_email']=$_POST["admin_email"];
		$xc_options['xc_time_zone']=$_POST['timezone_list'];
		$xc_options['xc_page_pin']=$_POST['page_pin'];
		update_option("xc_options",$xc_options);
		?>
		<div class="updated fade"><p><strong><?php _e( 'Settings Saved!', 'xllentech' ); ?></strong></p></div> <?php
	}
	
	$islamic_months = explode(",", $xc_options['islamic_months']);
	$islamic_month_days = explode(",", $xc_options['islamic_month_days']);
	$calendar_email_choice= $xc_options['calendar_email_choice'];
	$calendar_admin_email= $xc_options['calendar_admin_email'];
?>
	<div class="wrap">
	<h2>XllenTech Calendar Settings</h2>

	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>options-general.php?page=xllentech_options">Settings</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=xllentech_options_tab3">Month Names</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=xllentech_options_tab2">Month Days</a>
	</h2>
		<div class="xllentech_sidebar">
			<?php include_once 'includes/sidebar.php'; ?>
		</div>
		<div class="xllentech-calendar-settings-tab3">

		<h4>Select TimeZone: </h4>
		<form method="post" action="#">
			<select name="timezone_list" id="timezone_list">
			<?php $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
				foreach($tzlist as $timezone_list) {
			?>
				<option value="<?php echo $timezone_list; ?>" <?php if($timezone_list==$xc_options['xc_time_zone']) echo 'selected'; ?> ><?php echo $timezone_list; ?></option>
				<?php } ?>
			</select>
			<h4>Email Feature</h4>
			<h5 style="margin-right:20px;display:inline;">For this feature to work, you are required to create a page titled DAYS with the file named islamic-month-days.php included in the plugin folder, make the page accessible at (www.)yourwebsite(.com)/days. Once the page is setup, you can click buttons in the sample email below to make sure buttons take you to the page correctly.
			</br>If the Enable Email is checked, Every 29th of Islamic Month, Email will be sent out to the Calendar Admin Email with the link. Clicking the 29 or 30 Days link in the email will take you to your website page, that will override islamic month days accordingly. Sample Email shown at bottom.</h5>
				<table>
					<tr><td>Enable Email:</td><td> <input type='checkbox' name='email_choice' id='email_choice' <?php if($calendar_email_choice!='No'): echo 'value="Yes"'; echo ' checked'; else: echo 'value="No"'; endif; ?> /></td> </tr>
					<tr><td>Calendar Admin Email: </td> <td> <input type='text' name='admin_email' id='admin_email' value='<?php echo $calendar_admin_email ?>' /> </td> </tr>
					<tr><td>Page PIN: </td><td> <input type='number' size="6" maxlength="6" name="page_pin" id="page_pin" value="<?php echo $xc_options['xc_page_pin']; ?>" /></td></tr>
				</table>
			<input type='hidden' name='update_xc_settings' value='Y' />
			</br></br>
			<button>Update</button> 
		</form>
<br/><br/>
			<div class="tab3-sample-email">
			<h2>Sample Email</h2>
				<h4>Islamic Month Days Override Form</h4>
				<p align="justify">Please check default number of days for the current Islamic Month on the Calendar on your website, If moon sighting is witnessed to be the same as the default shown in the Calendar, No action is required. </p> <p>If the number of days of the Islamic Month is proven to be different, Click on the appropriate button below to override existing number of days shown in the Calendar.</p>
				<table style='width:100%;'><tr><td>
				<?php $site_url=get_site_url(); ?>
				<form name='form29days' action="<?php echo $site_url; ?>/days" method='get' target="_blank">
				<input name='islamic_days' type='hidden' value='29'>
				<input name='month_number' type='hidden' value='8'>
				<input name='year_number' type='hidden' value='1437'>
				<button class='pure-button' type='submit' style='width:60%;height:35px;background:rgb(28,184,65);font-size: 125%;'>29 Days</button>
				</form></td><td></td>
				<td align><form name='form30days' action="<?php echo $site_url; ?>/days" method='get' target="_blank">
				<input name='islamic_days' type='hidden' value='30'>
				<input name='month_number' type='hidden' value='8'>
				<input name='year_number' type='hidden' value='1437'>
				<button class='pure-button' type='submit' style='width:60%;height:35px;background:rgb(28,184,65);font-size: 125%;'>30 Days</button>
				</form></td></tr></table>
				<p align="justify">If you are not able to click on the links above, your email client doesn't allow you to view external link in your email. Please open this email in the browser to be able to click on it.</p>
				<p align="justify">Please note that you have received this email, because your email has been mentioned as the Calendar Admin on website: <?php echo $site_url ?>. If you are not the Admin, Please contact the Website Adminitrator to correct the information.</p>
			</div>
		</div> 
	</div>
<?php	
}
?>