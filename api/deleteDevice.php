<?php

$dblink=db_iconnect("equipment");
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
