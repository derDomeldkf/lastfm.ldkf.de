<?php

	include "include/config.php";
	include "include/db_connect.php";
	include_once "include/functions.php";
	if (preg_match("/(\w|\d)*/", $_GET['token'])) {
	 	$token=$_GET['token'];
	 	$presig = "api_key" . $api_key . "methodauth.getSessiontoken" . $token . $secret;
	  	$sig = md5($presig);
	  	$url = 'http://ws.audioscrobbler.com/2.0/';
	  	$data = array('api_key' => $api_key, 'method' => 'auth.getSession', 'token' => $token, 'api_sig' => $sig);
	  	$options = array(
	   	'http' => array(
	      	'header' => "Content-type: application/x-www-form-urlencoded\r\n",
	         'method' => 'POST',
	         'content' => http_build_query($data),
	   	),
	  	);
	  	$context = stream_context_create($options);
	  	$result = @file_get_contents($url, false, $context);
	  	if ($result === FALSE) {
			$error=1;
	  	}
	  	else {
	      $user = simplexml_load_string($result);
	     	$sk=$user->session->key; 
	     	$user_n=$user->session;
	     	$uncode_name=get_object_vars($user_n)['name'];
	     	$username = md5($uncode_name);
	      $getid = $db->query("SELECT `id` FROM `last_fm_users` WHERE username LIKE '$username'"); 
			$getid_user=$getid->fetch_assoc()['id'];
			if(!isset($getid_user) or $getid_user=="") {
				$insert = $db->query("INSERT INTO last_fm_users (username, session, sig, stat) VALUES ('$username', '$sk', '$sig', '1')"); 
				$error=2;
	      }
	      else{
	      	$getsession = $db->query("SELECT `session` FROM `last_fm_users` WHERE username LIKE '$username'"); 
				$getsession_user=$getsession->fetch_assoc()['session'];
				$update = $db->query("UPDATE last_fm_users SET session = '$sk', sig ='$sig', stat='1' where username = '$username'");  
				$error=2;
				$getid = $db->query("SELECT session FROM `last_fm_users` WHERE username LIKE '$username'"); 
				$sk=$getid->fetch_assoc()['session'];
				$getid = $db->query("SELECT sig FROM `last_fm_users` WHERE username LIKE '$username'"); 
				$sig=$getid->fetch_assoc()['sig'];
	      }
			$uname_db = $uncode_name;
	  		$_SESSION['user']=$uname_db;
	  		$_SESSION['session']=$sk;
			$_SESSION['sig']=$sig;
			setcookie('user', $uname_db, time()+(3600*24*365));  
		}
		header('Location: ./');
	 }

?> 
