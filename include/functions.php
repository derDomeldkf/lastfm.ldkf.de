 <?php
	function love() {
		$methode="method=track.love&track=Prostitution&artist=Alligatoah&api_sig".$sig."&sk=".$sk;
		$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
  	} 
  	
  	function login($method) {
  		$user_in=$_GET['user'];
  		$getsession = mysql_fetch_row(mysql_query("SELECT session, sig FROM `last_fm_users` WHERE username LIKE '$user_in'")); 
		$getsession_user=$getsession[0];
		$getsig_user=$getsession[1];
		if(isset($getsession_user) and $getsession_user!="") {
			$_SESSION['user']=$user_in;
			$_SESSION['sig']=$getsig_user;
			$_SESSION['session']=$getsession_user;
			$update = mysql_query("UPDATE last_fm_users SET stat='1' where username = '$user_in'");  
		}
		else {
  			header('Location: http://www.last.fm/api/auth?api_key=830d6e2d4d737d56aa1f94f717a477df&cb=https://lastfm.ldkf.de/lastfm.php?method_came='.$method.'');
		}
  	} 
  	

 	function logout($user_in) {
 		$update = mysql_query("UPDATE last_fm_users SET stat='0' where username = '$user_in'"); 
 		$_SESSION['user']="";
		session_destroy();
  	} 
 
 
	function refresh($db_name, $command, $para, $para2) {
		$getusers = mysql_query("SELECT `username` FROM `ldkf_lastfm`"); 
		while($getuser = mysql_fetch_row($getusers)){
			$users[]=$getuser[0];
		}
		$delete =  "DELETE FROM ".$db_name;
		$kill_it_all = mysql_query($delete);  
		$d=0;
		foreach($users as $user_in){
			$methode="method=".$command."&user=".$user_in;
   		$out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
			if(isset($out)) {
				$user_info_array = get_object_vars(json_decode($out));
				if(isset($user_info_array['toptracks'])) {
					$user_info = get_object_vars($user_info_array['toptracks']);	
					foreach($user_info['toptracks'] as $top) {
						$info=get_object_vars($top);
						$name=str_replace("'", " ", $info["name"]);
						$playcount=$info["playcount"];
						$url=$info["url"];
						$image = get_object_vars($info["image"][0]);
						$image_path=$image['#text'];
						if(!isset($image_path) or $image_path=="") {
							$image_path="pic/empty.png";
						}
						$getcount = mysql_query("SELECT `playcount` FROM ".$db_name." WHERE artist LIKE '$name'"); 
						$count =	mysql_fetch_row($getcount);
						$counter=$count[0];
						if(isset($counter) and $counter!="") {
							$getuser_add = mysql_query("SELECT `user` FROM ".$db_name." WHERE artist LIKE '$name'"); 
							$users_gotten =	mysql_fetch_row($getuser_add);
							$user_db=$users_gotten[0];
							$counter_insert=$counter+$playcount;
							$user_insert=$user_db."&&".$user_in;
							$update = "UPDATE ".$db_name." SET user = '$user_insert', playcount ='$counter_insert'  where artist = '$name'";
							$updaten = mysql_query($update);  
						}
						else {
							$eintrag = "INSERT INTO ".$db_name." (playcount, artist, user) VALUES ('$playcount', '$name', '$user_in')"; 
    						$eintragen = mysql_query($eintrag);
						}
					}
				}
				unset($out);
			} 
			$d++;
			sleep(1);
		}  
	}


	function refresh2($db_name, $command) {
		$getusers = mysql_query("SELECT `username` FROM `ldkf_lastfm`"); 
		while($getuser = mysql_fetch_row($getusers)){
			$users[]=$getuser[0];
		}
		$delete =  "DELETE FROM ".$db_name;
		$kill_it_all = mysql_query($delete);  
		$d=0;
		foreach($users as $user_in){
			$methode="method=".$command."&user=".$user_in;
   		$out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
			if(isset($out)) {
				$user_info_array = get_object_vars(json_decode($out));
				if(isset($user_info_array['weeklytrackchart'])) {
					$i=0;
					$user_info = get_object_vars($user_info_array['weeklytrackchart']);	
					foreach($user_info['track'] as $top) {
						$info=get_object_vars($top);
						$name=$info["name"];
						$playcount=$info["playcount"];
						$art_array=$info["artist"];
						$art=get_object_vars($art_array);
						$artist_name=$art['#text'];
						$url=$info["url"];
			
						$image = get_object_vars($info["image"][0]);
						$image_path=$image['#text'];
						if(!isset($image_path) or $image_path=="") {
							$image_path="pic/empty.png";
						}
						$getcount = mysql_query("SELECT `playcount` FROM ".$db_name." WHERE titel LIKE '$name'"); 
						$count =	mysql_fetch_row($getcount);
						$counter=$count[0];
						if(isset($counter) and $counter!="") {
							$getuser_add = mysql_query("SELECT `user` FROM ".$db_name." WHERE titel LIKE '$name'"); 
							$users_gotten =	mysql_fetch_row($getuser_add);
							$user_db=$users_gotten[0];
							
							$counter_insert=$counter+$playcount;
							$user_insert=$user_db."&&".$user_in;
							$update = "UPDATE ".$db_name." SET user = '$user_insert', playcount ='$counter_insert'  where titel = '$name'";
							$updaten = mysql_query($update);  
						}
						else {
							$eintrag = "INSERT INTO ".$db_name." (playcount, artist, user, titel) VALUES ('$playcount', '$artist_name', '$user_in', '$name')"; 
    						$eintragen = mysql_query($eintrag);
						}
					}
				}
				unset($out);
			} 
			$d++;
			sleep(1);
		}  
	}
	function refresh3($db_name, $command) {
		$getusers = mysql_query("SELECT `username` FROM `ldkf_lastfm`"); 
		while($getuser = mysql_fetch_row($getusers)){
			$users[]=$getuser[0];
		}
		$delete =  "DELETE FROM ".$db_name;
		$kill_it_all = mysql_query($delete);  
		$d=0;
		foreach($users as $user_in){
			$methode="method=".$command."&user=".$user_in;
   		$out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
			if(isset($out)) {
				$user_info_array = get_object_vars(json_decode($out));
				if(isset($user_info_array['weeklytrackchart'])) {
					$user_info = get_object_vars($user_info_array['weeklytrackchart']);	
					foreach($user_info['artist'] as $top) {
						$info=get_object_vars($top);
						$name=str_replace("'", " ", $info["name"]);
						$name_art=$info['artist'];
						$playcount=$info["playcount"];
						$url=$info["url"];
						$image = get_object_vars($info["image"][0]);
						$image_path=$image['#text'];
						if(!isset($image_path) or $image_path=="") {
							$image_path="pic/empty.png";
						}
						$getcount = mysql_query("SELECT `playcount` FROM ".$db_name." WHERE titel LIKE '$name'"); 
						$count =	mysql_fetch_row($getcount);
						$counter=$count[0];
						if(isset($counter) and $counter!="") {
							$getuser_add = mysql_query("SELECT `user` FROM ".$db_name." WHERE titel LIKE '$name'"); 
							$users_gotten =	mysql_fetch_row($getuser_add);
							$user_db=$users_gotten[0];
							$counter_insert=$counter+$playcount;
							$user_insert=$user_db."&&".$user_in;
							$update = "UPDATE ".$db_name." SET user = '$user_insert', playcount ='$counter_insert'  where titel = '$name'";
							$updaten = mysql_query($update);  
						}
						else {
							$eintrag = "INSERT INTO ".$db_name." (playcount, artist, user, titel) VALUES ('$playcount', '$name_art', '$user_in', '$name')"; 
    						$eintragen = mysql_query($eintrag);
						}
					}
				}
				unset($out);
			} 
			$d++;
			sleep(1);
		}  
	}
	
 	function group($db_name, $period) {
 		$content="";
		$getplace = mysql_query("SELECT `artist` FROM ".$db_name." ORDER BY playcount DESC "); 
		$p=0;
		while($getplaces = mysql_fetch_row($getplace)){
			$places[$p]=$getplaces[0];
			$p++;
		}
		$getmembers = mysql_query("SELECT `username` FROM `ldkf_lastfm`"); 
		$l=0;
		while($members = mysql_fetch_row($getmembers)){
			$member[$l]=$members[0];
			$l++;
		}
	 	$content= '<div class="member">
	 	<p style="margin-bottom:7px;"><b>Mitglieder dieser Gruppe:</b></p><div style="padding-left:15px;">';
		foreach($member as $member_name){
			$content .= '<form class="form_member" method="post" action="lastfm.php">
			<input type="hidden" name="username" value="'.$member_name.'">
			<input type="hidden" name="method" value="2">
			<button type="submit" class="userButton">'.$member_name.'</button></form>';
		}
		$content .='</div>
		</div>
 		<table style="border-top:2px solid; border-left:2px solid;">
 		<tbody>
 			<tr>
				<td class="list table_head" style="padding-left:10px;">
					Platz
				</td>
				<td class="list table_head" style="padding-left:8px;">
					K&uuml;nstler
			</td>
			<td class="list table_head">
				Insgesamt geh&ouml;rt
			</td> 
			<td class="list table_head">
				'.$period.'				
			</td> 	
		</tr>';
		$i=0;	 
		$place=1;		
		foreach($places as $artist_name){
			$getartist = mysql_query("SELECT `playcount` FROM ".$db_name." WHERE artist LIKE '$artist_name'"); 
			$artist =	mysql_fetch_row($getartist);
			$count=$artist[0];
			if($place==1) {$count_max=$count;}
			$getuser = mysql_query("SELECT `user` FROM ".$db_name." WHERE artist LIKE '$artist_name'"); 
			$users= mysql_fetch_row($getuser);
			$users_names=$users[0];
			$user =  str_replace("&&", ", ",$users_names);
			if(substr_count($user, ', ')>2){
				$teile = explode(",", $user, 4);
				$teil =  str_replace(", ", '</li><li style="padding-left:15px;">',$teile[3]);
				$user='
					<ul class="nav navbar-nav">
        				<li class="dropdown">
          				<a href="#" class="dropdown-toggle" style="padding:0px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          					'.$teile[0].', '.$teile[1].', '.$teile[2].'...
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
			if($count>1) {
			$content .='
				<tr class="" style="';
				if($i==0) { 
					$content .='background-color: #F2F2F2;';
				}
				$content .='">
					<td class="list" style="padding-left:15px;">
  	 	        		<span class="">
  	    	   			<span class="chartlist-image">
  	       					'.$place.'
 	       				</span>
 		  	 			</span>         		
 	     	  		</td>
 	   			<td class="list" style="padding-right:5px; padding-left:8px; min-width:265px;">
 	  	        	<span class="chartlist-ellipsis-wrap">
 	     	   		<span class="chartlist-artists">
 	        				<a href="http://www.last.fm/de/music/'. urlencode($artist_name).'" title="'.$artist_name.'" target="_blank">'.$artist_name.'</a>
 	       				</span>
 		  	 			</span>	
  	    	   </td>
  	  		'; 
  	    	$m=0; 
			$st=40*$count/$count_max;    				
 	   	$content .='      	   
  	    		<td class="list" style="padding-right:8px; min-width:210px;"><div class="
  	    	';
  	    	if($st>strlen($count)*2){ $content .='textunter';} 
  	    	$content .= '">'; 
 	   		while($m<$st) {
 	   			$content .= '<img style="'; if($m==0) {$content .= 'border-top-left-radius:3px; border-bottom-left-radius:3px; ';} 
 	   			if($m+1>=$st) {$content .= 'border-top-right-radius:3px; border-bottom-right-radius:3px';} 
  	  				$content .='" src="pic/count.png" height:15px;>'; 
					$m++; 					
 	   		}
  	  			$content .= '<span';
  	  			if($st>strlen($count)*2){}
  	  			else { 
  	  				$content .=' style="padding-left:5px;"';
  	  			}
  	  			$content .= '>'.$count.'</span></div>
 	         </td>
 	        	<td class="list" style="padding-right:3px; min-width:370px;">
 	   				<span>'.$user.'</span>
 	           </td>
				</tr>';
			if($i==0){$i++;}
 	     else {$i--;}
 	  	}	
 	     $place++;
		}
	 	$content .= '
 			</tbody>
		</table>
		';
		return $content;
	}
	
	
	function group2($db_name, $period) {
 		$content="";
		$getplace = mysql_query("SELECT `titel` FROM ".$db_name." ORDER BY playcount DESC "); 
		$p=0;
		while($getplaces = mysql_fetch_row($getplace)){
			$places[]=$getplaces[0];
		}
		$getmembers = mysql_query("SELECT `username` FROM `ldkf_lastfm`"); 
		$l=0;
		while($members = mysql_fetch_row($getmembers)){
			$member[$l]=$members[0];
			$l++;
		}
	 	$content= '<div class="member">
	 	<p style="margin-bottom:7px;"><b>Mitglieder dieser Gruppe:</b></p><div style="padding-left:15px;">';
		foreach($member as $member_name){
			$content .= '<form class="form_member" method="post" action="lastfm.php">
			<input type="hidden" name="username" value="'.$member_name.'">
			<input type="hidden" name="method" value="2">
			<button type="submit" class="userButton">'.$member_name.'</button></form>';
		}
		$content .='</div>
		</div>
 		<table style="border-top:2px solid; border-left:2px solid;">
 		<tbody>
 			<tr>
				<td class="list table_head" style="padding-left:10px;">
					Platz
				</td>
				<td class="list table_head">
					
				</td> 
				<td class="list table_head" style="padding-left:8px;">
					K&uuml;nstler — Titel
				</td>
				<td class="list table_head">
					'.$period.'				
				</td> 	
			</tr>';
		$i=0;	 
		$place=1;		
		foreach($places as $artist_name){
			$getartist = mysql_query("SELECT `playcount` FROM ".$db_name." WHERE titel LIKE '$artist_name'"); 
			$artist =	mysql_fetch_row($getartist);
			$count=$artist[0];
			if($place==1) {$count_max=$count;}
			$getuser = mysql_query("SELECT `user` FROM ".$db_name." WHERE titel LIKE '$artist_name'"); 
			$users= mysql_fetch_row($getuser);
			$users_names=$users[0];
			$getart= mysql_query("SELECT `artist` FROM ".$db_name." WHERE titel LIKE '$artist_name'"); 
			$art= mysql_fetch_row($getart);
			$art_name=$art[0];
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
			if($count>1) {
			$content .='
				<tr class="" style="';
				if($i==0) { 
					$content .='background-color: #F2F2F2;';
				}
				$content .='">
					<td class="list" style="padding-left:15px;">
  	 	        		<span class="">
  	    	   			<span class="chartlist-image">
  	       					'.$place.'
 	       				</span>
 		  	 			</span>         		
 	     	  		</td>
 	     	  		<td class="list" style="padding-left:8px; ">
  	  					<span>('.$count.')</span>
 	         	</td>     
 	   			<td class="chartlist-ellipsis-wrap list" style="padding-left:10px; padding-right:4px; min-width:460px;">
   	   			<span class="chartlist-ellipsis-wrap">
      	   			<span class="chartlist-artists">
      	   				<a href="http://www.last.fm/music/'.$art_name.'" target="_blank">'.$art_name.'</a>
         				</span>
							<span class="artist-name-spacer"> — </span>
    							<a href="http://www.last.fm/music/'.$art_name.'/_/'.$artist_name.'" target="_blank">'.$artist_name.'</a>
 	  	 				</span>
					</td>
  	  				<td class="list" style="padding-right:3px; min-width:360px;">
 	   				<span>'.$user.'</span>
 	           	</td>
				</tr>';
			if($i==0){$i++;}
 	     else {$i--;}
 	  	}	
 	     $place++;
		}
	 	$content .= '
 			</tbody>
		</table>
		';
		return $content;
	}
	
	
	
			
	function footer($method_in, $page, $totalPages, $user_in, $limit_in, $perPage) {	
	 	$content="";
		$page_n=$page+1;
		$page_l=$page-1;
		if($method_in==2 or $method_in==5 or $method_in==6 or $method_in==7) {
			$content= '
			<div class="nav footer">
				<table>
					<tr>
						<td class="navfooter" style="color:white;">
            			Seite '.$page.' von '.$totalPages.'
            		</td>
						<td class="navfooter">
					';
					if($page>2) {
						$content .= '
							<form action="?" style="margin:0; padding:0;" method="POST">
            				<input type="hidden" name="username" value='.$user_in.'>
   							<input type="hidden" name="method" value="'.$method_in.'">
   							<input type="hidden" name="limitin" value="'.$limit_in.'">
   				   		<input type="hidden" name="pagein" value="1">
								<button type="submit" class="btn btn-primary">
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
						$content .= '
							<form action="?" style="margin:0; padding:0;" method="POST">
            				<input type="hidden" name="username" value='.$user_in.'>
   						 	<input type="hidden" name="method" value="'.$method_in.'">
   							<input type="hidden" name="limitin" value="'.$limit_in.'">
   				   		<input type="hidden" name="pagein" value="'. $page_l .'">
								<button type="submit" class="btn btn-primary">
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
						$content .='
            			<form action="?" style="margin:0; padding:0;" method="POST">
            				<input type="hidden" name="username" value='.$user_in.'>
   								<input type="hidden" name="method" value="'.$method_in.'">
   								<input type="hidden" name="limitin" value="'.$limit_in.'">
   					   		<input type="hidden" name="pagein" value="'. $page_n .'">
									<button type="submit" class="btn btn-primary">
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
							$content .='
      	      			<form action="?" style="margin:0; padding:0;" method="POST">
     	       				<input type="hidden" name="username" value='.$user_in.'>
   								<input type="hidden" name="method" value="'.$method_in.'">
   								<input type="hidden" name="limitin" value="'.$limit_in.'">
   					   		<input type="hidden" name="pagein" value="'. $totalPages .'">
									<button type="submit" class="btn btn-primary">
										>>|
									</button>
								</form>
							';
							}
							$content .='
							</td>             			
   	         		<td class="navfooter">
   	         			<form action="?" style="margin:0; padding:0;" method="POST">
   								<select class="" name="limitin" id="myselect" onchange="this.form.submit()" style="color:black">';
   								if($method_in==6 or $method_in==7) {
  	 							$content .='
  	      							<option class="option"'; if($perPage==20) {$content .= " selected";} $content .= ' value="20">20 Eintr&auml;ge Pro Seite</option>
   	     							<option class="option"'; if($perPage==40) {$content .= " selected";} $content .= ' value="40">40 Eintr&auml;ge Pro Seite</option>
   	     							<option class="option"'; if($perPage==60) {$content .= " selected";} $content .= ' value="60">60 Eintr&auml;ge Pro Seite</option>';
   	     						}
   	     						else {
  	      						$content .='
  	      							<option class="option"'; if($perPage==15) {$content .=" selected";} $content .= ' value="15">15 Eintr&auml;ge Pro Seite</option>
 	       							<option class="option"'; if($perPage==25) {$content .=" selected";} $content .= ' value="25">25 Eintr&auml;ge Pro Seite</option>
  	      							<option class="option"'; if($perPage==35) {$content .=" selected";} $content .= ' value="35">35 Eintr&auml;ge Pro Seite</option>';
 	       						
  	      						}
 	  							$content .='</select>
  	 							<input type="hidden" name="username" value='.$user_in.'>
 	  							<input type="hidden" name="method" value="'.$method_in.'">
 	  							<input type="hidden" name="pagein" value="'. $page .'">
							</form>
						</td>
					</tr>
				</table>
			</div>
			';
		}
  		return $content;
	}
	
	
	function nav($method_in, $user_in, $image, $totalTracks, $starttime, $totaltracks) {
		$content="";
		if($method_in==2 or $method_in==5 or $method_in==6 or $method_in==7) {
   		$getname = mysql_query("SELECT `id` FROM `ldkf_lastfm` WHERE `username` LIKE '$user_in'"); 
			$namecheck = mysql_fetch_row($getname);
			$user = $namecheck[0];
			if(isset($user) and $user!="") {
				$content='<li><a href="./lastfm.php?method=4">Gruppe</a></li>
					<li><a href="http://explr.fm/?username='.$user_in.'" target="_blank">Explr.fm</a></li>
				';
				if(isset($_SESSION['user']) and $_SESSION['user']==$user_in) {				
					$content .='<li><a href="./lastfm.php?logout=1&user='.$user_in.'&methodlogout='.$method_in.'" >Logout</a></li>';
				}
				else {
					$content .='<li><a href="./lastfm.php?login=1&user='.$user_in.'&methodlogin='.$method_in.'" >Login</a></li>';
				}
        	}
        	$content .= '</ul>
   				<ul class="nav navbar-nav navbar-right">
   					<li class="dropdown" style="width:200px;">
      					<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
      						<img style="border-radius: 18px;" width="36px" src="'.$image.'"> '.$user_in.'<span class="caret"></span>
							</a>
         				<ul class="navbar-inverse dropdown-menu" style="border-radius: 6px; width:100%; margin-top:10px; padding-bottom:8px; color:white;">
           					<li style="padding-left:15px;">';
			if($method_in!=2) { 
				$content .='
           		<form class="form_member" method="post" action="lastfm.php">
						<input type="hidden" name="username" value="'.$user_in.'">
							<input type="hidden" name="method" value="2">
							<button type="submit" class="userButton">';
			}
			$content .= 'Scrobbles: '.$totalTracks; 
			if($method_in!=2) {
				$content .= '</button>
				</form>';
			}
			$content .= '
	      	</li>
   	   	<li style="padding-left:15px;">';
      	if($method_in!=5) { 
      		$content .='
      			<form class="form_member" method="post" action="lastfm.php">
						<input type="hidden" name="username" value="'.$user_in.'">
						<input type="hidden" name="method" value="5">
						<button type="submit" class="userButton">
				';
			}
			$content .= 'Lieblingslieder'; 
			if($method_in==5) { 
				$content .= ': '.$totaltracks;
			}
			$content .='</button>
				</form>
				</li>
				<li style="padding-left:15px;">';
	      if($method_in!=6) { 
	      	$content .='
   	      	<form class="form_member" method="post" action="lastfm.php">
						<input type="hidden" name="username" value="'.$user_in.'">
						<input type="hidden" name="method" value="6">
						<button type="submit" class="userButton">
				';
			}
			$content .= 'Top K&uuml;nstler'; 
			if($method_in==6) { 
				$content .= ': '.$totaltracks;
			}
			$content .='</button>
				</form>
				</li>
				<li style="padding-left:15px;">
			';
			if($method_in!=7) { 
				$content .='
  					<form class="form_member" method="post" action="lastfm.php">
						<input type="hidden" name="username" value="'.$user_in.'">
						<input type="hidden" name="method" value="7">
						<button type="submit" class="userButton">
					';
			}
			$content .= 'Top Titel'; 
			if($method_in==7) { 
				$content .= ': '.$totaltracks;
			}
			$content .='</button>
				</form>
				</li>
				<li style="padding-left:15px;">Scrobbelt seit: '. gmdate("d.m.Y", $starttime).'</li>
				</ul>
				</li> 
				</ul>
			'; 									
		}
		if($method_in==4) {
			$content='
				</ul>
				<ul class="nav navbar-nav navbar-right">
      			<li class="dropdown" style="width:200px;">
      				<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          				<img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
						</a>
         			<ul class="navbar-inverse dropdown-menu" style="border-radius: 6px; width:100%; margin-top:10px; padding-bottom:8px; color:white;">
        				   <li style="padding-left:15px;"><b>Top K&uuml;nstler (Woche)</b></li>
       					 <li style="padding-left:15px;">
        						<div>
									<a class="userButton" href="lastfm.php?method=8">Top Künstler (Gesamt)</a>
								</div>
							</li>
         	  			<li style="padding-left:15px;">
      	     				<div>
									<a class="userButton" href="lastfm.php?method=10">Top Titel (Woche)</a>
								</div>
           				</li>
           				<li style="padding-left:15px;">
      	     				<div>
									<a class="userButton" href="lastfm.php?method=9">Top Titel (Gesamt)</a>
								</div>
           				</li>
        				</ul>
       			</li> 
       		</ul>
       	';							
		}
		if($method_in==8) {
			$content ='
				</ul>
				<ul class="nav navbar-nav navbar-right">
        			<li class="dropdown" style="width:200px;">
          			<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          				<img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
						</a>
         			<ul class="navbar-inverse dropdown-menu" style="border-radius: 6px; width:100%; margin-top:10px; padding-bottom:8px; color:white;">
           				<li style="padding-left:15px;">
           					<div>
									<a class="userButton" href="lastfm.php?method=4">Top K&uuml;nstler (Woche)</a>
								</div>
           				</li>
 	          			<li style="padding-left:15px;"><b>Top Künstler (Gesamt)</b></li>
   	        			<li style="padding-left:15px;">
      	     				<div>
									<a class="userButton" href="lastfm.php?method=10">Top Titel (Woche)</a>
								</div>
           				</li>
           				<li style="padding-left:15px;">
      	     				<div>
									<a class="userButton" href="lastfm.php?method=9">Top Titel (Gesamt)</a>
								</div>
           				</li>
        				</ul>
       			</li> 
       		</ul>
       	';							
		}	
		if($method_in==9) {
			$content ='
				</ul>
				<ul class="nav navbar-nav navbar-right">
        			<li class="dropdown" style="width:200px;">
          			<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          				<img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
						</a>
         			<ul class="navbar-inverse dropdown-menu" style="border-radius: 6px; width:100%; margin-top:10px; padding-bottom:8px; color:white;">
           				<li style="padding-left:15px;">
           					<div>
									<a class="userButton" href="lastfm.php?method=4">Top K&uuml;nstler (Woche)</a>
								</div>
           				</li>
           				<li style="padding-left:15px;">
      	     				<div>
									<a class="userButton" href="lastfm.php?method=8">Top Künstler (Gesamt)</a>
								</div>
           				</li>
   	        			<li style="padding-left:15px;">
      	     				<div>
									<a class="userButton" href="lastfm.php?method=10">Top Titel (Woche)</a>
								</div>
           				</li>
           				<li style="padding-left:15px;"><b>Top Titel (Gesamt)</b></li>
        				</ul>
       			</li> 
       		</ul>
       	';							
		}	
	if($method_in==10) {
			$content ='
				</ul>
				<ul class="nav navbar-nav navbar-right">
        			<li class="dropdown" style="width:200px;">
          			<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          				<img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
						</a>
         			<ul class="navbar-inverse dropdown-menu" style="border-radius: 6px; width:100%; margin-top:10px; padding-bottom:8px; color:white;">
           				<li style="padding-left:15px;">
           					<div>
									<a class="userButton" href="lastfm.php?method=4">Top K&uuml;nstler (Woche)</a>
								</div>
           				</li>
           				<li style="padding-left:15px;">
      	     				<div>
									<a class="userButton" href="lastfm.php?method=8">Top Künstler (Gesamt)</a>
								</div>
           				</li>
           				<li style="padding-left:15px;"><b>Top Titel (Woche)</b></li>
   	        			<li style="padding-left:15px;">
      	     				<div>
									<a class="userButton" href="lastfm.php?method=9">Top Titel (Gesamt)</a>
								</div>
           				</li>
        				</ul>
       			</li> 
       		</ul>
       	';							
		}			
		
		
		
		return $content;
	}
	
	
	function image($images, $artist_name) {
		$content="";
		if(!isset($images) or $images=="") {
			$getimage = mysql_query("SELECT `name` FROM `last_fm_covers` WHERE artist LIKE '$artist_name'"); 
			$getimages = mysql_fetch_row($getimage);
			if(isset($getimages[0]) and $getimages[0]!="") {							
				$image="covers/".$getimages[0].".png"; 
			}
			else {
				$image="pic/empty.png";
			}
		}
		else {
			$image_db =  str_replace(".png", "",$images);
			$image_db =  str_replace("http://img2-ak.lst.fm/i/u/34s/", "",$image_db);
			$getimage = mysql_query("SELECT `id` FROM `last_fm_covers` WHERE name LIKE '$image_db'"); 
			$getimages = mysql_fetch_row($getimage);
			$getimage_row=$getimages[0];
			if(!isset($getimage_row) or $getimage_row=="") {
				$pfad="covers/".$image_db.".png";
				copy($images, $pfad);
				$eintrag = "INSERT INTO last_fm_covers (name) VALUES ('$image_db')"; 
    			$eintragen = mysql_query($eintrag);
			}
			$image="covers/".$image_db.".png"; 
		}	
		return $image;
	}
	
	
	function head() {
		$content="";
		$content= '
			<div style="margin-left:30px;">
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
				<table>
   				<tbody>';
   	return $content;
   }
   
   
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
