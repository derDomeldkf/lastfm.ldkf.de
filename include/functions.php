<?php



#################################################################################################################################################################
	
	include "refresh.functions.php"; 
 	include "group.functions.php";
  	 
#################################################################################################################################################################
	
	function get_info($method, $user_in, $page_in, $limit_in, $api_key){
		$methode="method=user.get".$method."&user=".$user_in."&page=".$page_in."&limit=".$limit_in."&extended=1&nowplaying=true";
		$out = post($methode, $api_key);
		$decode=json_decode($out);
		$get_decode=strtolower($method);
		$user_info_array = get_object_vars($decode->$get_decode);
		$user_decode= $user_info_array['@attr'];
		strpos($get_decode,"artist")!==false ?	$get_array="artist" : $get_array="track";
		$user[0] = $user_info_array[$get_array];
		$user[1] = $user_decode->user;
		$user[2] = $user_decode->page;
		$user[3] = $user_decode->perPage;
		$user[4] = $user_decode->totalPages;
		$user[5] = $user_decode->total;
		return $user;
	}

#################################################################################################################################################################
	
	function footer_limit($perPage){
		$content=' value="'.$perPage.'">'.$perPage.' Eintr&auml;ge Pro Seite</option>';
		return $content;
   }	     							

#################################################################################################################################################################
	
	function nav_group_right(){
		$content ='
			</ul>
			<ul class="nav navbar-nav navbar-right" style="margin-right:20px;">
      		<li class="dropdown" style="width:200px;">
      			<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
        				<img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
					</a>
					<ul class="navbar-inverse dropdown-menu" style="color:white; font-size:11pt; padding: 15px 10px 15px 30px; border-radius:0; width:240px;">
				'; 
		return $content;
	}
 
################################################################################################################################################################# 
	
	function nav_footer($user_in, $method_in, $limit_in){
 		$content='
			<form action="?" style="margin:0; padding:0;" method="POST">
         <input type="hidden" name="username" value='.$user_in.'>
   		<input type="hidden" name="method" value="'.$method_in.'">
   		<input type="hidden" name="limitin" value="'.$limit_in.'">
   	';
		return $content;
	}
 
#################################################################################################################################################################
	
	function listeners_dropdown($users_names){
		$user =  str_replace("&&", ", ",$users_names);
		if(substr_count($user, ', ')>2){
			$teile = explode(",", $user, 4);
			$teil =  str_replace(", ", '</li><li style="padding-left:15px;">',$teile[3]);
			$user='
				<ul class="nav navbar-nav">
      			<li class="dropdown">
         			<a href="#" class="dropdown-toggle" style="padding:0px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
         				'.$teile[0].', '.$teile[1].', '.$teile[2].' ...
							<span class="caret"></span>
						</a>
         			<ul class="navbar-inverse dropdown-menu" style="border-radius: 6px; width:100%; margin-top:10px; color:white;">
           				<li style="padding-left:15px;">
           					'.$teil.'
           				</li>
						</ul>
						</li> 
				</ul>
			'; 			
		}
		return $user;
	}
			
 
################################################################################################################################################################# 
	
	function post($methode, $api_key){
		$content=file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=".$api_key."&" . $methode);	
		return $content;
	} 
 
