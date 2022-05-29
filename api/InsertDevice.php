<?php
include("functions.php");
$dblink = db_iconnect("equipment");
$type = strtolower($_REQUEST['type']);

$manu = $_REQUEST["manufacturer"];
$serial_number = $_REQUEST["serial_number"];
$status = $_REQUEST["status"];
$devType = "device_".$type; 
$sn = "SN-";
$output=array();
$sql1 = "Select `manufacturer` from `manu_Tbl`";
$result1 = $dblink->query($sql1) or
		die("Something went wrong with $sql1");
$manus=array();
while ($data = $result1->fetch_array(MYSQLI_ASSOC))
{
		$manus[]=$data['manufacturer'];

}
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
else if($type != NULL && in_array($type,$types) == false)
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
else if ($manu == NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Manufacturer cannot be blank";
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
	$output[]="MSG: Must provide an existing manufacturer.";
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
	$output[]="MSG: Must provide a Serial Number.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else //// still needs to be written
{
	if(strpos($serial_number, $sn) === false)
	{
		$serial_number = "SN-" . $serial_number;
	}
	if ($status == NULL || strcasecmp($status, 'Inactive') != 0)
	{
		$status = 'Active';	
	}
	else if($status != NULL && strcasecmp($status, 'Inactive') == 0)
	{
		$status = 'Inactive';
	}
	$sql = "SELECT `manu_id` FROM `manu_Tbl` WHERE `manufacturer` = '".$manu."'";
	$result = $dblink->query($sql) or
			die("Something went wrong with $sql");
	$data = $result->fetch_array(MYSQLI_ASSOC);
	$manu_id = $data['manu_id'];
	$sql2 = "INSERT INTO `".$devType."` ( `manu_id`, `serial_number`, `status`) VALUES ('".$manu_id."', '".$serial_number."', '".$status."')";
	$dblink->query($sql2) or
		die("Something went wrong with $sql2");
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Success";
	$output[]="MSG: $serial_number was successfully added to $type.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
?>
