<?php

$dblink=db_iconnect("equipment");

$type = strtolower($_REQUEST['type']);
$devtype = "device_".$type;
$manu = $_REQUEST['manufacturer'];
$serial_number = $_REQUEST['serial_number'];
$sn = 'SN-';
$output=array();
$sql = "Select `type` from `device_types`";
$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
$types=array();
while ($data = $result->fetch_array(MYSQLI_ASSOC))
{
		$types[]=$data['type'];

}

$sql1 = "Select `manufacturer` from `manu_Tbl`";
$result1=$dblink->query($sql1) or
		die("Something went wrong with $sql1");
$manus=array();
while ($data = $result1->fetch_array(MYSQLI_ASSOC))
{
		$manus[]=$data['manufacturer'];

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
else if ($manu != NULL && in_array($manu, $manus) == false)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Manufacturer must exist.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else if ($manu == NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Manufacturer cannot be blank.";
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
	$sql = "Select * from `".$devtype."` where `serial_number` = '$serial_number'";
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
	$device=$result->fetch_array(MYSQLI_ASSOC);
	if ($result->num_rows>0)
	{
		$sql1 = "Select `manu_id` from `manu_Tbl` Where `manufacturer` = '".$manu."'";
		$result1 = $dblink->query($sql1) or
			die("Something went wrong with $sql1");
		$data = $result1->fetch_array(MYSQLI_ASSOC);
		$manu_id = $data['manu_id'];
		$sql2 = "Update `".$devtype."` Set `manu_id` = '$manu_id' Where `serial_number` = '$serial_number'";
		$dblink->query($sql2) or
			die("Something went wrong with $sql2");
		
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Success";
		$output[]="MSG: Change manufacturer for Device with Serial Number: $serial_number.";
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
		$output[]="MSG: Device with Serial Number: $serial_number not found in $type.";
		$data[]="";
		$output[]=$data;
		$responseData=json_encode($output);
		echo $responseData;
	}
}
?>
