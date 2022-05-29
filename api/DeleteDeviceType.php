<?php
$dblink=db_iconnect("equipment");
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