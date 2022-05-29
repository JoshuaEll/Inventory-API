<?php
include("functions.php");
$dblink = db_iconnect("equipment");
$type = strtolower($_REQUEST['type']);
$manu = $_REQUEST['manufacturer'];
if (isset($_REQUEST['serial_number']))
{
	$serial_number = $_REQUEST['serial_number'];
}
else
{
	$serial_number = '';
}
if (isset($_REQUEST['status']))
{
	$status = $_REQUEST['status'];
}
else
{
	$status = '';
}
$sn = "SN-";
$data2=array();
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
while ($data1 = $result1->fetch_array(MYSQLI_ASSOC))
{
		$manus[]=$data1['manufacturer'];

}
if ($type == NULL || in_array($type,$types) == false)
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
else if($manu != NULL && in_array($manu,$manus) == false)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Must provide a existing Manufacturer.";
	$output[]="Existing manufacturers: $manus";
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
		
		$sql="Select `device_".$type."`.`auto_id`, `manu_Tbl`.`manufacturer`, `device_".$type."`.`serial_number`, `device_".$type."`.`status`  from `device_".$type."`  Join `manu_Tbl` on `device_".$type."`.`manu_id` = `manu_Tbl`.`manu_id` WHERE `manu_Tbl`.`manufacturer` = '".$manu."' and `serial_number` like '%".$serial_number."%' and `status` like '%".$status."%' order by `serial_number` limit 1000";   
		$result=$dblink->query($sql) or
			die("Something went wrong with $sql");
		if($result->num_rows>0)
		{
			$counter = 1;
			$data2=array();
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]="Status: OK";
			$output[]="MSG: ";
			while ($info=$result->fetch_array(MYSQLI_ASSOC))
			{
				$data2[] = 'Counter: '.$counter;
				$data2[] = 'Manufacturer: '.$info['manufacturer'];
				$data2[] = 'Serial Number: '.$info['serial_number'];
				$data2[] = 'Status: '.$info['status'];
				$counter += 1;
			}
			$output[] = $data2;
			$responseData=json_encode($output);
			echo $responseData;
		}
		else
		{
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]="Status: Not Found";
			$output[]="MSG: No devices of type: $type found";
			$data[]="";
			$output[]=$data;
			$responseData=json_encode($output);
			echo $responseData;
		}
	}
	else{	
		$sql="Select `device_".$type."`.`auto_id`, `manu_Tbl`.`manufacturer`, `device_".$type."`.`serial_number`, `device_".$type."`.`status`  from `device_".$type."`  Join `manu_Tbl` on `device_".$type."`.`manu_id` = `manu_Tbl`.`manu_id` WHERE `manu_Tbl`.`manufacturer` = '".$manu."' and `serial_number` like '%".$serial_number."%' and `status` like '%".$status."%' order by `serial_number` limit 1000";   
		$result=$dblink->query($sql) or
			die("Something went wrong with $sql");
		if($result->num_rows>0)
		{
			$data2=array();
			$counter = 1;
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]="Status: OK";
			$output[]="MSG: ";
			while ($info=$result->fetch_array(MYSQLI_ASSOC))
			{
				$data2[] = 'Counter: '.$counter;
				$data2[] = 'Manufacturer: '.$info['manufacturer'];
				$data2[] = 'Serial Number: '.$info['serial_number'];
				$data2[] = 'Status: '.$info['status'];
				$counter += 1;
			}
			$output[] = $data2;
			$responseData=json_encode($output);
			echo $responseData;
		}
		else
		{
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]="Status: Not Found";
			$output[]="MSG: No devices of type: $type found $serial_number";
			$data[]="";
			$output[]=$data;
			$responseData=json_encode($output);
			echo $responseData;
		}
	}
}

?>