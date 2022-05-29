<?php
/* Author: Joshua Ellis
 * This file handles the deletion of device types (Caution: Gets rid of whole table)
 * For Production this would have to be either only be accessable for people that should have access or instead of actually deleting
 * it, it can be put into a different Database for a certain amount of time, in case it needs to be restored. Another method could be getting rid of the device type from the types table, but leaving the actual table intact
*/

// Database connection
$dblink=db_iconnect("equipment");

// Request the variables send through POST
$type = strtolower($_REQUEST['type']);
$devtype = 'device_'.$type;

$sql = "Select `type` from `device_types`";
$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
$types=array();
while ($data = $result->fetch_array(MYSQLI_ASSOC))
{
		$types[]=$data['type'];

}
// If the variable is not empty and the Device Type does not exist return the appropriate message
if ($type != NULL && in_array($type, $types) === false)
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
// If the variable is empty return the approriate message
else if ($type == NULL)
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
// Else continue with deletion
else
{
	$sql1 = "Delete from `device_types` where `type` = '$type'";
	$dblink->query($sql1) or
		die("Something went wrong with $sql1");
	$sql2 = "Drop table `".$devtype."`";
	$dblink->query($sql2) or
		die("Something went wrong with $sql2");
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Success";
	$output[]="MSG: Successfully deleted Type: $type.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}

?>