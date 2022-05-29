<?php
$dblink=db_iconnect("equipment");
$serial_number = $_REQUEST['serial_number'];
$sn = 'SN-';
$output=array();
$type = strtolower($_REQUEST['type']);
$devtype = "device_".$type;
$status = $_REQUEST['status'];
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
else if ( in_array($type,$types) == false)
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
else if ($status == NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Status cannot be blank.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else if (strcasecmp($status, 'Inactive') != 0 && strcasecmp($status, 'Active') != 0)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Status has to be either Inactive or Active.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else
{
	if (strpos($serial_number, $sn) === false)
	{
		$serial_number = 'SN-' . $serial_number;
	}
	if (strcasecmp($status, 'Inactive') == 0){
		$status = 'Inactive';
	}
	else if (strcasecmp($status, 'Active') == 0)
	{
		$status = 'Active';	
	}
	$sql = "Select * from `".$devtype."` where `serial_number` = '$serial_number'";
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
	$device=$result->fetch_array(MYSQLI_ASSOC);
	if ($result->num_rows>0)
	{
		$sql2 = "Update `".$devtype."` Set `status` = '$status' Where `serial_number` = '$serial_number'";
		$dblink->query($sql2) or
			die("Something went wrong with $sql2");
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Success";
		$output[]="MSG: Changed Status for Device with serial number: $serial_number to $status.";
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
