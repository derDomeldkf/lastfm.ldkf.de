 
<?php
	session_start();
	include "config.php";
	include "db_connect.php";
	include "functions.php";
	$user_in=$_POST[0];
	$page_in=1;
	$method_in=2;
	$limit_in=$_POST[1];
	if(isset($_POST[2])) {
		$time_check=$_POST[2];
	}
	$user=get_info("RecentTracks", $user_in, $page_in, 1, $api_key);
	include "../user_tracks.php";	 

?>