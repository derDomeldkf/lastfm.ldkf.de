<?php
/*array(1) {
	["user"]=> object(stdClass)#125 (12) { 
		["name"]=> string(15) "microsofthater2" 
		["image"]=> array(4) { 
			[0]=> object(stdClass)#126 (2) { 
				["#text"]=> string(66) "http://img2-ak.lst.fm/i/u/34s/4dea988a408f4db7cdb418360807f892.png" 
				["size"]=> string(5) "small" } [1]=> object(stdClass)#127 (2) { 
				["#text"]=> string(66) "http://img2-ak.lst.fm/i/u/64s/4dea988a408f4db7cdb418360807f892.png" 
				


<!--string(890)
"{
  "recenttracks":{
    "track":[
      {
	"artist":{
	  "#text":"Breaking Benjamin",*
	  "mbid":"854a1807-025b-42a8-ba8c-2a39717f1d25"
	 }, number to fetch. Defaults to first page.
api_key (Required) : A Last.fm API key.
Auth
	 "name":"Polyamorous",*
	 "streamable":"0",
	 "mbid":"9dc017a9-5f78-408b-a4cc-17dbc3ce9196",
	 "album":{
	    "#text":"Saturate",*
	    "mbid":"91d85c0e-319f-45c4-a863-026ef06774fd"
	 },
	  "url":"http://www.last.fm/music/Breaking+Benjamin/_/Polyamorous",*
	  "image":[
	    {
	      "#text":"http://img2-ak.lst.fm/i/u/34s/4c36b492f3244ce1cc4d527e4286d812.png",
	      "size":"small"
	    },
	    {
	      "#text":"http://img2-ak.lst.fm/i/u/64s/4c36b492f3244ce1cc4d527e4286d812.png",*
	      "size":"medium"
	    },
	    {
	      "#text":"http://img2-ak.lst.fm/i/u/174s/4c36b492f3244ce1cc4d527e4286d812.png",
	      "size":"large"
	    },
	    {
	      "#text":"http://img2-ak.lst.fm/i/u/300x300/4c36b492f3244ce1cc4d527e4286d812.png",
	      "size":"extralarge"
	    }
	  ],
	  "date":{
	    "uts":"1444467555",*
	    "#text":"10 Oct 2015, 08:59"*
	  }
      }
    ],
    "@attr":{
      "user":"microsofthater2",*
      "page":"100",*
      "perPage":"1",*
      "totalPages":"8809",*
      "total":"8809"
    }
  }
}" */
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
		if(!isset($_GET['method'])) {
			$method_in=$_POST['method'];
		}
		else {
			$method_in=$_GET['method'];
		}
	}
	else {
		$method_in=3;	
	}
	if(isset($_POST['username'])) {
		$user_in = $_POST['username'];
		if($method_in==3) {
			header('Location: http://www.last.fm/api/auth?api_key=830d6e2d4d737d56aa1f94f717a477df&cb=https://lastfm.ldkf.de/lastfm.php');
		}
	}
	else {
		if(isset($_GET['token']) and $_GET['token']!="") {
			$method_in=3;
			$token=$_GET['token'];
 			$sig = md5("api_key830d6e2d4d737d56aa1f94f717a477dfmethodauth.getSessiontoken".$token."1a05eab1f6dba7de78d59a6c94267464");
			$methode="'method=auth.getSession&token='".$token."'&api_sig='".$sig; 
			exec("python get.py $methode", $out);
			$decode=json_decode($out[0]);
			$info_array = get_object_vars($decode);
		// var_dump($info_array);
		//	$keyy=$info_array['session']->key;
		//	$methode="'method=track.love&track=Deify&artist=Disturbed&api_sig='".$sig."'&sk='".$keyy;
		//	exec("python get.py $methode", $outa);
	  	// var_dump($outa[0]);
			
			if(isset($info_array['error'])) {
				$error=1; //fehler bei übermittlung
			}
			else {
				$info = get_object_vars($info_array['session']);
				$user_in=$info['name'];
				$getname = mysql_query("SELECT `id` FROM `ldkf_lastfm` WHERE `username` LIKE '$user_in'"); 
				$namecheck = mysql_fetch_row($getname);
				$user = $namecheck[0];
				if(!isset($user) or $user=="") {
					$eintrag = "INSERT INTO ldkf_lastfm (username) VALUES ('$user_in')"; 
    				$eintragen = mysql_query($eintrag);
					$error=2; //ERFOLG
				}
				else {
					$error=3; //Bereits Mitglied				
				}
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
		$methode="'method=user.getInfo&user='".$user_in;
		exec("python get.py $methode", $out_user);
		if($out_user[0]!='{"error":6,"message":"User not found","links":[]}') {
			$decode_Info_User=json_decode($out_user[0]);
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
				$methode="'method=user.getRecentTracks&user='".$user_in."'&page='".$page_in."'&limit='".$limit_in."'&extended=1&nowplaying=true'";
				exec("python /var/www/projekte/last_fm/get.py $methode", $out);
				$decode=json_decode($out[0]);
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
				$methode="'method=user.getLovedTracks&user='".$user_in."'&page='".$page_in."'&limit='".$limit_in."'&extended=1&nowplaying=true'";
				exec("python /var/www/projekte/last_fm/get.py $methode", $out);
				if(isset($out[0])) {
					$decode=json_decode($out[0]);
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
				$methode="'method=user.getTopArtists&user='".$user_in."'&page='".$page_in."'&limit='".$limit_in."'&extended=1&nowplaying=true'";
				exec("python /var/www/projekte/last_fm/get.py $methode", $out);
				if(isset($out[0])) {
					$decode=json_decode($out[0]);
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
				$methode="'method=user.getTopTracks&user='".$user_in."'&page='".$page_in."'&limit='".$limit_in."'&extended=1&nowplaying=true'";
				exec("python /var/www/projekte/last_fm/get.py $methode", $out);
				if(isset($out[0])) {
					$decode=json_decode($out[0]);
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
    	<link href="https://msn.ldkf.de/css/lightbox.css" rel="stylesheet">
    	<link href="https://msn.ldkf.de/css/bootstrap-theme.min.css" rel="stylesheet">
		<title><?php if($method_in==4 or $method_in==8 or $method_in==9){echo "LDKF-Gruppe";} elseif($method_in==2){echo "Musikprofil";} elseif(isset($user_in)) {echo $user_in;}?></title>
			<style>
				html, body {
   			 	height: 100%;
				}
				@font-face {
					font-family: 'ubuntu-m';
					src: url('fonts/Ubuntu-R.ttf')
				}
				.option {
					padding-top:4px;
					padding-bottom:4px
				}
				.lyric{ 
					height:24px; 
					width: 24px;
					background: url("pic/lyrics.png") ; 
					background-size: cover;
				}
				.lyric:hover { 
					height:24px; 
					width: 24px;
					background: url("pic/lyrics_over.png") ; 
					background-size: cover; 
				}
				.member{
					position:relative; 
					background-color: #2B7824; 
					color:white; 
					float:left; 
					margin: 0px 40px 20px 0; 
					padding:12px;
					padding-top: 23px;
					padding-bottom: 23px;
					border-bottom-right-radius:6px;
					border-top-right-radius:6px;

				}
				.form_member {
					padding: 0;
					margin: 0;
				}
				.main-content{
					position: relative;
    				min-height: 100%;
				}
				.list {
				   padding: 2px 0px 2px 0px;
				   border-collapse: collapse;
  					border-bottom: 1px solid #d2d2d2;
				}	
				.list_image {
					padding-right:10px;
				   border-collapse: collapse;
  					border-bottom: 1px solid #d2d2d2;
				}	
				.table_head	{
					padding-bottom: 5px;
					padding-top: 5px;				
				}
				.navfooter{
					padding: 0px 5px 0px 5px;
				}
				.main{
					margin-left:40px;
									
				}
				.textunter span {
    				position: absolute;
    				color: white;
					left: 2px;
				}
				.textunter{
					position:relative; 
				}
				.footer{
					position:absolute;
					background-color:#222; 
					width:100%;
					min-width:500px;
					color:white;
					padding:10px 1px 10px 100px;
				   bottom: 0;
				}
				.userButton {
					border-style: none;
					background-color: rgba(43, 120, 36, 0);
					display:inline-block;
					cursor:pointer;
					color:#ffffff;
					padding:0 0 0 0;
					text-decoration:none;
				}
				.userButton:hover {
 					color: #D8D8D8;
 					text-decoration: none;
  				}
				.userButton:active {
					position:relative;
				}

			</style>

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
											echo "Du wurdest erfolgreich zu dieser Gruppe hinzugef&uuml;gt.";        				
											break;
										case 3:
											echo "Du bist bereits Mitglied in dieser Gruppe.";
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