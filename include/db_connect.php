<?php
  	include("config.php");
	$server="localhost";
	$verbindung = mysql_connect($server, $username , $password) or die("Verbindung zur Datenbank konnte nicht hergestellt werden"); 
	mysql_select_db($dbname) or die ("Datenbank konnte nicht ausgewählt werden"); 
?>