#################################################################################################################################################################
 	
 	function audioplayer($db, $secret, $user_in, $api_key)  {	
 		$content="";		
 		$getmembers = $db->query("SELECT `username` FROM `ldkf_lastfm` WHERE `username` LIKE '$user_in'"); 
		if(isset($_GET['p']) and $_GET['p']!="" and isset($getmembers->num_rows) and  $getmembers->num_rows!= 0) {
			$tid=$_GET['p'];
			$getpath = $db->query("SELECT `path` FROM `track` WHERE id LIKE '$tid'"); 
			if(isset($getpath->num_rows) and  $getpath->num_rows!= 0) {
				$path = $getpath->fetch_assoc()['path'];
				$getinfo = $db->query("SELECT artist, album, name FROM `track` WHERE id LIKE '$tid'"); 
				while($info = $getinfo->fetch_assoc()){
					$artist_id=$info['artist'];
					$track=$info['name'];
					$album_id=$info['album'];
				}
				$getinfo = $db->query("SELECT `name` FROM `artists` WHERE id LIKE '$artist_id'"); 
				$artist = $getinfo->fetch_assoc()['name'];
				$getinfo = $db->query("SELECT `name` FROM `album` WHERE id LIKE '$album_id'"); 
				$album = $getinfo->fetch_assoc()['name'];
				$sk=$_SESSION['session'];
				//$sig=$_SESSION['sig'];
				$psig="album". $album ."api_key".$api_key."artist". $artist ."methodtrack.scrobblesk".$sk."timestamp". time() ."track". $track ."".$secret;
				$sig=md5($psig);	
				$methode="method=track.scrobble&track=".$track."&artist=".$artist."&api_sig=".$sig."&sk=".$sk."&timestamp=" . time() ;
				if(isset($album) and $album!="") {
					$methode .="&album=". $album;
				}
				//$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
				$content .='<li style="padding-left:10px; padding-top:11px;"><audio src="'. $path .'" controls onloadstart="this.volume=0.15" autoplay></audio></li>';
			}		
    	}
     	return $content;
	}
 
#################################################################################################################################################################
 	
 	function rep($data){
		$data =  str_replace("&#252;", "ü",$data);
		$data =  str_replace("&#246;", "ö",$data);
		$data =  str_replace("&#228;", "ä",$data);
		$data =  str_replace("&amp;", "&",$data);
		$data =  str_replace("´", "'",$data);
		$data=htmlentities($data, ENT_QUOTES);
		return $data;
	
	}
	
################################################################################################################################################################# 
	
	function play($track_name, $artist_name, $db, $method_in, $limit_in, $page_in, $user_in){
		$content="";
		if(isset($_SESSION['user'])) {
			$track_name_get=mysql_escape_string( utf8_encode(rep($track_name)));
			$artist_name_get=mysql_escape_string( utf8_encode(rep($artist_name)));
			$gettrack = $db->query("SELECT `id` FROM `track` WHERE name LIKE '$track_name_get'"); 
			if(isset($gettrack->num_rows) and  $gettrack->num_rows!= 0) {
				$tid = $gettrack->fetch_assoc()['id'];
				$getartist = $db->query("SELECT `id` FROM `artists` WHERE name LIKE '$artist_name_get'"); 
				if(isset($getartist->num_rows) and  $getartist->num_rows!= 0) {
					$aid = $getartist->fetch_assoc()['id'];
					$content = '
						<td class="list" style="padding:0; padding-left:1px; padding-right:9px;">
							<a href="lastfm.php?p='.$tid.'&user='.$user_in.'&method_get='.$method_in.'&limitin='.$limit_in.'&pagein='.$page_in.'">
								<img src="pic/play.png" width="24px" height="24px">
							</a>
						</td>
					';
				}
				else {
					$content = '
						<td class="list" style="padding:0;">
						</td>
					';
				}	
			}	
			else {
					$content = '
						<td class="list" style="padding:0;">
						</td>
					';
			}		
		} 
		else {
			$content = '
				<td class="list" style="padding:0;">
				</td>
			';
			}	
		return $content;
 	}
 
#################################################################################################################################################################
 	
 	function love($love,$artist_name, $track_name, $method_in, $limit_in, $user_in, $page_in) {
 		if(isset($_SESSION['user']) and $_SESSION['user']==$user_in) {
			if($love==1) {
				$content= '
					<a href="include/do.php?f=unlove&meth='.$method_in.'&lim='.$limit_in.'&page='.$page_in.'&artist='. urlencode($artist_name) .'&track='. urlencode($track_name) .'" style="padding:1px; margin:0;">
						<img width="18px" height="18px;" src="pic/love.png">
					</a>
				';
			}
  			else {
   			$content= '
					<a href="include/do.php?f=love&meth='.$method_in.'&lim='.$limit_in.'&page='.$page_in.'&artist='. urlencode($artist_name) .'&track='. urlencode($track_name) .'" style="padding:1px; margin:0;">
						<img width="18px" height="18px;" src="pic/nolove.png">
					</a>
				';
    		} 
    	}
    	else {
    		if($love==1) {
				$content= '
						<img width="18px" height="18px;" src="pic/love.png">
				';
			}
  			else {
   			$content= '
						<img width="18px" height="18px;" src="pic/nolove.png">
				';
    		} 
     	}
		return $content;
  	}
  	
	
