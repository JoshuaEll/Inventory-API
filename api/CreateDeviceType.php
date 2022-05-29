<?php
/* Author: Joshua Ellis 
 * This File handles the request wanting to create a new Device Type in the Inventory
 * In Production I would change it a bit, by checking if the table already exists, since I would handle deletion differently.
*/

// Database Connection
include("functions.php");
$dblink=db_iconnect("equipment");

// Request for the variables send through POST
$type = strtolower($_REQUEST['type']);
$output=array();
$devType = 'device_'.$type;
// Query to create an array of existing Device Types
$sql = "Select `type` from `device_types`";
$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
$types=array();
while ($data = $result->fetch_array(MYSQLI_ASSOC))
{
		$types[]=$data['type'];

}
// If the variable is empty return with the appropriate message
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
// If the Device Type already exists return with the appropiate message
else if (in_array($type, $types) == true)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: $type already exists.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
// Else continue with creation
else
{
	$sql = "Create table ".$devType." ("." `auto_id` INT NOT NULL AUTO_INCREMENT, `manu_id` INT NOT NULL, `serial_number` VARCHAR(64) NOT NULL, `status` VARCHAR(32) NOT NULL Default ('Active'), PRIMARY KEY (`auto_id`), FOREIGN KEY (`manu_id`) REFERENCES `manu_Tbl`(`manu_id`) ON DELETE CASCADE ON UPDATE CASCADE)";
	$dblink->query($sql) or
			die(mysqli_error($dblink));
	$sql3 = "INSERT INTO device_types (`type`) VALUES ('".$type."')";
	$dblink->query($sql3) or
		die(mysqli_error($sql3));
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Success";
	$output[]="MSG: $type has been successfully added to the Database";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}

?>