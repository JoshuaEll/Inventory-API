<?php
include("functions.php");
//header("Content-Type: application/json");
//header("Acess-Control-Allow-Origin: *");
//header("Acess-Control-Allow-Methods: POST");
//header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods, Authorization");

$dblink=db_iconnect("equipment");
$extArray = array('application/pdf','application/docx', 'image/png', 'image/jpg', 'image/jpeg', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//$did = $_REQUEST['did'];
//$type = $_REQUEST['type'];
//$data = json_decode(file_get_contents("php://input:"), true);
//$did = $_REQUEST['did'];
$file = $_REQUEST['files'];
$fileName = $_FILES['files']['name'];
$tempPath = $_FILES['files']['tmp_name'];
$fileSize = $_FILES['files']['size'];


if ($fileName === NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: File name cannot be blank.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
else{
	$uploadDir = "/var/www/html/files";
	$fileName = preg_replace('/\s+/','_',$fileName);
	$fileType = $_FILES['files']['type'];
	$location = "$uploadDir/$fileName";
	if (in_array($_FILES['userfile']['type'], $extArray) && preg_match($regexEx, $fileName) !== 0)
		{	
			move_uploaded_file($tempPath, $location);
			$sql = "Insert into `files` (`file_name`,`file_type`,`file_size`, `location`, `device`) Values";
			$sql.=" ('$fileName', '$fileType', '$fileSize', '$location', '$did')";
			$dblink->query($sql) or
				die("Something went wrong with $sql");
		}
	else
		{	
			header('Content-Type: application/json');
			header('HTTP/1.1 200 OK');
			$output[]="Status: Invalid File Type";
			$output[]="MSG: Only Files of type: pdf, png, jpeg, docx and word accepted.";
			$output[]="";
			$responseData=json_encode($output);
			echo $responseData;
			die();
		}
}
?>