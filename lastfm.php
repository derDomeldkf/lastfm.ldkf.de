<?php
	session_start();
	$user_in="";
	$page="";
	$totalPages="";
	$user_in="";
	$perPage="";
	$totalTracks="";
	$image="";
	$totaltracks="";
	$starttime="";
 	include "include/db_connect.php";
	include "include/functions.php";
	if(!isset($_GET['token'])) {
		if (isset($_GET['login'])){
			$method_in=	11;
		}
		else {
			if(!isset($_GET['method'])) {
				$method_in=$_POST['method'];
			}
			else {
				$method_in=$_GET['method'];
			}
		}
	}
	else {
		$api_key="830d6e2d4d737d56aa1f94f717a477df";
		$secret="1a05eab1f6dba7de78d59a6c94267464";
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
				echo $error=1;
        	}
        	else {
            $user = simplexml_load_string($result);
           	$sk=$user->session->key; 
           	//immer da, speichern mit username
           	$username=$user->session->name;
            $getid = mysql_fetch_row(mysql_query("SELECT `id` FROM `last_fm_users` WHERE username LIKE '$username'")); 
				$getid_user=$getid[0];
				if(!isset($getid_user) or $getid_user=="") {
					$eintrag = "INSERT INTO last_fm_users (username, session, sig) VALUES ('$username', '$sk', '$sig')"; 
   				$eintragen = mysql_query($eintrag);
					$error=2;
            }
            else{
            	$getsession = mysql_fetch_row(mysql_query("SELECT `session` FROM `last_fm_users` WHERE username LIKE '$username'")); 
					$getsession_user=$getsession[0];
            	if(!isset($getsession_user) or $getsession_user!=$sk) {
						$update = mysql_query("UPDATE last_fm_users SET session = '$sk', sig ='$sig' where username = '$username'");  
						$error=2;
	           	}
	           	else {
						$error=3;	           	
	           	
	           	}
            }
            $uname_db=$username;
         	$_SESSION['user']=$uname_db;
         	$_SESSION['sk']=$sk;
				$_SESSION['sig']=$sig;
        	}
    	}
    	$method_in=$_GET['method_came'];
	}
	if(isset($_POST['username']) or isset($uname_db) and $uname_db!="") {
		if(isset($_POST['username'])) {
			$user_in = $_POST['username'];
		}
		else {
			$user_in=$uname_db;
		}
		$getsession = mysql_fetch_row(mysql_query("SELECT session, sig FROM `last_fm_users` WHERE username LIKE '$user_in'")); 
		$getsession_user=$getsession[0];
		$getsig_user=$getsession[1];
		if(isset($getsession_user) and $getsession_user!="") {
			$_SESSION['user']=$user_in;
			$_SESSION['sig']=$getsig_user;
			$_SESSION['session']=$getsession_user;
			echo $getsig_user;
			echo $getsession_user;
		}
	}
	else {
		if(isset($_GET['method'])){
			$method_in=$_GET['method'];
		}
		elseif($method_in==1 or $method_in==4 or $method_in==8) {
			if($method_in==1){
				header('Location: https://telegram.me/ldkf_bot');
			}
		}
		else {
			if(isset($_GET['login'])) {
			}
			else {
				header('Location: ./');
			}
		}
	}
	
	if(isset($_POST['pagein'])) {
		$page_in=$_POST['pagein'];
	}
	else {
		$page_in=1;
	}
	if(isset($_POST['limitin'])) {
		$limit_in=$_POST['limitin'];
	}
	elseif($method_in==2 or $method_in==5) {
		$limit_in=15;
	}
	else {
		$limit_in=20;
	}
	if(isset($user_in) and $user_in!="") {
		$methode="method=user.getInfo&user=".$user_in;
		$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
		if($out_user!='{"error":6,"message":"User not found","links":[]}') {
			$decode_Info_User=json_decode($out_user);
			$user_info_forimage_array = get_object_vars($decode_Info_User)['user'];
			$user_name_info = get_object_vars($decode_Info_User)['user']->name;
			$totalTracks = get_object_vars($decode_Info_User)['user']->playcount;
			$starttime = get_object_vars($decode_Info_User)['user']->registered->unixtime;
			$user_info_forimage = get_object_vars($user_info_forimage_array)['image'];
			$userimage = get_object_vars($user_info_forimage[1]);
			$account_image=$userimage['#text'];
			if(!isset($account_image) or $account_image=="") {
				$image="pic/empty.png";
			}
			else {
				$image_db =  str_replace(".png", "",$account_image);
				$image_db =  str_replace("http://img2-ak.lst.fm/i/u/64s/", "",$image_db);
				$getimage = mysql_query("SELECT `id` FROM `last_fm_user_pics` WHERE name LIKE '$image_db'"); 
				$getimages = mysql_fetch_row($getimage);
				$getimage_row=$getimages[0];
				if(!isset($getimage_row) or $getimage_row=="") {
					$pfad="user_pics/".$image_db.".png";
					copy($account_image, $pfad);
					$eintrag = "INSERT INTO last_fm_user_pics (name) VALUES ('$image_db')"; 
   				$eintragen = mysql_query($eintrag);
				}
				$image="user_pics/".$image_db.".png"; 
			}
			if($method_in==2) {
				if(!isset($_COOKIE['login']) and isset($_POST['start']) and $_POST['start']==1) {
					setcookie("login", $user_in, time()+(3600*24*365));  
				}  
				$methode="method=user.getRecentTracks&user=".$user_in."&page=".$page_in."&limit=".$limit_in."&extended=1&nowplaying=true";
				$out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
				$decode=json_decode($out);
				$user_info_array = get_object_vars($decode->recenttracks);
				$user_decode= $user_info_array['@attr'];
				$username = $user_decode->user;
				$page = $user_decode->page;
				$perPage = $user_decode->perPage;
				$totalPages = $user_decode->totalPages;
				$tracks= $decode->recenttracks->track;
				$totaltracks=$totalTracks;
			}
			if($method_in==5) {
				$methode="method=user.getLovedTracks&user=".$user_in."&page=".$page_in."&limit=".$limit_in."&extended=1&nowplaying=true";
				$out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
				if(isset($out)) {
					$decode=json_decode($out);
					$user_info_array_love = get_object_vars($decode->lovedtracks);
					$user=$user_info_array_love['@attr'];
					$tracks= $user_info_array_love['track'];
					$username = $user->user;
					$page = $user->page;
					$perPage = $user->perPage;
					$totalPages = $user->totalPages;
					$totaltracks=$user->total;
				}
			}
			if($method_in==6) {
				$methode="method=user.getTopArtists&user=".$user_in."&page=".$page_in."&limit=".$limit_in."&extended=1&nowplaying=true";
				$out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
				if(isset($out)) {
					$decode=json_decode($out);
					$user_info_array_love = get_object_vars($decode->topartists);
					$user=$user_info_array_love['@attr'];
					$tracks= $user_info_array_love['artist'];
					$username = $user->user;
					$page = $user->page;
					$perPage = $user->perPage;
					$totalPages = $user->totalPages;
					$totaltracks=$user->total;
				}
			}
			if($method_in==7) {
				$methode="method=user.getTopTracks&user=".$user_in."&page=".$page_in."&limit=".$limit_in."&extended=1&nowplaying=true";
				$out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
				if(isset($out)) {
					$decode=json_decode($out);
					$user_info_array_love = get_object_vars($decode->toptracks);
					$user=$user_info_array_love['@attr'];
					$tracks= $user_info_array_love['track'];
					$username = $user->user;
					$page = $user->page;
					$perPage = $user->perPage;
					$totalPages = $user->totalPages;
					$totaltracks=$user->total;
				}	
			}
		}
		else {
			$method_in=0;
		}
	}
