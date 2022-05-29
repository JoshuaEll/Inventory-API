<?php
/* Author: Joshua Ellis
 * This file was used when I was testing my endpoints using powershell's invoke-restmethod
*/

include("functions.php");
$uri=parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY);
$uri=explode('&',$uri);
$endPoint=$uri[0];
//die("End Point: $endPoint");
switch ($endPoint){
	case "ViewDevice":
		include("ViewDevice.php");
		break;
	case "ListDevices":
		include("ListDevices.php");
		break;
	case "UploadFile":
		include("UploadFile.php");
		break;
	case "UpdateDeviceType":
		include("UpdateDeviceType.php");
		break;
	case "UpdateDeviceManu":
		include("UpdateDeviceManu.php");
		break;
	case "UpdateDeviceSerial":
		include("UpdateDeviceSerial.php");
		break;
	case "UpdateDeviceStatus":
		include("UpdateDeviceStatus.php");
		break;
	case "ViewFile":
		include("ViewFile.php");
		break;
	case "CreateDeviceType":
		include("CreateDeviceType.php");
		break;
	case "DeleteDeviceType":
		include("DeleteDeviceType.php");
		break;
	case "InsertDevice":
		include("InsertDevice.php");
		break;
	case "deleteDevice":
		include("deleteDevice.php");
		break;
	case "createManufacturer":
		include("createManufacturer.php");
		break;
	default:
		header('Content-Type: application/json');
		header("HTTP/1.1 404 Not Found");
		$message[]="Status: Error";
		$message[]="MSG: Endpoint not found";
		$message[]="";
		$responseData = json_encode($message);
		echo $responseData;
		die();
}
?>