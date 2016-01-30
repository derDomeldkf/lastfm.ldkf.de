<?php
  	include("config.php");
  	$mysqli = new mysqli($server, $username, $password, $dbname);
	if ($mysqli->connect_errno) {
   	echo "Verbindung zur Datenbank konnte nicht hergestellt werden: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
?>

