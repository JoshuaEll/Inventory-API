<?php

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