#################################################################################################################################################################
 	
 	function logout($user_in, $db) {
 		$update = $db->query("UPDATE last_fm_users SET stat='0' where username = '$user_in'"); 
 		setcookie("user","",time() - 3600); 
		session_destroy();
  	} 


#################################################################################################################################################################
	
	function select($member, $user_input, $counter_select) {
		$content ='
			<option></option>   					
   	';
		foreach($member as $member_name){
			$content .= '
				<option value="'.$member_name.'"'; 
				if(isset($user_input[$counter_select]) and $member_name==$user_input[$counter_select]) {
				$content .= 'selected';
				
				}
				$content .= '>'.$member_name.'</option>
			';
		}  
		$content .= '</select>';
	
		return $content;
	}	
	
#################################################################################################################################################################	
	
	function footer($method_in, $page, $totalPages, $user_in, $limit_in, $perPage) {	
	 	$content="";
		$page_n=$page+1;
		$page_l=$page-1;
		$content= '
			<div class="nav footer">
				<table>
					<tr>';
					if($method_in==2 or $method_in==5 or $method_in==6 or $method_in==7) {
						$content .= '
						<td class="navfooter" style="color:white;">
            			Seite '.$page.' von '.$totalPages.'
            		</td>
						<td class="navfooter">
					';
					if($page>2) {
						$content .= nav_footer($user_in, $method_in, $limit_in);
						$content .= '  							
   				   		<input type="hidden" name="pagein" value="1">
								<button type="submit" class="btn btn-primary" style="padding-top:6px; padding-bottom:6px;">
									|<<
								</button>
							</form>
						';
					}
					$content .= '
						</td>
						<td class="navfooter">
					';
					if($page!=1) {
						$content .= nav_footer($user_in, $method_in, $limit_in);
						$content .= '
						 		<input type="hidden" name="pagein" value="'. $page_l .'">
								<button type="submit" class="btn btn-primary" style="padding-top:6px; padding-bottom:6px;">
									<<
								</button>
							</form>
						';
					}								
					$content .='
						</td>
						<td class="navfooter">
					';
					if($page<$totalPages) {
						$content .= nav_footer($user_in, $method_in, $limit_in);
						$content .= '
   					   	<input type="hidden" name="pagein" value="'. $page_n .'">
								<button type="submit" class="btn btn-primary" style="padding-top:6px; padding-bottom:6px;">
									>>
								</button>
							</form>
						';
					}
						$content .='
							</td>   
							<td class="navfooter">
						';
						if($page+1<$totalPages) {
							$content .= nav_footer($user_in, $method_in, $limit_in);
							$content .= '
   					   		<input type="hidden" name="pagein" value="'. $totalPages .'">
									<button type="submit" class="btn btn-primary" style="padding-top:6px; padding-bottom:6px;">
										>>|
									</button>
								</form>
							';
							}
							$content .='
							</td>';          			
							$content .='   	         		
   	         		<td class="navfooter">
   	         			<form action="?" style="margin:0; padding:0;" method="POST">
   								<select class="form-control"  name="limitin" id="myselect" onchange="this.form.submit()" style="padding:3px; font-size:12pt" style="padding-top:6px; padding-bottom:6px;">';
   								if($method_in==6 or $method_in==7) {
   									for($i=20; $i<=60; $i=$i+20){
  	 										$content .='<option class="option"'; 
  	 										($perPage==$i) ? $content .= " selected" : "";
   	     								$content .= footer_limit($i);
   	     							}
   	     						}
   	     						else {
  	      							for($i=15; $i<=35; $i=$i+10){
  	 										$content .='<option class="option"'; 
  	 										($perPage==$i) ? $content .= " selected" : "";
   	     								$content .= footer_limit($i);
   	     							}    						
  	      						}
 	  							$content .='</select>
  	 							<input type="hidden" name="username" value='.$user_in.'>
 	  							<input type="hidden" name="method" value="'.$method_in.'">
 	  							<input type="hidden" name="pagein" value="'. $page .'">
							</form>
						</td>';
					}
					$content .='
						<td>
							<span class="text-muted" style="padding-left:30px;"> <a  href="impressum.html" target="_blank">Impressum</a> - <a target="_blank" href="datenschutzerklaerung.html" >Datenschutz</a></span>  
						</td>
					</tr>
				</table>
			</div>
			';
		
  		return $content;
	}
	
