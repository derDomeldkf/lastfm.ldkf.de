<?php

 	function refresh($db_name, $command, $db, $api_key) {
		$getusers = $db->query("SELECT `username` FROM `ldkf_lastfm`"); 
 		while ($getuser = $getusers->fetch_assoc()) {
 			$users[]=$getuser['username'];
 		}		
 		if($db_name=="last_fm_charts_all") {
 			$delete = $db->query("DELETE FROM ".$db_name);	
 			$s=0;
 		}
 		else {
 			$sql = "CREATE TABLE `".$db_name."` (
				playcount INT(3) NOT NULL,
				artist TEXT NOT NULL COLLATE utf8_general_mysql500_ci,
				user TEXT NOT NULL COLLATE latin1_swedish_ci
			)";
			if ($db->query($sql) === TRUE) {
				$s=0;
			}
			else {
   		 	echo "Error creating table: " . $db->error;
   		 	$s=1;
			}
		}
		$d=0;
		if($s==0) {
			foreach($users as $user_in){
				$methode="method=".$command."&user=".$user_in;
				$out = post($methode, $api_key);
				if(isset($out)) {
					$user_info_array = get_object_vars(json_decode($out));
					if(isset($user_info_array['topartists'])) {
						$user_info = get_object_vars($user_info_array['topartists']);	
						foreach($user_info['artist'] as $top) {
							$info=get_object_vars($top);
							$name=rep($info["name"]);
							$playcount=$info["playcount"];
							$url=$info["url"];
							$image = get_object_vars($info["image"][0]);
							$image_path=$image['#text'];
							if(!isset($image_path) or $image_path=="") {
								$image_path="pic/empty.png";
							}
							$getcounter = $db->query("SELECT `playcount` FROM `".$db_name."` WHERE artist LIKE '$name'"); 
							if(isset($getcounter->num_rows) and  $getcounter->num_rows!= 0) {
								$counter = $getcounter->fetch_assoc()['playcount'];
							}
							if(isset($counter) and $counter!="") {
								$getuser_add = $db->query("SELECT `user` FROM `".$db_name."` WHERE artist LIKE '$name' "); 
								$user_db = $getuser_add->fetch_assoc()['user'];
								$counter_insert=$counter+$playcount;
								$user_insert=$user_db."&&".$user_in;
								$update = $db->query("UPDATE `".$db_name."` SET user = '$user_insert', playcount ='$counter_insert'  where artist = '$name'");  
							}
							else {
								$insert = $db->query("INSERT INTO `".$db_name."` (playcount, artist, user) VALUES ('$playcount', '$name', '$user_in')"); 
							}
							$counter="";
						}
						
					}
					unset($out);
				} 
				$d++;
			}  
		}
	}

#############################################################################################################################
	function refresh2($db_name, $command, $db, $api_key) {
		$getusers = $db->query("SELECT `username` FROM `ldkf_lastfm`"); 
 		while ($getuser = $getusers->fetch_assoc()) {
 			$users[]=$getuser['username'];
 		}		
 		$sql = "CREATE TABLE `".$db_name."` (
			playcount INT(3) NOT NULL,
			artist TEXT NOT NULL COLLATE utf8_general_mysql500_ci,
			user TEXT NOT NULL COLLATE latin1_swedish_ci,
			titel TEXT NOT NULL COLLATE utf8_general_mysql500_ci
		)";
		if ($db->query($sql) === TRUE) {
			$s=0;
		}
		else {
   	 	echo "Error creating table: " . $db->error;
   	 	$s=1;
		}	
		$d=0;
		foreach($users as $user_in){
			$methode="method=".$command."&user=".$user_in;
			$out = post($methode, $api_key);
			if(isset($out)) {
				$user_info_array = get_object_vars(json_decode($out));
				if(isset($user_info_array['weeklytrackchart'])) {
					$user_info = get_object_vars($user_info_array['weeklytrackchart']);	
					foreach($user_info['track'] as $top) {
						$info=get_object_vars($top);
						$name=rep($info["name"]);
						$playcount=$info["playcount"];
						$art_array=$info["artist"];
						$art=get_object_vars($art_array);
						$artist_name=rep($art['#text']);
						$url=$info["url"];
						$image = get_object_vars($info["image"][0]);
						$image_path=$image['#text'];
						if(!isset($image_path) or $image_path=="") {
							$image_path="pic/empty.png";
						}
						$getcount = $db->query("SELECT `playcount` FROM `".$db_name."` WHERE titel LIKE '$name' "); 
						if(isset($getcount->num_rows) and  $getcount->num_rows!= 0) {
							$counter = $getcount->fetch_assoc()['playcount'];
						}
						if(isset($counter) and $counter!="") {
							$getuser_add = $db->query("SELECT `user` FROM `".$db_name."` WHERE titel LIKE '$name' "); 
							$user_db = $getuser_add->fetch_assoc()['user'];
							$counter_insert=$counter+$playcount;
							$user_insert=$user_db."&&".$user_in;
							$update = $db->query("UPDATE `".$db_name."` SET user = '$user_insert', playcount ='$counter_insert'  where titel = '$name'");
						}
						else {
							$insert = $db->query("INSERT INTO `".$db_name."` (playcount, artist, user, titel) VALUES ('$playcount', '$artist_name', '$user_in', '$name')"); 
						}
						$counter="";
					}
				}
				unset($out);
			}
			$d++;
		}  
	}
	
######################################################################################################################################################
	function refresh3($db_name, $command, $db, $api_key) {
		$getusers = $db->query("SELECT `username` FROM `ldkf_lastfm`"); 
 		while ($getuser = $getusers->fetch_assoc()) {
 			$users[]=$getuser['username'];
 		}	
		$d=0;
		$delete = $db->query("DELETE FROM ".$db_name);
		foreach($users as $user_in){
			$methode="method=".$command."&user=".$user_in;
			$out = post($methode, $api_key);
			if(isset($out)) {
				$user_info_array = get_object_vars(json_decode($out));
				if(isset($user_info_array['weeklytrackchart'])) {
					$user_info = get_object_vars($user_info_array['weeklytrackchart']);	
					foreach($user_info['artist'] as $top) {
						$info=get_object_vars($top);
						$name=rep( $info["name"]);
						$name_art=rep($info['artist']);
						$playcount=$info["playcount"];
						$url=$info["url"];
						$image = get_object_vars($info["image"][0]);
						$image_path=$image['#text'];
						if(!isset($image_path) or $image_path=="") {
							$image_path="pic/empty.png";
						}
						$getcount = $db->query("SELECT `playcount` FROM ".$db_name." WHERE titel LIKE '$name'"); 
						$counter =	$getcount->fetch_assoc()['playcount'];
						if(isset($counter) and $counter!="") {
							$getuser_add = $db->query("SELECT `user` FROM ".$db_name." WHERE titel LIKE '$name' "); 
							$user_db = $getuser_add->fetch_assoc()['user'];
							$counter_insert=$counter+$playcount;
							$user_insert=$user_db."&&".$user_in;
							$update = $db->query("UPDATE ".$db_name." SET user = '$user_insert', playcount ='$counter_insert'  where titel = '$name'");
						}
						else {
							$insert = $db->query("INSERT INTO ".$db_name." (playcount, artist, user, titel) VALUES ('$playcount', '$name_art', '$user_in', '$name')"); 
						}
						$counter="";
					}
				}
				unset($out);
			} 
			$d++;
		}  
	}

?>
