 <?php
 	$bot_id = "56844913:AAG32PEmP3Uquw_m65fKI2Ec083A_ThkFs4";
 	include "include/config.php";
 	include "include/db_connect.php";
 	include "include/functions.php";	
 	
#####################################
	$db_t = date('YW');
	$db_name=$db_t."_titel";
	$insert = $db->query("INSERT INTO tables (table_name) VALUES ('$db_name')"); 
	$command="user.getTopArtists&period=7day"; 	
	$command="user.getTopTracks&limit=40&period=overall";
 	refresh3($db_name, $command, $db); 
 	
######################################	
	$time=	time();
	$time2=$time-(3600*24*7);
	$db_name="last_fm_charts_track";
	$command="user.getWeeklyTrackChart&from=".$time2."&to=".$time; 	
 	refresh2($db_name, $command, $db); 	
 	
 	
 
 
 ?>
