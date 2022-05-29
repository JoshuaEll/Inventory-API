<?php
/* Author: Joshua Ellis
 * This file handles the update of a device's type
*/

// Database Connection

include("functions.php");
$dblink=db_iconnect("equipment");

$output=array();
$sn = 'SN-';
// Get the variable send through POST
$oldtype = strtolower($_REQUEST['oldtype']);
$oldDevtype = "device_".$oldtype;
$newtype = strtolower($_REQUEST['newtype']);
$newDevType = "device_".$newtype;
$serial_number = $_REQUEST['serial_number'];
$sql = "Select `type` from `device_types`";
$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
$types=array();
while ($data = $result->fetch_array(MYSQLI_ASSOC))
{
		$types[]=$data['type'];

}
// Variable validation
if ($newtype == NULL || $oldtype == NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Device type cannot be blank.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else if ( in_array($newtype,$types) == false || in_array($oldtype,$types) == false)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Must provide a existing Device type.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else if( strcasecmp($newtype,$oldtype) == 0)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: The device is already of type: $newtype";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else if($serial_number == NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Serial Number cannot be blank.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}

else
{
	// Add SN- to serial number if it does not have it
	if (strpos($serial_number, $sn) === false)
	{
		$serial_number = 'SN-' . $serial_number;
	}
	$sql = "Select * from `".$oldDevtype."` where `serial_number` = '$serial_number'";
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
	$device=$result->fetch_array(MYSQLI_ASSOC);
	// If the record exists, update it's device Type,
	if ($result->num_rows>0)
	{
		$manu = $device['manu_id'];
		$status = $device['status'];
		// Insert it into the new table
		$sql2 = "Insert into `".$newDevType."` (`manu_id`, `serial_number`, `status`) Values ('".$manu."', '".$serial_number."', '".$status."')";
		$dblink->query($sql2) or
			die("Something went wrong with $sql2");
		// Delete it from the old table
		$sql3 = "Delete from `".$oldDevtype."` where `serial_number` = '".$serial_number."'";
		$dblink->query($sql3) or
			die("Something went wrong with $sql3");
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Success";
		$output[]="MSG: Changed type for $serial_number from $oldtype to $newtype.";
		$output[]="";
		$responseData=json_encode($output);
		echo $responseData;
	die();
	}
	else
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Not Found";
		$output[]="MSG: Device Id: $did not in database for $oldtype";
		$data[]="";
		$output[]=$data;
		$responseData=json_encode($output);
		echo $responseData;
	}
}
?>