#################################################################################################################################################################
	
	function nav($method_in, $user_in, $image, $totalTracks, $starttime, $totaltracks, $db, $page_in, $limit, $secret, $api_key) {

		$content="";
		if((isset($_SESSION['user']) and !isset($_GET['methodlogout'])) and !isset($_GET['methodlogout']) ) {
			$content .='
				<li><a href="./lastfm.php?method_get=2">Home</a></li>
			';
		}
		else {
  				$content .='<li><a href="./">Startseite<span class="sr-only">(current)</span></a></li>';
  			}
		$content .='<li><a href="https://scrobbler.ldkf.de" target="_blank">Scrobbler</a></li>';
		if($method_in==2 or $method_in==5 or $method_in==6 or $method_in==7) {
			$getname = $db->query("SELECT `id` FROM `ldkf_lastfm` WHERE `username` LIKE ('".$user_in."')");
			$user = $getname->fetch_assoc();
			if(isset($user) and $user!="") {
				$content .='<li><a href="./lastfm.php?method=4">Gruppe</a></li>
					<li><a href="http://explr.fm/?username='.$user_in.'" target="_blank">Explr.fm</a></li>
				';
				if((isset($_SESSION['user']) and $_SESSION['user']==$user_in) and !isset($_GET['methodlogout'])) {				
					$content .='<li><a href="./lastfm.php?logout=1&user='.$user_in.'&methodlogout='.$method_in.'&page='.$page_in.'&limit='.$limit.'" >Logout</a></li>';
				}
				elseif(!isset($_SESSION['user']) or isset($_GET['methodlogout'])) {
					$content .='<li><a href="./lastfm.php?login=1&user='.$user_in.'&methodlogin='.$method_in.'&page='.$page_in.'&limit='.$limit.'" >Login</a></li>';
				}
  			}	
			$content .= audioplayer($db, $secret, $user_in, $api_key);
        	$content .= '
        		</ul>
   				<ul class="nav navbar-nav navbar-right" style="margin-right:20px;">
   					<li class="dropdown" style="width:200px;">
      					<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
      						<img style="border-radius: 18px;" width="36px" src="'.$image.'"> '.$user_in.'<span class="caret"></span>
							</a>
         				<ul class="navbar-inverse dropdown-menu" style="color:white; font-size:11pt; padding: 15px 10px 15px 30px; border-radius:0; width:240px;">
           					<li>
           				';
			if($method_in!=2) { 
				$content .='
           		<form class="form_member" method="post" action="lastfm.php">
					<input type="hidden" name="username" value="'.$user_in.'">
					<input type="hidden" name="method" value="2">
					<button type="submit" class="userButton">				
						Scrobbles: '.$totalTracks.'
					</button>
					</form>
				';
			}
			else {
				$content .='<b>Scrobbles: '.$totalTracks.'</b>';
			}
			$content .= '
	      	</li>
   	   	<li>';
      	if($method_in!=5) { 
      		$content .='
      			<form class="form_member" method="post" action="lastfm.php">
						<input type="hidden" name="username" value="'.$user_in.'">
						<input type="hidden" name="method" value="5">
						<button type="submit" class="userButton">
				';
			}
			if($method_in==5) { 
				$content .= '<b>Lieblingslieder: '.$totaltracks.'</b>';
			}
			else {
				$content .= 'Lieblingslieder';
			}
			$content .='</button>
				</form>
				</li>
				<li>';
	      if($method_in!=6) { //also zu 6 gelangen
	      	$content .='
   	      	<form class="form_member" method="post" action="lastfm.php">
						<input type="hidden" name="username" value="'.$user_in.'">
						<input type="hidden" name="method" value="6">
						<button type="submit" class="userButton">
				';
			}
			if($method_in==6) { 
				$content .= '<b>Top K&uuml;nstler: '.$totaltracks.'</b>'; //6, also ausgewählt
			}
			else {
				$content .= 'Top K&uuml;nstler';			
			}
			$content .='</button>
				</form>
				</li>
				<li>
			';
			if($method_in!=7) { //gelange zu 7
				$content .='
  					<form class="form_member" method="post" action="lastfm.php">
						<input type="hidden" name="username" value="'.$user_in.'">
						<input type="hidden" name="method" value="7">
						<button type="submit" class="userButton">
					';
			}
			if($method_in==7) { 
				$content .= '<b>Top Titel: '.$totaltracks.'</b>';
			}
			else {
				$content .= 'Top Titel';
			}
			$content .='</button>
				</form>
				</li>
				<li>Scrobbelt seit: '. gmdate("d.m.Y", $starttime).'</li>
				</ul>
				</li> 
				</ul>
			'; 									
		}
		
		
######################################## Gruppe #################################################################################################################		
		
		if($method_in==4 or $method_in==8 or $method_in==9 or $method_in==10) {
			$content .='
				<li class="dropdown" style="">
   				<a href="#" class="dropdown-toggle" style="" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
   					Mitglieder<span class="caret"></span>
					</a>
         	<ul class="navbar-inverse dropdown-menu" style="color:white; font-size:11pt; padding: 15px 10px 15px 30px; border-radius:0; width:240px;">
  				';
				$getmembers = $db->query("SELECT `username` FROM `ldkf_lastfm` order by `username` ASC"); 
				while($members = $getmembers->fetch_assoc()){
					$member[]=$members['username'];
				}
				foreach($member as $member_name){
					$content .= '
						<li>	
							<form class="form_member" method="post" action="lastfm.php">
								<input type="hidden" name="username" value="'.$member_name.'">
								<input type="hidden" name="method" value="2">
								<button type="submit" class="userButton">'.$member_name.'</button>
							</form>
						</li>
					';
				}  
				$content .= '
					</ul>
				</li>
			';  
			$content .= audioplayer($db, $secret, $user_in, $api_key);   		
        			
		}		
		switch($method_in) {
			case 4:
				$content .=nav_group_right();
        		$content .='  
        				 	<li><b>Top K&uuml;nstler (Woche)</b></li>
       					<li>
        						<div>
									<a class="userButton" href="lastfm.php?method=8">Top Künstler (Gesamt)</a>
								</div>
							</li>
         	  			<li>
      	     				<div>
									<a class="userButton" href="lastfm.php?method=10">Top Titel (Woche)</a>
								</div>
           				</li>
           				<li>
      	     				<div>
									<a class="userButton" href="lastfm.php?method=9">Top Titel (Gesamt)</a>
								</div>
           				</li>
        				</ul>
       			</li> 
       		</ul>
       	';	
				break;
			case 8:
				$content .=nav_group_right();
				$content .='		
           				<li>
           					<div>
									<a class="userButton" href="lastfm.php?method=4">Top K&uuml;nstler (Woche)</a>
								</div>
           				</li>
 	          			<li><b>Top Künstler (Gesamt)</b></li>
   	        			<li>
      	     				<div>
									<a class="userButton" href="lastfm.php?method=10">Top Titel (Woche)</a>
								</div>
           				</li>
           				<li>
      	     				<div>
									<a class="userButton" href="lastfm.php?method=9">Top Titel (Gesamt)</a>
								</div>
           				</li>
        				</ul>
       			</li> 
       		</ul>
       	';						
				break;
			case 9:
				$content .=nav_group_right();
				$content .='
							<li>
           					<div>
									<a class="userButton" href="lastfm.php?method=4">Top K&uuml;nstler (Woche)</a>
								</div>
           				</li>
           				<li>
      	     				<div>
									<a class="userButton" href="lastfm.php?method=8">Top Künstler (Gesamt)</a>
								</div>
           				</li>
   	        			<li>
      	     				<div>
									<a class="userButton" href="lastfm.php?method=10">Top Titel (Woche)</a>
								</div>
           				</li>
           				<li><b>Top Titel (Gesamt)</b></li>
        				</ul>
       			</li> 
       		</ul>
       	';					
				break;
			case 10:
				$content .=nav_group_right();
				$content .='
							<li>
           					<div>
									<a class="userButton" href="lastfm.php?method=4">Top K&uuml;nstler (Woche)</a>
								</div>
           				</li>
           				<li>
      	     				<div>
									<a class="userButton" href="lastfm.php?method=8">Top Künstler (Gesamt)</a>
								</div>
           				</li>
           				<li><b>Top Titel (Woche)</b></li>
   	        			<li>
      	     				<div>
									<a class="userButton" href="lastfm.php?method=9">Top Titel (Gesamt)</a>
								</div>
           				</li>
        				</ul>
       			</li> 
       		</ul>
       	';											
				break;
		}
		return $content;
	}
	
