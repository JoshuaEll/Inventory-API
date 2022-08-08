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

// data base connection (better to use sepereate file for this information)
$usr = "User name here";
$pw = "password here";
$db = "equipment";
$hostname = "localhost";
$sn = "SN-";
$did = $_REQUEST['did'];
$type = $_REQUEST['type'];
$dblink = new mysqli($hostname, $usr, $pw, $db);
// find all types
$sql = "Select `type` from `device_types`";
$result = $dblink->query($sql) or
		die("Something went wrong with $sql");
$devices=array();

while ($data = $result->fetch_array(MYSQLI_ASSOC))
	{
		$devices[]=$data['type'];
		
	}
echo '<div class="panel panel-success col-md-3">';
echo '<div class="panel-heading">Update Device</div>';
echo '<div class="panel-body">';
echo '<form method="post" action="">';
echo '<div><select name="types">';

// display the types in a drop down to allow the user to change the type of the original device
foreach($devices as $key=>$value)
	{
		echo '<option value="'.$value.'">'.$value.'</option>';
	}
echo '</select></div>';
echo '<p>Please select the type:</p>';
echo '<input type="text" name="manufacturer">';
echo '<p>Please enter the manufacturer:</p>';
echo '<input type="text" name="serial_number">';
echo '<p>Please enter the serial Number:</p>';
echo '<div><select name="status">';
echo '<option value="Active">Active</option>';
echo '<option value="Inactive">Inactive</option>';
echo '</select></div>';
echo '<p>(Optional)Please select the status of the Device:</p>';
echo '<hr>';
echo '<div><button type="submit" name="submit" value="lookUp">Submit</button></div>';
echo '</form>';
echo '</div>';
echo '</div>';
	
		
if (isset($_POST['submit']) && $_POST['submit']=="lookUp")
	{
			
			
			$devtype = $_POST['types'];
			$devtype = "device_".$devtype;//"_copy";
			$lowType = strtolower($devtype);
			$manu = $_POST['manufacturer'];
			$serial = $_POST['serial_number'];
			$status = $_POST['status'];
			$dat = array();
			$sql = "Select `manufacturer` from `manu_Tbl`";
			$result = $dblink->query($sql) or
				die("Something went wrong with $sql");
			
			$manuArray=array();
			
			while ($data1 = $result->fetch_array(MYSQLI_ASSOC))
			{
					$manuArray[]=$data1['manufacturer'];

			}
			
			// unnecessary. I could just allow for empty fields and then query the manufacturer of that device
			if(empty($manu) || in_array($manu, $manuArray) == false)
			{
				echo '<script>alert("Field cannot be empty and manufacturer must exist in database!")</script>';
				redirect("https://ec2-54-144-131-180.compute-1.amazonaws.com/update.php?did=".$did."&type=".$type."");
			}
			// same as with the manufacturer
			if(empty($serial))
			{
				echo '<script>alert("Serial Number field needs to filled out.")</script>';
				redirect("https://ec2-54-144-131-180.compute-1.amazonaws.com/update.php?did=".$did."&type=".$type."");
			}
	
			// if the user changed the type, then move the device to the new table and do all the other changes 
			if(strcmp($lowType,$type) !== 0)
			{
				if(strpos($serial, $sn) !== false)
				{
				$sql = "SELECT `manu_id` FROM `manu_Tbl` WHERE `manufacturer` = '".$manu."'";
				$result = $dblink->query($sql) or
						die("Something went wrong with $sql");
				while ($data = $result->fetch_array(MYSQLI_ASSOC))
				{
						$manu_id = $data['manu_id'];
				}
				$sql1 = "INSERT INTO `".$lowType."` (`manu_id`, `serial_number`, `status`) VALUES ('".$manu_id."', '".$serial."', '".$status."')";
				$dblink->query($sql1) or
						die("Something went wrong with $sql1");
				$sql2 = "DELETE FROM ".$type." WHERE `auto_id` = '".$did."'";
				$dblink->query($sql2) or
						die("Something went wrong with $sql2");
				$sql3 = "SELECT `auto_id` FROM `".$lowType."` WHERE `serial_number` = '".$serial."'";
				$result2 = $dblink->query($sql3) or
						die("Something went wrong with $sql");
				while ($data = $result2->fetch_array(MYSQLI_ASSOC))
				{
						$newDID = $data['auto_id'];
				}
				redirect("https://ec2-54-144-131-180.compute-1.amazonaws.com/update.php?did=".$newDID."&type=".$lowType."");
				}
				else
				{
					$fixed_serial = "SN-" . $serial;
					$sql = "SELECT `manu_id` FROM `manu_Tbl` WHERE `manufacturer` = '".$manu."'";
					$result = $dblink->query($sql) or
						die("Something went wrong with $sql");
					while ($data = $result->fetch_array(MYSQLI_ASSOC))
					{
						$manu_id = $data['manu_id'];
					}
					$sql1 = "INSERT INTO ".$lowType." (`manu_id`, `serial_number`, `status`) VALUES ('".$manu_id."', '".$fixed_serial."', '".$status."')";
					$dblink->query($sql1) or
						die("Something went wrong with $sql1");
					$sql2 = "DELETE FROM ".$type." WHERE `auto_id` = '".$did."'";
					$dblink->query($sql2) or
						die("Something went wrong with $sql2");	
					$sql3 = "SELECT `auto_id` FROM `".$lowType."` WHERE `serial_number` = '".$fixed_serial."'";
					$result2 = $dblink->query($sql3) or
						die("Something went wrong with $sql");
					while ($data = $result2->fetch_array(MYSQLI_ASSOC))
					{
							$newDID = $data['auto_id'];
					}
					redirect("https://ec2-54-144-131-180.compute-1.amazonaws.com/update.php?did=".$newDID."&type=".$lowType."");
					
				}
			}
			else
			{
				if(strpos($serial, $sn) !== false)
				{

					$sql = "SELECT `manu_id` FROM `manu_Tbl` WHERE `manufacturer` = '".$manu."'";
					$result = $dblink->query($sql) or
						die("Something went wrong with $sql");
					while ($data = $result->fetch_array(MYSQLI_ASSOC))
					{
						$manu_id = $data['manu_id'];
					}
					$sql2 = "UPDATE ".$lowType." SET `manu_id` = ".$manu_id.", `serial_number` = '".$serial."', `status` = '".$status."' WHERE `auto_id` = '".$did."'";
					$dblink->query($sql2) or
						die("Something went wrong with $sql2");
				}
				else
				{
					
					$fixed_serial = "SN-" . $serial;
					$sql = "SELECT `manu_id` FROM `manu_Tbl` WHERE `manufacturer` = '".$manu."'";
					$result = $dblink->query($sql) or
						die("Something went wrong with $sql");
					while ($data = $result->fetch_array(MYSQLI_ASSOC))
					{
						$manu_id = $data['manu_id'];
					}
					$sql2 = "UPDATE `".$lowType."` SET `manu_id` = ".$manu_id.", `serial_number` = '$fixed_serial', `status` = '$status' WHERE `auto_id` = '$did'";
					$dblink->query($sql2) or
						die("Something went wrong with $sql2");
			}
		}
}

echo '<div class="panel panel-success col-md-3">';
echo '<div class="panel-heading">More Options</div>';
echo '<div class="panel-body">';
echo '<div> <a href="index.php">Home</a></div>';
echo '</div>';
echo '</div>';
?>
