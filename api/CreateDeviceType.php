<?php
include("functions.php");
$dblink=db_iconnect("equipment");
$type = strtolower($_REQUEST['type']);
$output=array();
$devType = 'device_'.$type;
$sql = "Select `type` from `device_types`";
$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
$types=array();
while ($data = $result->fetch_array(MYSQLI_ASSOC))
{
		$types[]=$data['type'];

}

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