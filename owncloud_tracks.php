<?php
	include "include/config.php";
 	include "include/db_connect.php";
 	include "include/functions.php";
	require_once('getid/getid3/getid3.php');
	$delete = $db->query("DELETE FROM `track`");	
	function insert_info($path, $artist, $album, $track, $time, $db) {
		$getartist = $db->query("SELECT `id` FROM `artists` WHERE name LIKE '$artist'"); 
		if(isset($getartist->num_rows) and  $getartist->num_rows!= 0) {
			$aid = $getartist->fetch_assoc()['id'];
		}
		else {
			$insert = $db->query("INSERT INTO `artists` (name) VALUES ('$artist')"); 
			$getartist = $db->query("SELECT `id` FROM `artists` WHERE name LIKE '$artist'"); 
			$aid = $getartist->fetch_assoc()['id'];
		}
		$getalbum = $db->query("SELECT `id` FROM `album` WHERE name LIKE '$album'"); 
		if(isset($getalbum->num_rows) and  $getalbum->num_rows!= 0) {
			$alid = $getalbum->fetch_assoc()['id'];
		}
		else {
			$insert = $db->query("INSERT INTO `album` (name, artist) VALUES ('$album', '$aid')"); 	
			$getalbum = $db->query("SELECT `id` FROM `album` WHERE name LIKE '$album'"); 
			if(isset($getalbum->num_rows) and  $getalbum->num_rows!= 0) {
				$alid = $getalbum->fetch_assoc()['id'];
			}
		}
		$gettrack = $db->query("SELECT `id` FROM `track` WHERE name LIKE '$track"); 
		if(isset($gettrack->num_rows) and  $gettrack->num_rows!= 0) {
			$tid = $gettrack->fetch_assoc()['id'];
		}
		else {
			if(!isset($alid)) {
				$alid="";			
			}
			$insert = $db->query("INSERT INTO `track` (name, artist, album, time, path) VALUES ('$track', '$aid', '$alid', '$time', '$path')"); 							
		}
	}

	$path="Musik";
	$data[]="";
	function read_dir($path, $data, $db) {
		$dir=scandir($path);
		foreach ($dir as $file) {
			if($file!="." and $file!="..") {
				if(strpos($file,".jpg")===false and strpos($file,".png")===false and strpos($file,".jpeg")===false) {
					if(strpos($file,".")!==false) {
						$getID3 = new getID3;
						$ThisFileInfo = $getID3->analyze($path."/".$file);
						getid3_lib::CopyTagsToComments($ThisFileInfo);
						$path_in = $path."/".$file;
						$artist= !empty($ThisFileInfo['comments_html']['artist']) ? $ThisFileInfo['comments_html']['artist'][0] : "";
						$album= !empty($ThisFileInfo['comments_html']['album']) ? $ThisFileInfo['comments_html']['album'][0] : "";
						$track= !empty($ThisFileInfo['comments_html']['title']) ? $ThisFileInfo['comments_html']['title'][0] : "";
						$time= !empty($ThisFileInfo['playtime_string']) ? $ThisFileInfo['playtime_string'] : "";
						if($track!="") {
							$track=rep($track);
							
							echo $track;
 							$artist=rep($artist);
							$path_in=rep($path_in);	
							$album=rep($album);	
							insert_info($path_in, $artist, $album, $track, $time, $db);
						}
					}
					else{
						read_dir($path.'/'.$file, $data, $db);
					}
				}
			}
		}
		return $data;
	}
	read_dir($path, $data, $db);


?>