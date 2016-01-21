<?php
	session_start();
  	$sig="b86a7b4762183d1bf9a3c8a3d4ca3b8b";
	$sk="5f2da3461830257b8fe29f83047fd0da";
  	if(isset($_GET['f']) and $_GET['f']=="love") {
  		$track=$_GET['track'];
  		$artist=$_GET['artist'];
		$methode="method=track.love&track=".$track."&artist=".$artist."&api_sig".$sig."&sk=".$sk;
		$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
	}
?> 
