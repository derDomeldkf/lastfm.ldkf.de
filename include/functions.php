 <?php
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
  	
############################################################################################################################################ 	
  	function login($method, $db, $page, $limit) {
  		/*$user_in=$_GET['user'];
  		$getsession = mysql_fetch_row(mysql_query("SELECT session, sig FROM `last_fm_users` WHERE username LIKE '$user_in'")); 
		$getsession_user=$getsession[0];
		$getsig_user=$getsession[1];
		if(isset($getsession_user) and $getsession_user!="") {
			$_SESSION['user']=$user_in;
			$_SESSION['sig']=$getsig_user;
			$_SESSION['session']=$getsession_user;
			$update = mysql_query("UPDATE last_fm_users SET stat='1' where username = '$user_in'");  
		}
		else {*/
  			header('Location: http://www.last.fm/api/auth?api_key=830d6e2d4d737d56aa1f94f717a477df&cb=https://lastfm.ldkf.de/lastfm.php?mpl='.$method.'_'.$page.'_'.$limit.'');
		//}
  	} 
  	
##############################################################################################################################################
 	function logout($user_in, $db) {
 		$update = $db->query("UPDATE last_fm_users SET stat='0' where username = '$user_in'"); 
 		setcookie("user","",time() - 3600); 
		session_destroy();
  	} 

###############################################################################################################################################
 	function refresh($db_name, $command, $db) {
		$getusers = $db->query("SELECT `username` FROM `ldkf_lastfm`"); 
 		while ($getuser = $getusers->fetch_assoc()) {
 			$users[]=$getuser['username'];
 		}		
 		if($db_name=="last_fm_charts_all") {
 			$delete = $db->query("DELETE FROM ".$db_name);	
 			$s=0;
 		}
 		else {
 			$sql = "CREATE TABLE `".$db_name."` (
				playcount INT(3) NOT NULL,
				artist VARCHAR(40) NOT NULL COLLATE utf8_general_mysql500_ci,
				user TEXT NOT NULL COLLATE latin1_swedish_ci
			)";
			if ($db->query($sql) === TRUE) {
				$s=0;
			}
			else {
   		 	echo "Error creating table: " . $db->error;
   		 	$s=1;
			}
		}
		$d=0;
		if($s==0) {
			foreach($users as $user_in){
				$methode="method=".$command."&user=".$user_in;
   			$out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
				if(isset($out)) {
					$user_info_array = get_object_vars(json_decode($out));
					if(isset($user_info_array['topartists'])) {
						$user_info = get_object_vars($user_info_array['topartists']);	
						foreach($user_info['artist'] as $top) {
							$info=get_object_vars($top);
							$name=str_replace(" ", " ", $info["name"]);
							$playcount=$info["playcount"];
							$url=$info["url"];
							$image = get_object_vars($info["image"][0]);
							$image_path=$image['#text'];
							if(!isset($image_path) or $image_path=="") {
								$image_path="pic/empty.png";
							}
							$getcounter = $db->query("SELECT `playcount` FROM `".$db_name."` WHERE artist LIKE '$name'"); 
							if(isset($getcounter->num_rows) and  $getcounter->num_rows!= 0) {
								$counter = $getcounter->fetch_assoc()['playcount'];
							}
							if(isset($counter) and $counter!="") {
								$getuser_add = $db->query("SELECT `user` FROM `".$db_name."` WHERE artist LIKE '$name' "); 
								$user_db = $getuser_add->fetch_assoc()['user'];
								$counter_insert=$counter+$playcount;
								$user_insert=$user_db."&&".$user_in;
								$update = $db->query("UPDATE `".$db_name."` SET user = '$user_insert', playcount ='$counter_insert'  where artist = '$name'");  
							}
							else {
								$insert = $db->query("INSERT INTO `".$db_name."` (playcount, artist, user) VALUES ('$playcount', '$name', '$user_in')"); 
							}
							$counter="";
						}
						
					}
					unset($out);
				} 
				$d++;
			//	sleep(1);
			}  
		}
	}

