<?php
include("functions.php");
$dblink=db_iconnect("equipment");
$type = strtolower($_REQUEST['type']);
$devtype = 'device_'.$type;
$serial_number = $_REQUEST['serial_number'];
$sn = 'SN-';
$output = array();
$data = array();
$sql = "Select `type` from `device_types`";
$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
$types=array();
while ($data = $result->fetch_array(MYSQLI_ASSOC))
{
		$types[]=$data['type'];

}

if($serial_number == NULL)
{
	header('Content-Type: application/json');
	header("HTTP/1.1 200 OK");
	$output[]="Status: Invalid Data";
	$output[]="MSG: Serial Number cannot be blank.";
	$output[]="";
	echo json_encode($output);
	die();
}
else if($type != NULL && in_array($type, $types) === false)
{
	header('Content-Type: application/json');
	header("HTTP/1.1 200 OK");
	$output[]="Status: Invalid Data";
	$output[]="MSG: Must be an existing device type.";
	$output[]="";
	echo json_encode($output);
	die();
}
else if($type == NULL)
{
	header('Content-Type: application/json');
	header("HTTP/1.1 200 OK");
	$output[]="Status: Invalid Data";
	$output[]="MSG: Device Type cannot be blank.";
	$output[]="";
	echo json_encode($output);
	die();
}
else
{
	if(strpos($serial_number, $sn) === false)
	{
		$serial_number = "SN-" . $serial_number;
	}
	$sql = "Select `auto_id` from `".$devtype."` where `serial_number` = '".$serial_number."'";
	$result = $dblink->query($sql) or
		die("Something went wrong with $sql");
	$device = $result->fetch_array(MYSQLI_ASSOC);
	$did = $device['auto_id'];
	if ($result->num_rows>0)
	{
		$sql1 = "Select * from `files` where `device` = '".$did."'";
		$result1 = $dblink->query($sql1) or
			die("Something went wrong with $sql1");
		if ($result1->num_rows>0)
		{
			while ($file = $result1->fetch_array(MYSQLI_ASSOC))
			{
				$data[] = "https://ec2-54-144-131-180.compute-1.amazonaws.com/files/".$file['file_name'];
			}
			header('Content-Type: application/json');
			header("HTTP/1.1 200 OK");
			$output[]="Status: Success";
			$output[]="MSG: Here are the file links";
			$output[]= $data;
			echo json_encode($output);
			die();
		}
		else
		{
			header('Content-Type: application/json');
			header("HTTP/1.1 200 OK");
			$output[]="Status: Not Found";
			$output[]="MSG: Device has no files.";
			$output[]= "";
			echo json_encode($output);
			die();
		}
	}
	else
	{
		header('Content-Type: application/json');
		header("HTTP/1.1 200 OK");
		$output[]="Status: Not Found";
		$output[]="MSG: Device was not found.";
		$output[]= "";
		echo json_encode($output);
		die();
	}
}
?>
