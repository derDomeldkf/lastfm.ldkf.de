<?php
	session_start();
	$td='<td class="list table_head"';
	$user_in="";
	$page="";
	$totalPages="";
	$user_in="";
	$perPage="";
	$totalTracks="";
	$image="";
	$totaltracks="";
	$starttime="";
	include "include/config.php";
 	include "include/db_connect.php";
	include "include/functions.php";
	if(!isset($_GET['token'])) {
		if(isset($_COOKIE['user']) and $_COOKIE['user']!="") {	
			$uname_db = $_COOKIE['user'];
			$username=md5($uname_db);
			$getid = $db->query("SELECT session FROM `last_fm_users` WHERE username LIKE '$username'"); 
			$sk=$getid->fetch_assoc()['session'];
			$getid = $db->query("SELECT sig FROM `last_fm_users` WHERE username LIKE '$username'"); 
			$sig=$getid->fetch_assoc()['sig'];
        	$_SESSION['user']=$uname_db;
        	$_SESSION['session']=$sk;	
			$_SESSION['sig']=$sig;
		}
		if(isset($_GET['user']) and $_GET['user']!="") {
			$uname_db = $_GET['user'];
		}
		
		if (isset($_GET['login'])){
			$method_in=$_GET['methodlogin'];
			$page_in=$_GET['page'];
			$limit_in=$_GET['limit'];
  			header('Location: http://www.last.fm/api/auth?api_key='.$api_key.'&cb=https://lastfm.ldkf.de/lastfm.php?mpl='.$method_in.'_'.$page_in.'_'.$limit_in.'');
		}
		elseif (isset($_GET['logout'])){
			$user_in=$_GET['user'];
			logout($user_in, $db);
			$method_in=$_GET['methodlogout'];
			$page_in=$_GET['page'];
			$limit_in=$_GET['limit'];
			$uname_db=$user_in;
		}
		else {
			if(!isset($_GET['method'])) {
				if(isset($_GET['method_get'])) {				
					$method_in=$_GET['method_get'];
				}
				elseif(isset($_POST['method'])) {
					$method_in=$_POST['method'];
				}
				else {
					$method_in=2;
				}
			}
			else {
				$method_in=$_GET['method'];
			}
		}
	}
	else {
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
    	}
    	$mpl=explode("_", $_GET['mpl']);
    	$method_in=$mpl[0];
    	$page_in=$mpl[1];
    	$limit_in=$mpl[2];
	}
	if(isset($_POST['username']) or isset($uname_db) and $uname_db!="") {
		if(isset($_POST['username'])) {
			$user_in = $_POST['username'];
		}
		else {
			$user_in=$uname_db;
		}
		if(!isset($_COOKIE['login'])) {
			setcookie('login', $user_in, time()+(3600*24*365));  
		}   
	}
	else {
		if($method_in==1 or $method_in==4 or $method_in==8 or $method_in==9 or $method_in==10) {
			if($method_in==1){
				header('Location: https://telegram.me/ldkf_bot');
			}
		}
		else {
			if(!isset($_GET['login'])) {
				header('Location: ./');
			}
		}
	}
	if(!isset($_GET['mpl']) and !isset($_GET['logout'])) {
		if(isset($_POST['pagein'])) {
			$page_in=$_POST['pagein'];
		}
		elseif(isset($_GET['pagein'])) {
			$page_in=$_GET['pagein'];
		}
		else {
			$page_in=1;
		}
		if(isset($_POST['limitin'])) {
			$limit_in=$_POST['limitin'];
		}
		elseif(isset($_GET['limitin'])) {
			$limit_in=$_GET['limitin'];
		}
		elseif($method_in==2 or $method_in==5) {
			$limit_in=15;
		}
		else {
			$limit_in=20;
		}
	}
	if(isset($user_in) and $user_in!="") {
		$methode="method=user.getInfo&user=".$user_in;
		$out_user = post($methode, $api_key);
		if($out_user!='{"error":6,"message":"User not found","links":[]}') {
			$decode_Info_User=json_decode($out_user);
			$user_info_forimage_array = get_object_vars($decode_Info_User)['user'];
			$user_name_info = get_object_vars($decode_Info_User)['user']->name;
			$totalTracks = get_object_vars($decode_Info_User)['user']->playcount;
			$starttime = get_object_vars($decode_Info_User)['user']->registered->unixtime;
			$user_info_forimage = get_object_vars($user_info_forimage_array)['image'];
			$userimage = get_object_vars($user_info_forimage[1]);
			$getimage = $db->query("SELECT `name` FROM `last_fm_user_pics` WHERE user LIKE '$user_in'"); 
			$getimage_row = $getimage->fetch_assoc()['name'];
			if(isset($getimage_row)and $getimage_row!="") {
				$image="user_pics/".$getimage_row.".png";
			}
			else {
				$image="pic/empty.png";
			}
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
		<title><?php if($method_in==4 or $method_in==8 or $method_in==9 or $method_in==10){echo "LDKF-Gruppe";} elseif(isset($user_in)) {echo $user_in;} else { echo "lastfm.ldkf.de";}?></title>
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
        						echo nav($method_in, $user_in, $image, $totalTracks, $starttime, $totaltracks, $db, $page_in, $limit_in, $secret);
							?>					
	  				</div>
  				</div>
			</nav>
				<div class="main" style="margin-left:0; padding-bottom:70px;">
					<section class="tracklist-section">
						<?php 
							switch($method_in) {
								case 0:

								case 1:
									break;
								case 2:
									$methode="method=user.getRecentTracks&user=".$user_in."&page=".$page_in."&limit=".$limit_in."&extended=1&nowplaying=true";
									$out = post($methode, $api_key);
									$decode=json_decode($out);
									$user_info_array = get_object_vars($decode->recenttracks);
									$user_decode= $user_info_array['@attr'];
									$username = $user_decode->user;
									$page = $user_decode->page;
									$perPage = $user_decode->perPage;
									$totalPages = $user_decode->totalPages;
									$tracks= $decode->recenttracks->track;
									$totaltracks=$totalTracks;
									include "user_tracks.php";	 
									break;
								case 3:
									
									break;
								case 4:
									if(!isset($_POST['tableselect'])) {
										$gethighest = $db->query("SELECT MAX(id) FROM `tables`"); 
										$getplaces = $gethighest->fetch_assoc();
										$id=$getplaces['MAX(id)'];
										$post=$id;
									}
									else {
										$id=$_POST['tableselect'];
										$post="";
									}
									$gethighest = $db->query("SELECT time FROM `tables` where id LIKE '$id'"); 
									$getplaces = $gethighest->fetch_assoc();
									$time=$getplaces['time'];
									$date=date('d.m.Y',strtotime($time));
									$getname = $db->query("SELECT `table_name` FROM `tables` WHERE id LIKE '$id' "); 
									$name = $getname->fetch_assoc();
									$db_name=$name['table_name'];
									$period="In der letzten Woche gehört von";
									echo group($db_name, $period, $db, $post, $date, $td);	   
									break;
								case 5:
									$methode="method=user.getLovedTracks&user=".$user_in."&page=".$page_in."&limit=".$limit_in."&extended=1&nowplaying=true";
									$out = post($methode, $api_key);
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
									include "user_love_track.php";	        				
									break;
								case 6:
									$methode="method=user.getTopArtists&user=".$user_in."&page=".$page_in."&limit=".$limit_in."&extended=1&nowplaying=true";
									$out = post($methode, $api_key);
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
									include "user_topartist.php";	        				
									break;
								case 7:
									$methode="method=user.getTopTracks&user=".$user_in."&page=".$page_in."&limit=".$limit_in."&extended=1&nowplaying=true";
									$out = post($methode, $api_key);
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
									include "user_toptrack.php";	        				
									break;
								case 8:
									$db_name="last_fm_charts_all";
									$period="Gehört von";
									echo group($db_name, $period, $db, 0, 0, $td);		   
									break;
								case 9:
									$db_name="last_fm_charts_track_all";
									$period="Gehört von";
									echo group2($db_name, $period, $db, $method_in, $td);	   
									break;
								case 10:
									if(!isset($_POST['tableselect'])) {
										$gethighest = $db->query("SELECT MAX(id) FROM `tables_title`"); 
										$getplaces = $gethighest->fetch_assoc();
										$id=$getplaces['MAX(id)'];
										$post=$id;
									}
									else {
										$id=$_POST['tableselect'];
										$post="";
									}
									$gethighest = $db->query("SELECT time FROM `tables_title` where id LIKE '$id'"); 
									$getplaces = $gethighest->fetch_assoc();
									$time=$getplaces['time'];
									$date=date('d.m.Y',strtotime($time));
									$getname = $db->query("SELECT `table_name` FROM `tables_title` WHERE id LIKE '$id' "); 
									$name = $getname->fetch_assoc();
									$db_name=$name['table_name'];
									$period="In der letzten Woche gehört von";
									echo group2($db_name, $period, $db, $method_in, $td);	   
									break;
								case 11:
									
									break;	
								default:
									echo '<div style="margin:40px;"><h3>Benutzer "'.$user_in.'" existiert nicht.</h3></div>';
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