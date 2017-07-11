<?php
	session_start();
	include "config.php";
	include "db_connect.php";
	include_once "functions.php";
	$user_in=$_POST[0];
	echo logout($user_in, $db);
?> 
