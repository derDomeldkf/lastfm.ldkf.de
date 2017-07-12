 
<?php
	if(isset($_POST[1])) {
		session_start();
		include "config.php";
		include "db_connect.php";
		include "functions.php";
		$user_in=$_POST[0];
		$limit_in=$_POST[1];
		$page_in=$_POST[2];
		$method=$_POST[3];
		if(isset($_POST[4]) and $_POST[4]=="true") {
			$time_check=$_POST[5];
		}
	}
	else {

		$user_in=$_SESSION['user'];
		$limit_in=35;
		$page_in=1;
		$method="RecentTracks";
	}	
	$user=get_info($method, $user_in, $page_in, $limit_in, $api_key);
	include $method.".php";	 
?>