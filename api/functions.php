<?php
/* Author: Joshua Ellis
 * This file holds the functions used in the other files
 * This is also where I would put a verification Function
*/
function db_iconnect($dbName)
{
	$un = "Enter the user name here";
	$pw = "Enter the password here";
	$db = $dbName;
	$hostname = "localhost";
	$dblink = new mysqli($hostname, $un, $pw, $db);
	return $dblink;
}



?>
