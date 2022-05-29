<?php
/* Author: Joshua Ellis
   This file handles deletion of individual Device records
 * In Production I would either make it so only someone who is at a higher position is allowed to do this or instead of actually deleting it I would move it to a different database,
 * where it can be deleted at a later point, or restored.
*/

// Database connection
$dblink=db_iconnect("equipment");
// Get the variables send by POST
$serial_number = $_REQUEST['serial_number'];
$type = strtolower($_REQUEST['type']);
$devType = "device_".$type;
$sn = "SN-";
$output=array();
$sql = "Select `type` from `device_types`";
$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
$types=array();
while ($data = $result->fetch_array(MYSQLI_ASSOC))
{
		$types[]=$data['type'];

}

// If the type does not exist and the variable is not empty return the appropriate message
if (in_array($type, $types) == false && $type != NULL)
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

// If the variable is empty return the appropriate message
else if ($type == NULL)
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
// If the variable is empty return the appropriate message
else if ($serial_number == NULL)
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

// Else proceed with deletion
else
{
	if(strpos($serial_number, $sn) === false)
	{
		$serial_number = "SN-" . $serial_number;
	}
	$sql = "Select * from `".$devType."` Where `serial_number` = '".$serial_number."'";
	$result = $dblink->query($sql) or
		die("Something went wrong with $sql");
	$device = $result->fetch_array(MYSQLI_ASSOC);
	if ($result->num_rows>0)
	{
		$sql1 = "Delete from `".$devType."` Where `serial_number` = '".$serial_number."'";	
		$dblink->query($sql1) or
			die("Something went wrong with $sql1");
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Success";
		$output[]="MSG: Successfully deleted Device with Serial Number: $serial_number";
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
		$output[]="MSG: Device with Serial Number: $serial_number not found for Type: $type.";
		$output[]="";
		$responseData=json_encode($output);
		echo $responseData;
		die();	
	}
	
}
?>