#############################################################################################################################
	function refresh2($db_name, $command, $db) {
		$getusers = $db->query("SELECT `username` FROM `ldkf_lastfm`"); 
 		while ($getuser = $getusers->fetch_assoc()) {
 			$users[]=$getuser['username'];
 		}		
 		$sql = "CREATE TABLE `".$db_name."` (
			playcount INT(3) NOT NULL,
			artist VARCHAR(40) NOT NULL COLLATE utf8_general_mysql500_ci,
			user TEXT NOT NULL COLLATE latin1_swedish_ci,
			titel VARCHAR(40) NOT NULL COLLATE utf8_general_mysql500_ci
		)";
		if ($db->query($sql) === TRUE) {
			$s=0;
		}
		else {
   	 	echo "Error creating table: " . $db->error;
   	 	$s=1;
		}	
		$d=0;
		foreach($users as $user_in){
			$methode="method=".$command."&user=".$user_in;
   		$out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
			if(isset($out)) {
				$user_info_array = get_object_vars(json_decode($out));
				if(isset($user_info_array['weeklytrackchart'])) {
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
						$getcount = $db->query("SELECT `playcount` FROM `".$db_name."` WHERE titel LIKE '$name' "); 
						if(isset($getcount->num_rows) and  $getcount->num_rows!= 0) {
							$counter = $getcount->fetch_assoc()['playcount'];
						}
						if(isset($counter) and $counter!="") {
							$getuser_add = $db->query("SELECT `user` FROM `".$db_name."` WHERE titel LIKE '$name' "); 
							$user_db = $getuser_add->fetch_assoc()['user'];
							$counter_insert=$counter+$playcount;
							$user_insert=$user_db."&&".$user_in;
							$update = $db->query("UPDATE `".$db_name."` SET user = '$user_insert', playcount ='$counter_insert'  where titel = '$name'");
						}
						else {
							$insert = $db->query("INSERT INTO `".$db_name."` (playcount, artist, user, titel) VALUES ('$playcount', '$artist_name', '$user_in', '$name')"); 
						}
						$counter="";
					}
				}
				unset($out);
			}
			$d++;
			//sleep(1);	 
		}  
	}
	
######################################################################################################################################################
	function refresh3($db_name, $command, $db) {
		$getusers = $db->query("SELECT `username` FROM `ldkf_lastfm`"); 
 		while ($getuser = $getusers->fetch_assoc()) {
 			$users[]=$getuser['username'];
 		}	
		$d=0;
		$delete = $db->query("DELETE FROM ".$db_name);
		foreach($users as $user_in){
			$methode="method=".$command."&user=".$user_in;
   		$out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
			if(isset($out)) {
				$user_info_array = get_object_vars(json_decode($out));
				if(isset($user_info_array['weeklytrackchart'])) {
					$user_info = get_object_vars($user_info_array['weeklytrackchart']);	
					foreach($user_info['artist'] as $top) {
						$info=get_object_vars($top);
						$name=str_replace(" ", " ", $info["name"]);
						$name_art=$info['artist'];
						$playcount=$info["playcount"];
						$url=$info["url"];
						$image = get_object_vars($info["image"][0]);
						$image_path=$image['#text'];
						if(!isset($image_path) or $image_path=="") {
							$image_path="pic/empty.png";
						}
						$getcount = $db->query("SELECT `playcount` FROM ".$db_name." WHERE titel LIKE '$name'"); 
						$counter =	$getcount->fetch_assoc()['playcount'];
						if(isset($counter) and $counter!="") {
							$getuser_add = $db->query("SELECT `user` FROM ".$db_name." WHERE titel LIKE '$name' "); 
							$user_db = $getuser_add->fetch_assoc()['user'];
							$counter_insert=$counter+$playcount;
							$user_insert=$user_db."&&".$user_in;
							$update = $db->query("UPDATE ".$db_name." SET user = '$user_insert', playcount ='$counter_insert'  where titel = '$name'");
						}
						else {
							$insert = $db->query("INSERT INTO ".$db_name." (playcount, artist, user, titel) VALUES ('$playcount', '$name_art', '$user_in', '$name')"); 
						}
						$counter="";
					}
				}
				unset($out);
			} 
			$d++;
			//sleep(1);
		}  
	}

###################################################################################################################################
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
		$content .= '</select>	';
	
		return $content;
	}	
	
