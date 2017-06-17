 <?php
 	$bot_id = "56844913:AAG32PEmP3Uquw_m65fKI2Ec083A_ThkFs4";
 	include "include/config.php";
 	include "include/db_connect.php";
 	include "include/functions.php";	
 	
##########################
	$para="topartists";
	$para2="artist";
	$db_name = date('YW');
	$insert = $db->query("INSERT INTO tables (table_name) VALUES ('$db_name')"); 
	$command="user.getTopArtists&period=7day"; 	
 	refresh($db_name, $command, $db, $api_key);
	$getplace = $db->query("SELECT `artist` FROM `".$db_name."` ORDER BY playcount DESC "); 
	while($getplaces = $getplace->fetch_assoc()){
		$places[]=$getplaces['artist'];
	}
	$i=0;	 
	$place=1;		
	foreach($places as $artist_name){
		if($i<40) {
			$getartist = $db->query("SELECT `playcount` FROM `".$db_name."` WHERE artist LIKE '$artist_name'"); 
			$artist = $getartist->fetch_assoc();
			$count=$artist['playcount'];
			if($place==1) {$count_max=$count;}
			$getuser = $db->query("SELECT `user` FROM `".$db_name."` WHERE artist LIKE '$artist_name' "); 
			$users= $getuser->fetch_assoc();
			$users_names=$users['user'];
			$user =  str_replace("&&", ", ",$users_names);
			$pl="";
			if($i<9) {
				$pl="  ";
			}
			$cont= $pl."<b>".$place.". ".$artist_name."</b>".PHP_EOL."      (".$user.")";
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
	$output=urlencode(html_entity_decode ('<a href="https://lastfm.ldkf.de/lastfm.php?method=4">Lastfm Gruppencharts</a>'.PHP_EOL.''.$content[$i-1]));
	$getid = $db->query("SELECT `telegram-id` FROM `last_fm`"); 
	while($id_db = $getid->fetch_assoc()){
		
		$url = 'https://api.telegram.org/bot'.$bot_id.'/sendMessage?chat_id=78597075&parse_mode=HTML&text='.$output; 
		#$url = 'https://api.telegram.org/bot'.$bot_id.'/sendMessage?chat_id='.$id_db['telegram-id'].'&parse_mode=HTML&text='.$output; 
		$result = file_get_contents($url);	
	}		

	$db_name="last_fm_charts_all";
	$command="user.getTopArtists&period=overall";
 	refresh($db_name, $command, $db, $api_key);
?>
