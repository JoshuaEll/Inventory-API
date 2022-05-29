<?php
/* Author: Joshua Ellis
 * This file handles the update of a device's Serial number_format
*/

// Database connection
include("functions.php");
$dblink=db_iconnect("equipment");

// Get the variables send through POST
$oldSerial = $_REQUEST['old_serial'];
$type = strtolower($_REQUEST['type']);
$devtype = "device_".$type;
$output=array();
$new_serial = $_REQUEST['new_serial'];
$sn = "SN-";
$sql = "Select `type` from `device_types`";
$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
$types=array();
while ($data = $result->fetch_array(MYSQLI_ASSOC))
{
		$types[]=$data['type'];

}

// Variable Validation
if ($type == NULL)
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
else if ( $type != NULL && in_array($type,$types) == false)
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
else if ( $type == NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Device Type cannot be blank.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else if($oldSerial == NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Serial Numbers cannot be blank.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else if ($new_serial == NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Serial Numbers cannot be blank.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else
{
	// Add SN- to the old and new serial if they did not have it
	if(strpos($oldSerial,$sn) === false)
	{
		$oldSerial = 'SN-' . $oldSerial;	
	}
	if(strpos($new_serial,$sn) === false)
	{
		$new_serial = 'SN-' . $new_serial;	
	}
	$sql = "Select * from `".$devtype."` where `serial_number` = '$oldSerial'";
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
	$device=$result->fetch_array(MYSQLI_ASSOC);
	// If the device exists change the serial number
	if ($result->num_rows>0)
	{
		$sql2 = "Update `".$devtype."` Set `serial_number` = '$new_serial' Where `serial_number` = '$oldSerial'";
		$dblink->query($sql2) or
			die("Something went wrong with $sql2");
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Success";
		$output[]="MSG: Changed Serial Number from $oldSerial to $new_serial.";
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
		$output[]="MSG: Device Id: $did not in database for $type";
		$data[]="";
		$output[]=$data;
		$responseData=json_encode($output);
		echo $responseData;
	}
}
?>