#################################################################################################################################
	function group($db_name, $period, $db, $post, $date) {
 		$content="";
 		if(!isset($_POST['userselc'][0])) {
			$getplace = $db->query("SELECT `artist` FROM `".$db_name."` ORDER BY playcount DESC"); 
			$user_input[]="0";
		}
		else {
			$i=0;
			$where="";
			foreach($_POST['userselc'] as $user_sel) {
				if($i==0) {
					$where .="WHERE user LIKE '%".$user_sel."%'";
					$user_input[]=$user_sel;
				
				}
				else {
					$where .="and user LIKE '%".$user_sel."%'";
					$user_input[]=$user_sel;
				}
			$i++;
			}
			$getplace = $db->query("SELECT `artist` FROM `".$db_name."` ".$where." ORDER BY playcount DESC"); 
		}		
		
		while($getplaces = $getplace->fetch_assoc()){
			$places[]=$getplaces['artist'];
		}
		if(!isset($places) and isset($ua)) {
			$content .='
				<div class="row" style="margin:0">
					<div class="col-md-9" style="padding-left:40px;">
						<h3>Diese Benutzer habe keine gemeinsam gehörten Künstler.</h3>
			';		
		}
		else {
		$content .='		
		<div class="row" style="margin:0">
		<div class="col-md-9" style="padding-left:40px;">
 		<table style="">
 		<tbody>
 			<tr>
 				<td class="list table_head">
 				</td>
				<td class="list table_head" style="padding-left:10px;">
					Platz
				</td>
				<td class="list table_head">
					
				</td> 
				<td class="list table_head" style="padding-left:8px;">
					K&uuml;nstler
				</td>
				<td class="list table_head">
					'.$period.'				
				</td> 	
			</tr>';
		$i=0;	 
		$place=1;	
		if(isset($places )) {
		foreach($places as $artist_name){
			$getartist = $db->query("SELECT `playcount` FROM `".$db_name."` WHERE artist LIKE '$artist_name'"); 
			$counter = $getartist->fetch_assoc();
			$count=$counter['playcount'];
			if($place==1) {$count_max=$count;}
			$getuser = $db->query("SELECT `user` FROM `".$db_name."` WHERE artist LIKE '$artist_name' "); 
			$users_names= $getuser->fetch_assoc()['user'];
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
				$content .='">';
				$content .= image_artist($artist_name, $db); 				
				$content .='
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
 	   			<td class="chartlist-ellipsis-wrap list" style="padding-left:10px; padding-right:4px; min-width:260px;">
   	   			<span class="chartlist-ellipsis-wrap">
  							<a href="http://www.last.fm/music/'.$artist_name.'" target="_blank">'.$artist_name.'</a>
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
		}
		
	 	$content .= '
 			</tbody>
		</table>';
		}
		$content .= '
		</div>
		<div class="col-md-3" style="padding-left:30px; padding-top:20px;">
		';
		if($db_name=="last_fm_charts_all") {
			$getmembers = $db->query("SELECT `username` FROM `ldkf_lastfm` order by `username` ASC"); 
				while($members = $getmembers->fetch_assoc()){
					$member[]=$members['username'];
				}
			$content .= '
			<h4>Gemeinsame Künstler von:</h4>
				<div style="max-width:220px; margin-top:30px;">
					<form class="form-signin" method="post" action="lastfm.php?">
   					<input type="hidden" name="method" value="8">
   					<select name="userselc[]" class="form-control" style="margin-bottom:5px;" required>';
   					$content .= select($member, $user_input, "0");
   					$content .= '
   					<select name="userselc[]" class="form-control" style="margin-bottom:5px;" required>';
   					$content .= select($member, $user_input, "1");
   					$content .= '
   					<select name="userselc[]" class="form-control" style="margin-bottom:5px;">';
   					$content .= select($member, $user_input, "2");
   					$content .= '
						<select name="userselc[]" class="form-control" style="margin-bottom:5px;">';
   					$content .= select($member, $user_input, "3");
 						$content .= '
   					<br>
						<button type="submit" class="btn btn-primary">
							Suchen
						</button>
   				</form>';
   				if(isset($_POST['userselc'][0]) and $_POST['userselc'][0] != "") {
   					$content .= '
   				<form class="form-signin" method="post" action="lastfm.php?">
   					<input type="hidden" name="method" value="8">
						<button type="submit" class="btn btn-primary">
							Reset
						</button>
   				</form>';
   				}
   			$content .= '
   			</div>	
			';
		}
		else {
			$getid = $db->query("SELECT id FROM `tables` ORDER BY id DESC"); 
			while($getplaces = $getid->fetch_assoc()){
				$ids[]=$getplaces['id']; 
			}
			$content .= '<h4>'.$date.'</h4>
				<div style="max-width:220px; margin-top:30px;">
			';
			$i=1;
			foreach($ids as $id){
				$content .= '
					<form class="form-signin" method="post" action="lastfm.php?">
				  		<input type="hidden" name="tableselect" value="'.$id.'">
				  		<input type="hidden" name="method" value="4">
   					<button type="submit" class="btn btn-primary"'; if((isset($_POST['tableselect']) and $id==$_POST['tableselect']) or ($post==$id)) {  $content .= " disabled";} $content .= '>
							Seite '.$i.'
						</button>
   				</form>
   			';
   			$i++;
			}
			$content .= '
   			</div>	
			';
		}	
				
		$content .= '
		</div>
		</div>
		';
		return $content;
	}

##################################################################################################################################
	function group2($db_name, $period, $db) {
		
 		$content=head();
		$getplace = $db->query("SELECT `titel` FROM ".$db_name." ORDER BY playcount DESC "); 
		while($getplaces = $getplace->fetch_assoc()){
			$places[]=$getplaces['titel'];
		}
		$content .='
 			<tr>
 				<td class="list table_head">
 				</td>
				<td class="list table_head" style="padding-left:10px;">
					Platz
				</td>
				<td class="list table_head">
					
				</td> 
				<td class="list table_head" style="padding-left:8px;">
					K&uuml;nstler — Titel
				</td>
				<td class="list table_head">
				</td>
				<td class="list table_head">
					'.$period.'				
				</td> 	
			</tr>';
		$i=0;	 
		$place=1;		
		foreach($places as $track_name){
			$getartist = $db->query("SELECT `playcount` FROM ".$db_name." WHERE titel LIKE '$track_name'"); 
			$count = $getartist->fetch_assoc()['playcount'];
			if($place==1) {
				$count_max=$count;
			}
			$getuser = $db->query("SELECT `user` FROM ".$db_name." WHERE titel LIKE '$track_name' "); 
			$users_names = $getuser->fetch_assoc()['user'];
			$getart= $db->query("SELECT `artist` FROM ".$db_name." WHERE titel LIKE '$track_name' "); 
			$artist_name= $getart->fetch_assoc()['artist'];
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
				$content .='">';
				$content .= image_artist($artist_name, $db); 				
				$content .='
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
      	   				<a href="http://www.last.fm/music/'.$artist_name.'" target="_blank">'.$artist_name.'</a>
         				</span>
							<span class="artist-name-spacer"> — </span>
    							<a href="http://www.last.fm/music/'.$artist_name.'/_/'.$track_name.'" target="_blank">'.$track_name.'</a>
 	  	 				</span>
					</td>';
					$content .= lyric($artist_name, $track_name);	
						
					$content .= '  	  				
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
		</div>
		';
		return $content;
	}

###########################################################################################################################################	
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
						$content .= '
							<form action="?" style="margin:0; padding:0;" method="POST">
            				<input type="hidden" name="username" value='.$user_in.'>
   							<input type="hidden" name="method" value="'.$method_in.'">
   							<input type="hidden" name="limitin" value="'.$limit_in.'">
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
						$content .= '
							<form action="?" style="margin:0; padding:0;" method="POST">
            				<input type="hidden" name="username" value='.$user_in.'>
   						 	<input type="hidden" name="method" value="'.$method_in.'">
   							<input type="hidden" name="limitin" value="'.$limit_in.'">
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
						$content .='
            			<form action="?" style="margin:0; padding:0;" method="POST">
            				<input type="hidden" name="username" value='.$user_in.'>
   							<input type="hidden" name="method" value="'.$method_in.'">
   							<input type="hidden" name="limitin" value="'.$limit_in.'">
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
							$content .='
      	      			<form action="?" style="margin:0; padding:0;" method="POST">
     	       				<input type="hidden" name="username" value='.$user_in.'>
   								<input type="hidden" name="method" value="'.$method_in.'">
   								<input type="hidden" name="limitin" value="'.$limit_in.'">
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
	
########################################################################################################################################
	function nav($method_in, $user_in, $image, $totalTracks, $starttime, $totaltracks, $db, $page_in, $limit) {

		$content="";
		if(isset($_SESSION['user']) and !isset($_GET['methodlogout'])) {
				$content .='
					<li><a href="./lastfm.php?method_get=2">Home</a></li>
				';
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
					$sig=$_SESSION['sig'];
					$methode="method=track.scrobble&track=". urlencode($track) ."&timestamp=". time() ."&artist=". urlencode($artist) ."&api_sig=".$sig."&sk=".$sk;
					if(isset($album) and $album!="") {
						$methode .="&album=". urlencode($album);
					}
					$out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
					var_dump($out_user);
					$content .='<li style="padding-left:10px; padding-top:11px;"><audio src="'. $path .'" controls onloadstart="this.volume=0.05" autoplay></audio></li>';
				}		
        	}
       	$content .= '</ul>
   				<ul class="nav navbar-nav navbar-right" style="margin-right:20px;">
   					<li class="dropdown" style="width:200px;">
      					<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
      						<img style="border-radius: 18px;" width="36px" src="'.$image.'"> '.$user_in.'<span class="caret"></span>
							</a>
         				<ul class="navbar-inverse dropdown-menu" style="color:white; font-size:11pt; padding: 15px 10px 15px 30px; border-radius:0; width:240px;">
           					<li>';
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
	      if($method_in!=6) { 
	      	$content .='
   	      	<form class="form_member" method="post" action="lastfm.php">
						<input type="hidden" name="username" value="'.$user_in.'">
						<input type="hidden" name="method" value="6">
						<button type="submit" class="userButton">
				';
			}
			$content .= ''; 
			if($method_in==6) { 
				$content .= '<b>Top K&uuml;nstler: '.$totaltracks.'</b>';
			}
			else {
				$content .= 'Top K&uuml;nstler';			
			}
			$content .='</button>
				</form>
				</li>
				<li>
			';
			if($method_in!=7) { 
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
        			
		}		
		if($method_in==4) {
			$content .='
				</ul>
				<ul class="nav navbar-nav navbar-right" style="margin-right:20px;">
      			<li class="dropdown" style="width:200px;">
      				<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          				<img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
						</a>
						<ul class="navbar-inverse dropdown-menu" style="color:white; font-size:11pt; padding: 15px 10px 15px 30px; border-radius:0; width:240px;">
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
		}
		if($method_in==8) {
			$content .='
				</ul>
				<ul class="nav navbar-nav navbar-right" style="margin-right:20px;">
        			<li class="dropdown" style="width:200px;">
          			<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          				<img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
						</a>
						<ul class="navbar-inverse dropdown-menu" style="color:white; font-size:11pt; padding: 15px 10px 15px 30px; border-radius:0; width:240px;">
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
		}	
		if($method_in==9) {
			$content .='
				</ul>
				<ul class="nav navbar-nav navbar-right" style="margin-right:20px;">
        			<li class="dropdown" style="width:200px;">
          			<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          				<img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
						</a>
						<ul class="navbar-inverse dropdown-menu" style="color:white; font-size:11pt; padding: 15px 10px 15px 30px; border-radius:0; width:240px;">
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
		}	
	if($method_in==10) {
			$content .='
				</ul>
				<ul class="nav navbar-nav navbar-right" style="margin-right:20px;">
        			<li class="dropdown" style="width:200px;">
          			<a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          				<img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
						</a>
						<ul class="navbar-inverse dropdown-menu" style="color:white; font-size:11pt; padding: 15px 10px 15px 30px; border-radius:0; width:240px;">
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
		}				
		return $content;
	}
	function image_artist($artist_name, $db) {
		$getimage = $db->query("SELECT `name` FROM `last_fm_covers` WHERE artist LIKE '$artist_name'"); 
		$getimages = $getimage->fetch_assoc()['name'];
		if(isset($getimages) and $getimages!="") {							
			$image="covers/".$getimages.".png"; 
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
	
	function image($images, $artist_name, $db, $album) {
		$content="";
		if((!isset($images) or $images=="")) {
			if($album!="") {
				$getimage = $db->query("SELECT `name` FROM `last_fm_covers` WHERE album LIKE '$album'"); 
				if(isset($getimage->num_rows) and  $getimage->num_rows!= 0) {
					$getimages = $getimage->fetch_assoc()['name'];
					$image="covers/".$getimages.".png"; 
				}
				else {
					$getimage = $db->query("SELECT `name` FROM `last_fm_covers` WHERE artist LIKE '$artist_name'"); 
					if(isset($getimage->num_rows) and  $getimage->num_rows!= 0) {
						$getimages = $getimage->fetch_assoc()['name'];
						$image="covers/".$getimages.".png"; 
					}
				}
			}						
		}
		
		else {
			$image_db =  str_replace(".png", "",$images);
			$image_db =  str_replace("http://img2-ak.lst.fm/i/u/34s/", "",$image_db);
			$getimage = $db->query("SELECT `id` FROM `last_fm_covers` WHERE name LIKE '$image_db'"); 
			$getimage_row = $getimage->fetch_assoc();
			if(!isset($getimage_row) or $getimage_row=="") {
				$pfad="covers/".$image_db.".png";
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
			$image="covers/".$image_db.".png"; 
		}
		if(!isset($image) or $image=="") {
			$image="pic/empty.png";		
		}
		return $image;
	}
	
	
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
