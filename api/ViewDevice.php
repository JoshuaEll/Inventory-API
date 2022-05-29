<?php
include("functions.php");
$dblink=db_iconnect("equipment");
$serial_number = $_REQUEST['serial_number'];
$sn='SN-';
$type = strtolower($_REQUEST['type']);
$devType = "device_".$type; 
$output=array();
$sql = "Select `type` from `device_types`";
$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
$types=array();
while ($data = $result->fetch_array(MYSQLI_ASSOC))
{
		$types[]=$data['type'];

}

if ($serial_number == NULL )
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Serial Number must not be blank.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
elseif ($type == NULL || in_array($type,$types) == false)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Must provide a existing Device type.";
	$output[]="$did";
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
	$sql= "Select `manu_Tbl`.`manufacturer`, `auto_id`, `serial_number`, `status` From `".$devType."` Join `manu_Tbl` on `".$devType."`.`manu_id` = `manu_Tbl`.`manu_id` where `serial_number` = '$serial_number'";
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
	$device=$result->fetch_array(MYSQLI_ASSOC);
	if ($result->num_rows>0)
	{
		$did = $device['auto_id'];
		$sql2 = "Select * from `files` where `device` = '".$did."'";
		$result1=$dblink->query($sql2) or
			die("Something went wrong with $sql2");
		if ($result1->num_rows>0)
		{
			$files = 'Yes';	
		}
		else
		{
			$files = 'No';	
		}
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: OK";
		$output[]="MSG: Results for Serial Number: $serial_number. ";
		$data[]= 'ID: '.$device['auto_id'];
		$data[]='Manufacturer: '.$device['manufacturer'];
		$data[]='Device Type: '.$type;
		$data[]='Status: '.$device['status'];
		$data[]='Files available?: '.$files;
		$output[]=$data;
		$responseData=json_encode($output);
		echo $responseData;
	}
	else
	{
		header('Content-Type: application/json');
		header('HTTP/1.1 200 OK');
		$output[]="Status: Not Found";
		$output[]="MSG: $serial_number";
		$data[]="";
		$output[]=$data;
		$responseData=json_encode($output);
		echo $responseData;
	}
}
?>