#################################################################################################################################################################	
	
	function image_artist($artist_name, $db) {
		$artist_name_db=$artist_name;
		$getimage = $db->query("SELECT `name` FROM `last_fm_covers` WHERE artist LIKE '$artist_name_db' and album LIKE 'NULL'"); 
		$getimages = $getimage->fetch_assoc()['name'];
		if(isset($getimages) and $getimages!="") {							
			$image="covers/".$getimages; 
		}
		else {
			$image="pic/empty.png";
		}
		$content=
			'<td class="list">
   	  	    <span class="">
          	 	<span class="chartlist-image">
        				<img src="'.$image.'">
      			</span>
 	  			</span>              		
           </td>
      ';
      return $content;
      	
	}
	
#################################################################################################################################################################	
	
	function image($images, $artist_name_get, $db, $album_get) {
		$artist_name=rep($artist_name_get);
		$album=rep($album_get);		
		$content="";
		if($album!="") {
			$getimage = $db->query("SELECT `name` FROM `last_fm_covers` WHERE album LIKE '$album'"); 
			if(isset($getimage->num_rows) and  $getimage->num_rows!= 0) {
				$getimages = $getimage->fetch_assoc()['name'];
				$image="covers/".$getimages; 
			}
			else {
				if(isset($images) and $images!="") {				
					//$image_db =  str_replace("http://img2-ak.lst.fm/i/u/34s/", "",$images);
					//$image_db =  str_replace("https://secure-img2.last.fm/i/u/34s/", "",$image_db);
					//$image_db =  str_replace("https://lastfm-img2.akamaized.net/i/u/34s/", "",$image_db);
							$image_db =	explode("i/u/34s/", $images)[1];
					
					
					$getimage = $db->query("SELECT `id` FROM `last_fm_covers` WHERE name LIKE '$image_db'"); 
					$getimage_row = $getimage->fetch_assoc();
					if(!isset($getimage_row) or $getimage_row=="") {
						$pfad="covers/".$image_db;
						copy($images, $pfad);
						$insert = $db->query("INSERT INTO last_fm_covers (name, artist, album) VALUES ('$image_db', '$artist_name', '$album')"); 
					}
					else {
						$getimage = $db->query("SELECT `album` FROM `last_fm_covers` WHERE name LIKE '$image_db'"); 
						$getimage_row = $getimage->fetch_assoc();
						if(!isset($getimage_row) or $getimage_row=="") {
							$update = $db->query("UPDATE last_fm_users SET album='$album' where name LIKE '$image_db'");  
						}
					}
					$image="covers/".$image_db; 
				}
			}
		}
		if(!isset($image) or $image=="") {
			$getimage = $db->query("SELECT `name` FROM `last_fm_covers` WHERE artist LIKE '$artist_name' and album = 'NULL'"); 
			if(isset($getimage->num_rows) and  $getimage->num_rows!= 0) {
				$getimages = $getimage->fetch_assoc()['name'];
				$image="covers/".$getimages; 
			}
		}
		if(!isset($image) or $image=="") {
			$image="pic/empty.png";		
		}
		return $image;
	}
	
