<?php
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

global $wpdb;
$month_days_table = $wpdb->prefix . 'month_days'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$year_number=$_POST["year_number"];	
	$month_number=$_POST["month_number"];	
	$islamic_days=$_POST["islamic_days"];
	$add_pin_received=$_POST["add_pin"];
	
	if($add_pin_received==$xc_options['xc_page_pin']){
	$wpdb->insert($month_days_table, array("month_number" => $month_number, "year_number" => $year_number, "days" => $islamic_days), array("%d", "%d", "%d"));
	//exit( var_dump( $wpdb->last_query ) );
		if($wpdb->last_error != '') {
			$wpdb->print_error();
		}
		else {
			echo "Transaction successful.";	
		}
	}
	else {
			echo "Incorrect PIN, system blocked.";		
	}
}
elseif (isset($_GET['year_number'])) {
	$year_number=$_GET["year_number"];
	$month_number=$_GET["month_number"];
	$islamic_days=$_GET["islamic_days"];
?>
	<form id="islamic-days" method="post" action="#">
<table>
<tbody>
	<tr>
		<td><label for="month_number">Month Number:</label></td>
		<td><select name="month_number">
			<option value="1" <?php if($month_number==1) echo "selected"; ?> ><?php echo $islamic_months[1] ?></option>
			<option value="2" <?php if($month_number==2) echo "selected"; ?> ><?php echo $islamic_months[2] ?></option>
			<option value="3" <?php if($month_number==3) echo "selected"; ?> ><?php echo $islamic_months[3] ?></option>
			<option value="4" <?php if($month_number==4) echo "selected"; ?> ><?php echo $islamic_months[4] ?></option>
			<option value="5" <?php if($month_number==5) echo "selected"; ?> ><?php echo $islamic_months[5] ?></option>
			<option value="6" <?php if($month_number==6) echo "selected"; ?> ><?php echo $islamic_months[6] ?></option>
			<option value="7" <?php if($month_number==7) echo "selected"; ?> ><?php echo $islamic_months[7] ?></option>
			<option value="8" <?php if($month_number==8) echo "selected"; ?> ><?php echo $islamic_months[8] ?></option>
			<option value="9" <?php if($month_number==9) echo "selected"; ?> ><?php echo $islamic_months[9] ?></option>
			<option value="10" <?php if($month_number==10) echo "selected"; ?> ><?php echo $islamic_months[10] ?></option>
			<option value="11" <?php if($month_number==11) echo "selected"; ?> ><?php echo $islamic_months[11] ?></option>
			<option value="12" <?php if($month_number==12) echo "selected"; ?> ><?php echo $islamic_months[12] ?></option>
		</select></td>
	</tr>
	<tr>
		<td><label for="year_number">Year Number:</label></td><td><input value=<?php echo "$year_number"; ?> type="number" name="year_number"/></td>
	</tr>
	<tr>
		<td><label for="islamic_days">Number of Days:</label></td>
		<td><select name="islamic_days">
			<option value="29" <?php if($islamic_days==29) echo "selected"; ?> >29</option>
			<option value="30" <?php if($islamic_days==30) echo "selected"; ?>>30</option></select> 
		</td>
	</tr>
	<tr>
		<td><label for="add_pin">Enter PIN:</label></td><td><input type="number" name="add_pin"/></td>
	</tr>
	<tr>
		<td colspan="2"><button>Add to Calendar</button></td>
	</tr>
</tbody>
</table>
</form>
<?php
}
else {
	?>
<form id="islamic-days" method="post" action="#">
<table>
<tbody>
	<tr>
		<td><label for="month_number">Month Number:</label></td>
		<td><select name="month_number">
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
	</tr>
	<tr>
		<td><label for="year_number">Year Number:</label></td><td><input value="1437" type="number" name="year_number"/></td>
	</tr>
	<tr>
		<td><label for="islamic_days">Number of Days:</label></td>
		<td><select name="islamic_days">
			<option value="29">29</option>
			<option value="30">30</option></select> 
		</td>
	</tr>
	<tr>
		<td><label for="add_pin">Enter PIN:</label></td><td><input type="number" name="add_pin"/></td>
	</tr>
	<tr>
		<td colspan="2"><button>Add to Calendar</button></td>
	</tr>
</tbody>
</table>
</form>
<?php
}
?>