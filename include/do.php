<?php
	session_start();
  	if(isset($_GET['f']) and $_GET['f']=="unlove") {
  		$track=urlencode($_GET['track']);
  		$artist=urlencode($_GET['artist']);
      $sk=$_SESSION['session'];
		$sig=$_SESSION['sig'];
		$methode="method=track.unlove&track=".$track."&artist=".$artist."&api_sig".$sig."&sk=".$sk;
		$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
		$meth=$_GET['meth'];
		$lim=$_GET['lim'];
		header('Location: ../lastfm.php?method_get='.$meth.'&limitin='.$lim);
	}
	if(isset($_GET['f']) and $_GET['f']=="love") {
  		$track=urlencode($_GET['track']);
  		$artist=urlencode($_GET['artist']);
      $sk=$_SESSION['session'];
		$sig=$_SESSION['sig'];
		$methode="method=track.love&track=".$track."&artist=".$artist."&api_sig".$sig."&sk=".$sk;
		$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
		$meth=$_GET['meth'];
		$lim=$_GET['lim'];
		header('Location: ../lastfm.php?method_get='.$meth.'&limitin='.$lim);
	}
?> 
