<link href="assets/css/bootstrap.css" rel="stylesheet" />
<link href="assets/css/main.css" rel="stylesheet" />
<link href="assets/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="assets/css/responsive.dataTables.min.css" rel="stylesheet" />
<script src="assets/js/jquery-3.5.1.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<script src="assets/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
 $.extend( $.fn.dataTable.defaults, {
    responsive: true
});
 
$(document).ready(function() {
    $('#invDetails').DataTable(); 
});
</script>
<?php
	function redirect ( $uri )
	{ ?>
		<script type="text/javascript">
		<!--
		document.location.href="<?php echo $uri; ?>";
		-->
	</script>
<?php die;}
// db connection
$usr = "username here";
$pw = "Password here";
$db = "equipment";
$hostname = "localhost";

$dblink = new mysqli($hostname, $usr, $pw, $db);
$sn = "SN-";
// check if the submit button has not been clicked yet
if (!isset($_POST['submit']))
{
	// display all the selection components
	$sql = "Select `type` from `device_types`";
	$result = $dblink->query($sql) or
		die("Something went wrong with $sql");
	$devices=array();

	while ($data = $result->fetch_array(MYSQLI_ASSOC))
	{
		$devices[]=$data['type'];
		
	}
	$sql = "Select `manufacturer` from `manu_Tbl`";
	$result = $dblink->query($sql) or
		die("Something went wrong with $sql");
	$manu=array();

	while ($data = $result->fetch_array(MYSQLI_ASSOC))
	{
		$manuf[]=$data['manufacturer'];
		
	}
	$devStr=implode(",",$devices);
	
	echo '<div class="panel panel-success col-md-3">';
	echo '<div class="panel-heading">Search Device</div>';
	echo '<div class="panel-body">';
	echo '<form method="post" action="">';
	echo '<input type="hidden" name="devices" value="'.$devStr.'">';
	echo '<label>(Required)Please select the type:</label>';
	echo '<div><select name="device">';
	foreach($devices as $key=>$value)
	{
		echo '<option value="'.$value.'">'.$value.'</option>';
	}
	echo '</select></div>';
	
	$manuStr=implode(",",$manuf);
	echo '<form method="post" action="">';
	echo '<input type="hidden" name="manufacturer" value="'.$manuStr.'">';
	echo '<label>(Required)Please select the manufacturer:</label>';
	echo '<div><select name="manu">';
	foreach($manuf as $key=>$value)
	{
		echo '<option value="'.$value.'">'.$value.'</option>';
	}
	echo '</select></div>';
	echo '<label>(Optional)Please enter the serial Number:</label>';
	echo '<input type="text" name="serial_number">';
	echo '<label>Please select the status of the Device:</label>';
	echo '<div><select name="status">';
	echo '<option value="Active">Active</option>';
	echo '<option value="Inactive">Inactive</option>';
	echo '</select></div>';
	echo '<hr>';
	echo '<div><button type="submit" name="submit" value="lookUp">Submit</button></div>';
	
	echo '</form>';
	echo '</div>';
	echo '</div>';
}
// if the submit button has been clicked fullfill the query
if (isset($_POST['submit']) && $_POST['submit']=="lookUp")
{
	$device=$_POST['device'];
	$tmp=$_POST['devices'];
	$devices=explode(",", $tmp);
	
	$manu=$_POST['manu'];
	$serial_number = $_POST['serial_number'];
	$status = $_POST['status'];
	echo '<div class="panel">';
	echo '<div class="panel-body">';
	echo '<table id="invDetails" class="display" cellspacing="0" width="100%">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>Manufacturer</th>';
	echo '<th>Serial Number</th>';
	echo '<th>Status</th>';
	echo '<th>Action</th>';
	echo '<th></th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	//if (empty($manu) == false)
	//{
	if(strpos($serial_number, $sn) === false)
	{
		$serial_number = "SN-" . $serial_number;
	}
	$sql="Select `device_".$device."`.`auto_id`, `manu_Tbl`.`manufacturer`, `device_".$device."`.`serial_number`, `device_".$device."`.`status`  from `device_".$device."`  Join `manu_Tbl` on `device_".$device."`.`manu_id` = `manu_Tbl`.`manu_id` WHERE `manu_Tbl`.`manufacturer` = '".$manu."' and `serial_number` like '%".$serial_number."%' and `status` = '".$status."' order by `serial_number` limit 19000";   
	$result=$dblink->query($sql, MYSQLI_USE_RESULT) or
		die("Something went wrong with $sql");

	while ($info=$result->fetch_array(MYSQLI_ASSOC))
	{
		echo '<tr>';
		echo '<td>'.$info['manufacturer'].'</td>';
		echo '<td>'.$info['serial_number'].'</td>';
		echo '<td>'.$info['status'].'</td>';
		echo '<td><a href="upload.php?did='.$info['auto_id'].'&type=device_'.$device.'" >More Info </a></td>';
		echo '<td><a href="update.php?did='.$info['auto_id'].'&type=device_'.$device.'" > Update</a></td>';
		echo '</tr>';

	}
	//}
	//else
	//{
		//echo '<script>alert("Please enter all required fields.")</script>';
		//redirect("https://ec2-54-144-131-180.compute-1.amazonaws.com/index.php");
	//}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
	echo '</div>';
	
	
}
echo '<div class="panel panel-success col-md-3">';
echo '<div class="panel-heading">More Options</div>';
echo '<div class="panel-body">';
echo '<div> <a href="create.php">Create/Insert</a></div>';
echo '<div> <a href="index.php">Home</a></div>';
echo '</div>';
echo '</div>';

?>
