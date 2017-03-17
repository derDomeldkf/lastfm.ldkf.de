<?php
/*object(stdClass)#11 (9) { 
	["artist"]=> object(stdClass)#12 (4) { 
		["name"]=> string(9) "Nightwish" 
		["mbid"]=> string(36) "00a9f935-ba93-4fc8-a33a-993abe9c936b" 
		["url"]=> string(34) "http://www.last.fm/music/Nightwish" 
		["image"]=> array(4) { 
			[0]=> object(stdClass)#13 (1) { 
				["size"]=> string(5) "small" 
			} 
			[1]=> object(stdClass)#14 (1) { 
				["size"]=> string(6) "medium" 
			}
			[2]=> object(stdClass)#15 (1) { 
				["size"]=> string(5) "large"
			} 
			[3]=> object(stdClass)#16 (1) { 
				["size"]=> string(10) "extralarge" 
			}
		}
	}
	["loved"]=> string(1) "0" 
	["name"]=> string(4) "Nemo" 
	["streamable"]=> string(1) "0" 
	["mbid"]=> string(36) "4246bace-0765-4715-84cb-fbb1e4247f2a" 
	["album"]=> object(stdClass)#17 (2) { 
		["#text"]=> string(4) "Once" 
		["mbid"]=> string(36) "aeea3eaa-3041-4564-a3f1-78e0aeb71eba" 
	}
	["url"]=> string(41) "http://www.last.fm/music/Nightwish/_/Nemo" 
	["image"]=> array(4) { [0]=> object(stdClass)#18 (1) { 
		["size"]=> string(5) "small"
	}
	[1]=> object(stdClass)#19 (1) { ["size"]=> string(6) "medium" } [2]=> object(stdClass)#20 (1) { ["size"]=> string(5) "large" } [3]=> object(stdClass)#21 (1) { ["size"]=> string(10) "extralarge" } } ["date"]=> object(stdClass)#22 (2) { ["uts"]=> string(10) "1454524838" ["#text"]=> string(18) "03 Feb 2016, 18:40" } } 


*/



	$noplay=1;
	$skript=0;
	$m=0;
	$i=0;
	$playing=0;
	$page_n=$user[2]+1;
	$page_l=$user[2]-1;
	if($user[3] > 1) {
		echo head();
	}
	if(isset($user[0][1])) {
		$dd=$user[0][1]->date;
		$dd = get_object_vars($dd); 
		$ddd=$dd['uts']+3600;	
	}

	elseif(isset($user[0][0])) {
		$dd=$user[0][0]->date;
		$dd = get_object_vars($dd); 
		$ddd=$dd['uts']+3600;	
	}	
	if(isset($time_check) and isset($ddd) and $time_check == $ddd) {
		$error= "no";
		}
		if($user[3] <= 1 and $page_in>1) {
			$error= "no";
		}
		if(!isset($error) or $error!="no") {
			foreach($user[0] as $track){
				$album_name="";
				$artist_decode= $track->artist;
				$love= $track->loved;
				$album_decode= $track->album;
				$track_name= $track->name;
				$track_mbid=md5($track_name);
				$url= $track->url;
				$image_decode= $track->image;
				if(isset($track->date)) {
					$date_decode= $track->date;
				}
				else {
					$date_decode="wird gerade gehört";
					$noplay=0;
				}
				$artist_array = get_object_vars($artist_decode);
				$album_array = get_object_vars($album_decode);
				$url_array = get_object_vars($album_decode);
				if($date_decode!="wird gerade gehört") {
					$date_array = get_object_vars($date_decode); 
				}
				$image_array = get_object_vars($image_decode[0]);
				$artist_name=$artist_array['name'];
				$artist_mbid=$artist_array['mbid'];
				$album_name=$album_array['#text'];
				if($date_decode!="wird gerade gehört") {
					$date_uts=$date_array['uts']+3600;  //lastfm fehler ausgleichen
				}
				
				$images=$image_array['#text'];
				//$images="";
				$image=image($images, $artist_name, $db, $album_name);
				if($date_decode!="wird gerade gehört") {
					$gmdate = gmdate("H:i", $date_uts);
					$ch_m_in=gmdate("d", $date_uts);
					$show_date=0;
					if(!isset($check_date) or $check_date=="" ) {
						$ch_m=$ch_m_in;
						$show_date=1;
					}
					else {
						$ch_m=gmdate("d", $check_date);
					}
					if($ch_m_in!=$ch_m or $show_date==1){
						$date_eng=gmdate("l, j. F Y", $date_uts);
						$date_eng=month_rename($date_eng);  //monate vom englischen ins deutsche umbenennen, weil php-funktion nicht läuft
						echo'
							<tr class="';
							if($m==0 and $date_decode!="wird gerade gehört") {							
								echo 'repl';
							}
							else {
								echo 'del';
							} 
							echo'">
								<td colspan="5" style="';
								if($show_date!=1 or $playing==1){ 
									echo'padding-top:18px;';
								}
								echo' padding-bottom:7px; font-size:15pt;">
									'.$date_eng.'
								</td>
							</tr>
						';								
					}
				}
				echo'
					<tr frame="hsides" class="';
					
					if($date_decode=="wird gerade gehört") { 
					echo'repl';
				}
				elseif($m==0 and $noplay==0) {echo 'del';}
					
					echo '" style="
				';
				if($date_decode=="wird gerade gehört") { 
					echo'background-color: #F2F5A9;';
				}
				elseif($i==0) { 
					echo'background-color: #F2F2F2;';
				}
				if(((isset($ch_m_in) and isset($ch_m)) and $ch_m_in!=$ch_m) or (isset($show_date) and $show_date==1)) {
					echo' border-top: 1px solid #D2D2D2; ';
				}
				echo'
					">
						<td class="list">
   	  					<span class="">
           					<span class="chartlist-image">
           						<a href="http://www.last.fm/de/user/'.$user[1].'/library/music/'. urlencode($artist_name).'/'. urlencode($album_name).'" title="'.$artist_name; if (isset($album_name) and $album_name!="") {echo ' - '.$album_name; }echo '" target="_blank"><img src="'.$image.'"></a>
      						</span>
 	  						</span>              		
       				</td>
        				<td class="list" style="padding-left:10px; min-width:20px;">
   	  					<span class="">
           					<span class="'.$track_mbid.'">
      						'; 
        						echo love($love,$artist_name, $track_name, $method_in, $limit_in, $user_in, $page_in);
           					if($date_decode=="wird gerade gehört") {
         						$gmdate=$date_decode;
         						$date_uts="now";
         					}
         					echo '
         						
        						</span>
 	  	 					</span>              		
      				</td>
 	      			<td class="chartlist-ellipsis-wrap list" style="padding-left:10px; padding-right:4px; min-width:600px;">
   	   				<span class="chartlist-ellipsis-wrap">
      	   				<span class="chartlist-artists">
         						<a href="http://www.last.fm/de/user/'.$user[1].'/library/music/'. urlencode($artist_name).'" title="'.$artist_name.'" target="_blank">'.$artist_name.'</a>
        						</span>
								<span class="artist-name-spacer"> — </span>
								<span class="chartlist-titel">
									<a href="'.$url.'" title="'.$artist_name.'-'.$track_name.'" target="_blank" class="link-block-target">                                                         
    									'.$track_name.'
  	  								</a>
  	  							</span>
 	  	 					</span>
						</td>
					';
					echo lyric($artist_name, $track_name);		
      	  		echo'<td class="list" style="padding-right:2px;">';
         		if($date_decode=="wird gerade gehört") {
       				echo '
       	   			<figure style="float:left; padding-right:8px;">
								<img src="pic/test.gif" width="15px" height="20px">
							</figure>
						';
         		}
         		echo '
         			<span title="'.$date_uts;    if($m==1 or ($noplay==1) and $m==0){echo '" id="last'; }    echo'" style="vertical-align:bottom; padding-right:3px;">
         				'.$gmdate.'
         			</span>
					</td>';
					echo play($track_name, $artist_name, $db, $method_in, $limit_in, $page_in, $user_in);
					echo '</tr>
					
   			';
      		if($i==0){$i++;}
      		else {$i--;}
      		if($date_decode!="wird gerade gehört") {
      			$check_date=$date_uts;
      			$playing=0;
     			}
     		 	else {
					$playing=1;           			
      		}
  			$m++;
			} 
			if($user[3] > 1) {
				echo '
  					</tbody>
  				</table>
  			</div>
  		'; 
  	}
  	}
  	else { echo 1;}//close head()

?>