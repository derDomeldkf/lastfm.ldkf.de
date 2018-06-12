<?php
	session_start();
  	if(isset($_POST['f']) and $_POST['f']=="unlove") {
  		$track=($_POST['track']);
  		$artist=($_POST['artist']);
      $sk=$_SESSION['session'];
		$sig=$_SESSION['sig'];
		$methode="method=track.unlove&track=".$track."&artist=".$artist."&api_sig".$sig."&sk=".$sk;
		$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
		echo "nolove";
	}
	if(isset($_POST['f']) and $_POST['f']=="love") {
  		$track=($_POST['track']);
  		$artist=($_POST['artist']);
      $sk=$_SESSION['session'];
		$sig=$_SESSION['sig'];
		//$methode="&track=".$track."&artist=".$artist."&api_sig".$sig."&sk=".$sk;



		$postData = array(
	   	"method" => "track.love", 
	    	"track" => $track,
			"artist" => $artist,
			"api_sig" => $sig,
			"sk" => $sk,
			"api_key" => "830d6e2d4d737d56aa1f94f717a477df",

	    	"format" => "json"
		);
	
		$url = 'https://ws.audioscrobbler.com/2.0/'; 
	 
	   $options = array(
			'http' => array(
	      	'header' => "Content-type: application/x-www-form-urlencoded",
	      	'method' => 'POST',
	      	'content' => http_build_query($postData),
	  		),
	  	);
	   $context = stream_context_create($options);
	   $result = @file_get_contents($url, false, $context);
	   echo 	$result;
		
		
		
		//$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=&" . $methode);
		//echo "love";
	}
?> 
