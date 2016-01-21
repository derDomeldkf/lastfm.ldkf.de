<?php
	session_start();
  	$sig=$_COOKIE['sig'];
	$sk=$_COOKIE['session'];
  	if(isset($_GET['f']) and $_GET['f']=="love") {
  		$track=$_GET['track'];
  		$artist=$_GET['artist'];
		$methode="method=track.love&track=".$track."&artist=".$artist."&api_sig".$sig."&sk=".$sk;
		$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
	}
?> 
