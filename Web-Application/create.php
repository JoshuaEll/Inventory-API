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
$usr = "webuser";
$pw = "gG6SLzdskA2IrbKs";
$db = "equipment";
$hostname = "localhost";
$sn = "SN-";
$dblink = new mysqli($hostname, $usr, $pw, $db);
$sql = "Select `type` from `device_types`";
$result = $dblink->query($sql) or
	die("Something went wrong with $sql");
			
$devices=array();
			
while ($data = $result->fetch_array(MYSQLI_ASSOC))
{
		$devices[]=$data['type'];

}
echo '<div class="panel panel-success col-md-3">';
echo '<div class="panel-heading">Create/Delete Device Type</div>';
echo '<div class="panel-body">';
echo '<form method="post" action="">';
echo '<input type="text" name="types">';
echo '<p>Please enter the type:</p>';
echo '<hr>';
echo '<div><button type="submit" name="submit" value="lookUp">Submit</button> <button type="submit" name="delete" value="lookUp">Delete</button></div>';
echo '</form>';
echo '</div>';
echo '</div>';
			
		
if (isset($_POST['submit']) && $_POST['submit']=="lookUp")
		{
			$newType = $_POST['types'];
			$lowType = strtolower($newType);
			foreach($devices as $key=>$value)
			{
				if($value == $lowType)
				{
					echo '<p>Type already exists</p>';
					redirect("https://ec2-54-144-131-180.compute-1.amazonaws.com/create.php");
				}
			}
			

			$sql = "Create table device_".$lowType." ("." `auto_id` INT NOT NULL AUTO_INCREMENT, `manu_id` INT NOT NULL, `serial_number` VARCHAR(64) NOT NULL, `status` VARCHAR(32) NOT NULL Default ('Active'), PRIMARY KEY (`auto_id`), FOREIGN KEY (`manu_id`) REFERENCES `manu_Tbl`(`manu_id`) ON DELETE CASCADE ON UPDATE CASCADE)";
			$dblink->query($sql) or
					die(mysqli_error($dblink));
			$sql3 = "INSERT INTO device_types (`type`) VALUES ('".$lowType."')";
			$dblink->query($sql3) or
				die(mysqli_error($sql3));
						//redirect("https://ec2-54-144-131-180.compute-1.amazonaws.com");
					
		}
if(isset($_POST['delete']) && $_POST['submit']=="lookUp")
{
	$newType = $_POST['types'];
	$lowType = strtolower($newType);
	$sql2 = "Delete from `device_types` where `type` = '".$lowType."'";
	$dblink->query($sql2) or
			die("Something went wrong with $sql2");
	$sql3 = "Drop table `device_".$lowType."`";
	$dblink->query($sql3) or
			die("Something went wrong with $sql3");
	
}

echo '<div class="panel panel-success col-md-3">';
echo '<div class="panel-heading">Insert/Delete Device</div>';
echo '<div class="panel-body">';
echo '<form method="post" action="">';
echo '<input type="text" name="types">';
echo '<p>Please enter the type:</p>';
echo '<input type="text" name="manufacturer">';
echo '<p>Please enter the manufacturer:</p>';
echo '<input type="text" name="serial_number">';
echo '<p>Please enter the serial Number:</p>';
echo '<div><select name="status">';
echo '<option value="Active">Active</option>';
echo '<option value="Inactive">Inactive</option>';
echo '</select></div>';
echo '<hr>';
echo '<div><button type="submit" name="submi" value="lookUp">Submit</button>   <button type="submit" name="dele" value="lookUp">Delete</button></div>';
echo '</form>';
echo '</div>';
echo '</div>';	
		
if (isset($_POST['submi']) && $_POST['submi']=="lookUp")
		{
			$newType = $_POST['types'];;
			$lowType = strtolower($newType);
			$new_manu = $_POST['manufacturer'];
			$new_serial = $_POST['serial_number'];
			$status = $_POST['status'];
			$sql2 = "Select `manufacturer` from `manu_Tbl`";
			$result2 = $dblink->query($sql2) or
				die("Something went wrong with $sql2");
			$oldManu = array();
			while ($data = $result2->fetch_array(MYSQLI_ASSOC))
			{
				$oldManu[]=$data['manufacturer'];

			}
			if(in_array($newType, $devices)== true)
			{
				
				
				if(in_array($new_manu, $oldManu) == false)
				{

					$sql3 =  "INSERT INTO manu_Tbl (`manufacturer`) VALUES ('".$new_manu."')";
					$dblink->query($sql3) or
						die("Something went wrong with $sql3");
				}
				if(strpos($new_serial, $sn) !== false)
						{
							$sql = "SELECT `manu_id` FROM `manu_Tbl` WHERE `manufacturer` = '".$new_manu."'";
							$result = $dblink->query($sql) or
									die("Something went wrong with $sql");
							while ($data = $result->fetch_array(MYSQLI_ASSOC))
							{
									$manu_id = $data['manu_id'];
							}
							$sql2 = "INSERT INTO device_".$lowType." ( `manu_id`, `serial_number`, `status`) VALUES ('".$manu_id."', '".$new_serial."', '".$status."')";
							$dblink->query($sql2) or
								die("Something went wrong with $sql2");
							//redirect("https://ec2-54-144-131-180.compute-1.amazonaws.com");
						}
				else
						{
							$fixed_serial = "SN-" . $new_serial;
							$sql = "SELECT `manu_id` FROM `manu_Tbl` WHERE `manufacturer` = '".$new_manu."'";
							$result = $dblink->query($sql) or
									die("Something went wrong with $sql");
							while ($data = $result->fetch_array(MYSQLI_ASSOC))
							{
									$manu_id = $data['manu_id'];
							}
							$sql2 = "INSERT INTO device_".$lowType." ( `manu_id`, `serial_number`, `status`) VALUES ('".$manu_id."', '".$fixed_serial."', '".$status."')";
							$dblink->query($sql2) or
								die("Something went wrong with $sql2");

						}
			}
			else
			{
				echo '<script>alert("Device type does not exist.\nPlease create it first.")</script>';
			}
}
if(isset($_POST['dele']))
{
	
	$newType = $_POST['types'];
	$new_serial = $_POST['serial_number'];
	if(in_array($newType, $devices))
	{
		if(strpos($new_serial, $sn) !== false)
		{
			$sql = "DELETE FROM device_".$newType."_copy WHERE `serial_number` = '".$new_serial."'";
			$dblink->query($sql) or
				die("Something went wrong with $sql");
		}
		else
		{
			$fixed_serial = "SN-" . $new_serial;
			$sql2 = "DELETE FROM device_".$newType." WHERE `serial_number` = '".$fixed_serial."'";
			$dblink->query($sql2) or
					die("Something went wrong with $sql2");
		}
	}
	else
	{
		echo '<script>alert("Device type does not exist.\nPlease create it first.")</script>';
	}
}
			
echo '<div class="panel panel-success col-md-3">';
echo '<div class="panel-heading">More Options</div>';
echo '<div class="panel-body">';
echo '<div> <a href="index.php">Home</a></div>';

?>