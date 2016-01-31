 <?php
 	$bot_id = "56844913:AAG32PEmP3Uquw_m65fKI2Ec083A_ThkFs4";
 	include "include/config.php";
 	include "include/db_connect.php";
 	include "include/functions.php";	
 	
##########################
	$para="topartists";
	$para2="artist";
 	$db_name="last_fm_charts";
	$command="user.getTopArtists&period=7day"; 	
 	refresh($db_name, $command, $db);
	$getplace = $db->query("SELECT `artist` FROM `last_fm_charts` ORDER BY playcount DESC "); 
 	$p=0;
	while($getplaces = $getplace->fetch_assoc()){
		$places[$p]=$getplaces['artist'];
		$p++;
	}
	$i=0;	 
	$place=1;		
	foreach($places as $artist_name){
		if($i<60) {
			$getartist = $db->query("SELECT `playcount` FROM `last_fm_charts` WHERE artist LIKE '$artist_name'"); 
			$artist = $getartist->fetch_assoc();
			$count=$artist['playcount'];
			if($place==1) {$count_max=$count;}
			$getuser = $db->query("SELECT `user` FROM `last_fm_charts` WHERE artist LIKE '$artist_name' "); 
			$users= $getuser->fetch_assoc();
			$users_names=$users['user'];
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
	$getid = $db->query("SELECT `telegram-id` FROM `last_fm`"); 
	while($id_db = $getid->fetch_assoc()){
		$url = 'https://api.telegram.org/bot'.$bot_id.'/sendMessage?chat_id='.$id_db['telegram-id'].'&text='.$output; 
		//$result = file_get_contents($url);	
	}		
######################################	
	$db_name="last_fm_charts_all";
	$command="user.getTopArtists&limit=40&period=overall";
 	refresh($db_name, $command, $db);
 
######################################
	$db_name="last_fm_charts_track";
	$command="user.getTopTracks&limit=40&period=overall";
 	refresh3($db_name, $command, $db); 
 	
######################################	
	$time=	time();
	$time2=$time-(3600*24*7);
	$db_name="last_fm_charts_track";
	$command="user.getWeeklyTrackChart&from=".$time2."&to=".$time; 	
 	refresh2($db_name, $command, $db); 	
 	
 	
?>