#################################################################################################################################################################
	
	function head() {
		$content="";
		$content= '
			<div style="margin-left:40px;">
				<div class="modal fade" id="modaleins" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
 					<div class="modal-dialog" role="document">
    					<div class="modal-content">
      					<div class="modal-header" style="padding-top:5px; padding-bottom:20px; padding-right:10px;">
      						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
      							<span aria-hidden="true">&times;</span>
      						</button>
      					</div>
      					<div class="modal-body" id="modal_text">
      						<div style="heihgt:70px;">
      							<h4>Lyrics werden geladen</h4>
      					 		<figure style="">
  							     		<img src="pic/loading.gif" width="40px" height="40px">
  							  		</figure>
  							 	</div>
      					</div>
      				</div>
  					</div>
				</div>           
				<table id="t1">
				<tbody>
   			';
   	return $content;
   }
   
################################################################################################################################################################# 
   
   function lyric($artist_name, $track_name) {
   	$content="";
   	$lyric_band=str_replace(" ", "_", $artist_name);
   	$lyric_band = preg_replace ( '/[^a-z0-9A-Z]_/i', '',  $lyric_band); 
		$lyric_name =	explode("(", $track_name)[0];
		$lyric_name = strtolower(preg_replace ( '/[^a-z0-9]/i', '', $lyric_name)); 
		$url="include/prox.php?artist=".$lyric_band."&song=".$lyric_name;	
		$content='
			<td class="list_image">
					<label href="'.$url.'" style="padding:1px; margin:0;" data-toggle="modal" data-target="#modaleins">
						<div class="lyric" style="border-radius: 3px;"></div>
					</label>
 	  	 	</td>
 	  	 ';
 	  	 return $content;
   }
      
