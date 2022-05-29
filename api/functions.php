<?php

function db_iconnect($dbName)
{
	$un = "webuser";
	$pw = "gG6SLzdskA2IrbKs";
	$db = $dbName;
	$hostname = "localhost";
	$dblink = new mysqli($hostname, $un, $pw, $db);
	return $dblink;
}



?>