?>
<html>
	<head>
		<meta charset="UTF-8">
		<link rel="icon" href="favicon.png">
		<link href="https://msn.ldkf.de/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://msn.ldkf.de/css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="css/main.css" rel="stylesheet">
		<title><?php if($method_in==4 or $method_in==8 or $method_in==9 or $method_in==10){echo "LDKF-Gruppe";} elseif($method_in==2){echo "Musikprofil";} elseif(isset($user_in)) {echo $user_in;}?></title>
	</head>
	<body style="font-family: ubuntu-m;">
		<div id="content" class="main-content" role="main" style="">
			<nav class="navbar navbar-inverse navbar-static-top">
	  			<div class="container-fluid" id="navigation" style="display:block;">
    			<!-- Brand and toggle get grouped for better mobile display -->
    				<div class="navbar-header">
    					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            	   	<span class="sr-only">Toggle navigation</span>
            	      <span class="icon-bar"></span>
  	                <span class="icon-bar"></span>
   	                <span class="icon-bar"></span>
      	         </button>
    					<a class="navbar-brand" href="http://ldkf.de" target="_blank">LDKF.de</a>
    				</div>
       			<div id="navbar" class="navbar-collapse collapse">
      				<ul class="nav navbar-nav">
        					<li><a href="./">Startseite<span class="sr-only">(current)</span></a></li>
        					<?php
        						echo nav($method_in, $user_in, $image, $totalTracks, $starttime, $totaltracks);
							?>					
	  				</div>
  				</div>
			</nav>
				<div class="main" style="margin-left:0; padding-bottom:70px;">
					<section class="tracklist-section">
						<?php 
							switch($method_in) {
								case 0:
									echo '<div style="margin:40px;"><h3>Benutzer "'.$user_in.'" existiert nicht.</h3></div>';
								case 1:
									break;
								case 2:
									include "user_tracks.php";	 
									break;
								case 3:
									switch($error) {
										case 1:
											echo "Es gab einen Fehler, versuche es noch einmal.";        				
											break;
										case 2:
											echo "Du wurdest erfolgreich zur Gruppe hinzugef&uuml;gt.";        				
											break;
										case 3:
											echo "Du bist bereits Mitglied dieser Gruppe.";
											break;
										default:
											echo "";
											break;
									}				
									break;
								case 4:
									$db_name="last_fm_charts";
									$period="In der letzten Woche gehört von";
									echo group($db_name, $period);	   
									break;
								case 5:
									include "user_love_track.php";	        				
									break;
								case 6:
									include "user_topartist.php";	        				
									break;
								case 7:
									include "user_toptrack.php";	        				
									break;
								case 8:
									$db_name="last_fm_charts_all";
									$period="Gehört von";
									echo group($db_name, $period);		   
									break;
								case 9:
									$db_name="last_fm_charts_track_all";
									$period="Gehört von";
									echo group2($db_name, $period);	   
									break;
								case 10:
									$db_name="last_fm_charts_track";
									$period="In der letzten Woche gehört von";
									echo group2($db_name, $period);	   
									break;
								case 11:
									$method=$_GET['methodlogin'];
									header('Location: http://www.last.fm/api/auth?api_key=830d6e2d4d737d56aa1f94f717a477df&cb=https://lastfm.ldkf.de/lastfm.php?method_came='.$method.'');
									break;

									
								default:
									break;
							}					
						?>
					</section>
				</div>
			<?php 
				echo footer($method_in, $page, $totalPages, $user_in, $limit_in, $perPage);
			?>
		</div>
      <script type="text/javascript" src="https://msn.ldkf.de/js/jquery-1.11.2.min.js"></script>
   	<script type="text/javascript" src="https://msn.ldkf.de/js/bootstrap.min.js"></script>
 			<script type="text/javascript">
            $(document).ready(function(){
                $('body').on('hidden.bs.modal', '.modal', function () {
                    $(this).removeData('bs.modal');
                });   
            });
         </script>
	</body>
</html> 