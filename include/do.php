<?php
	session_start();
  	if(isset($_POST['f']) and $_POST['f']=="unlove") {
  		$track=urlencode($_POST['track']);
  		$artist=urlencode($_POST['artist']);
      $sk=$_SESSION['session'];
		$sig=$_SESSION['sig'];
		$methode="method=track.unlove&track=".$track."&artist=".$artist."&api_sig".$sig."&sk=".$sk;
		$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
		echo "nolove";
	}
	if(isset($_POST['f']) and $_POST['f']=="love") {
  		$track=urlencode($_POST['track']);
  		$artist=urlencode($_POST['artist']);
      $sk=$_SESSION['session'];
		$sig=$_SESSION['sig'];
		$methode="method=track.love&track=".$track."&artist=".$artist."&api_sig".$sig."&sk=".$sk;
		$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
		echo "love";
	}
?> 
