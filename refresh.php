 <?php
 	$bot_id = "56844913:AAG32PEmP3Uquw_m65fKI2Ec083A_ThkFs4";
 	include "include/db_connect.php";
 	include "include/functions.php";	
 	
##########################
/*	$para="topartists";
	$para2="artist";
 	$db_name="last_fm_charts";
	$command="user.getTopArtists&period=7day"; 	
 	refresh($db_name, $command, $para, $para2);
 	$getplace = mysql_query("SELECT `artist` FROM ".$db_name." ORDER BY playcount DESC "); 
	while($getplaces = mysql_fetch_row($getplace)){
		$places[]=$getplaces[0];
	}
	$i=0;	 
	$place=1;		
	foreach($places as $artist_name){
		if($i<60) {
			$getartist = mysql_query("SELECT `playcount` FROM `last_fm_charts` WHERE artist LIKE '$artist_name'"); 
			$artist =	mysql_fetch_row($getartist);
			$count=$artist[0];
			if($place==1) {$count_max=$count;}
			$getuser = mysql_query("SELECT `user` FROM `last_fm_charts` WHERE artist LIKE '$artist_name'"); 
			$users= mysql_fetch_row($getuser);
			$users_names=$users[0];
			$user =  str_replace("&&", ", ",$users_names);
			$cont= $place.". ".$artist_name." (".$count.") Gehört von ".$user;
			if($i>0) {
				$content[$i]=$content[$i-1].''.PHP_EOL.''.$cont;
			}	
			else {
				$content[$i]=$cont;
			}
			$place++;
			$i++;
		}
	}
	$output=urlencode($content[$i-1]);
	$getid = mysql_query("SELECT `telegram-id` FROM `last_fm`"); 
	while($id_db = mysql_fetch_row($getid)){
		$url = 'https://api.telegram.org/bot'.$bot_id.'/sendMessage?chat_id='.$id_db[0].'&text='.$output; 
	//	$result = file_get_contents($url);	
	}		
######################################	
	$db_name="last_fm_charts_all";
	$command="user.getTopArtists&limit=40&period=overall";
 	refresh($db_name, $command, $para, $para2);
 */	
 
######################################
	$para="toptracks";
	$para2="track";
	$db_name="last_fm_charts_track_all";
	$command="user.getTopTracks&limit=40&period=overall";
 	refresh2($db_name, $command, $para, $para2); 
 	
######################################	
 	$para="toptracks";
	$para2="track";
	$db_name="last_fm_charts_track";
	$command="user.getTopTracks&period=7day"; 	
 	refresh2($db_name, $command, $para, $para2); 	
 	
 	
?>