#################################################################################################################################################################  
	
	function lyrics_text($ly, $link) {   
  		$content='
  	 		<div class="modal-content">
  	 			<div class="modal-header" style="padding-top:5px; padding-bottom:20px; padding-right:10px;">
   				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      		</div>
      		<div class="modal-body">'.$ly.'<br/><br/>
      			Lyrics provided by '.$link.'
       			<button type="button" class="btn btn-default" data-dismiss="modal">Schlie&szlig;en</button>
      		</div>
      	</div>
     ';
		return $content;
  	}
  	
#################################################################################################################################################################  	
  	
  	function month_rename($date_eng) {
		$date_eng =  str_replace("Monday", "Montag",$date_eng);
		$date_eng =  str_replace("Tuesday", "Dienstag",$date_eng);
		$date_eng =  str_replace("Wednesday", "Mittwoch",$date_eng);
		$date_eng =  str_replace("Thursday", "Donnerstag",$date_eng);
		$date_eng =  str_replace("Friday", "Freitag",$date_eng);
		$date_eng =  str_replace("Saturday", "Samstag",$date_eng);
		$date_eng =  str_replace("Sunday", "Sonntag",$date_eng);
		$date_eng =  str_replace("January", "Januar",$date_eng);
		$date_eng =  str_replace("February", "Februar",$date_eng);
		$date_eng =  str_replace("March", "M&auml;rz",$date_eng);
		$date_eng =  str_replace("May", "Mai",$date_eng);
		$date_eng =  str_replace("June", "Juni",$date_eng);
		$date_eng =  str_replace("July", "Juli",$date_eng);
		$date_eng =  str_replace("October", "Oktober",$date_eng);
		$date_eng =  str_replace("December", "Dezember",$date_eng);  		
  		return $date_eng;
  	}
   
   
   
 ?>
