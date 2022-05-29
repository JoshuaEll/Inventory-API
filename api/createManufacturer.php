<?php
/* Author: Joshua Ellis
 * Handles the creation of a new Manufacturer to the Inventory Database
*/

// Database connection
$dblink=db_iconnect("equipment");
$manu = $_REQUEST["manufacturer"];
$output=array();
$sql1 = "Select `manufacturer` from `manu_Tbl`";
$result1=$dblink->query($sql1) or
		die("Something went wrong with $sql1");
$manus=array();
while ($data = $result1->fetch_array(MYSQLI_ASSOC))
{
		$manus[]=$data['manufacturer'];

}
// If the manufacturer variable is not empty and already exists return with appropriate message
if (in_array($manu, $manus) == true && $manu != NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Manufacturer exists already.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
// If the manufacturer variable is empty return with appropriate message
else if	($manu == NULL)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Invalid Data";
	$output[]="MSG: Manufacturer must not be blank.";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}

// Else continue with creation
else
{
	$sql = "Insert into `manu_Tbl` (`manufacturer`) Values ('".$manu."')";
	$dblink->query($sql) or
		die("Something went wrong with $sql");
	
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]="Status: Success";
	$output[]="MSG: $manu has been successfully added to the Database";
	$output[]="";
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
?>