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
	$n=1;
	$skript=0;
	$m=0;
	$i=0;
	$playing=0;
	if($user[3] > 1 and $user[2]==1) {
		echo head("RecentTracks", $user_in);
	}
	if(isset($user[0][1])) {
		$dd=$user[0][1]->date;
		$dd = get_object_vars($dd); 
		$ddd=$dd['uts'];	
	}

	elseif(isset($user[0][0])) {
		$dd=$user[0][0]->date;
		$dd = get_object_vars($dd); 
		$ddd=$dd['uts'];	
	}	
	if(isset($time_check) and isset($ddd) and $time_check == $ddd) {
		$error= "no";
		}
		if($user[3] <= 1 and $page_in>1) {
			$error= "no";
		}
		if(!isset($error) or $error!="no") {
			foreach($user[0] as $track){
				$noplay=1;
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
					$date_decode="jetzt";
					$noplay=0;
					$n=0; //dont set back because m==1 wäre ja nächste runde
				}

				if($noplay==0 and $user[2]!=1) {
				}
				else {

					$artist_array = get_object_vars($artist_decode);
					$album_array = get_object_vars($album_decode);
					$url_array = get_object_vars($album_decode);
					if($date_decode!="jetzt") {
						$date_array = get_object_vars($date_decode); 
					}
					$image_array = get_object_vars($image_decode[0]);
					$artist_name=$artist_array['name'];
					$artist_mbid=$artist_array['mbid'];
					$album_name=$album_array['#text'];
					if($date_decode!="jetzt") {
						$date_uts=$date_array['uts'];  //lastfm fehler ausgleichen
					}
					
					$images=$image_array['#text'];
					//$images="";
					$image=image($images, $artist_name, $db, $album_name);
					if($date_decode!="jetzt") {
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
						if((($ch_m_in!=$ch_m or $show_date==1) and $user[2]==1) or (($ch_m_in!=$ch_m or $show_date==1) and $user[2]!=1 and $m!=0)){
							$date_eng=gmdate("l, j. F Y", $date_uts);
							$date_eng=month_rename($date_eng);  //monate vom englischen ins deutsche umbenennen, weil php-funktion nicht läuft
							echo'
								<tr class="'.$user[1].' ';
								if($user[2]==1) {
									if($m==0 and $date_decode!="jetzt") {							
										echo 'repl';
									}
									else {
										echo 'del';
									} 
								}
								
								echo'">
									<td colspan="5" style="';
									if($show_date!=1 or $playing==1){ 
										echo'padding-top:18px; ';
									}
	
									echo' padding-bottom:7px; font-size:15pt; border-top:0">
										'.$date_eng.'
									</td>
								</tr>
							';								
						}
					}
					echo'
					<tr frame="hsides" class="'.$user[1].' ';
						
					if($date_decode=="jetzt") { 
						echo'repl';
					}

					elseif($m==0 and $noplay==0) {echo 'del';}
						echo '" style="
					';
					if($date_decode=="jetzt") { 
						echo'background-color: #F2F5A9;';
					}
					echo'
						">
							<td class="list">
	   	  					<span class="">
	           					<span class="chartlist-image">
	           						<a href="http://www.last.fm/de/user/'.$user[1].'/library/music/'. urlencode($artist_name).'/'. urlencode($album_name).'" title="'.$artist_name; if (isset($album_name) and $album_name!="") {echo ' - '.$album_name; }echo '" target="_blank"><img src="'.$image.'" width="34" height="34"></a>
	      						</span>
	 	  						</span>              		
	       				</td>
	        				<td class="list" style="padding-left:10px; min-width:20px;">
	   	  					<span class="">
	           					<span class="'.$track_mbid.'">
	      						'; 
	        						echo love($love,$artist_name, $track_name, $limit_in, $user_in, $page_in);
	           					if($date_decode=="jetzt") {
	         						$gmdate=$date_decode;
	         						$date_uts="now";
	         					}
	         					echo '
	         						
	        						</span>
	 	  	 					</span>              		
	      				</td>
	 	      			<td class="chartlist-ellipsis-wrap list" style="padding-left:10px; padding-right:4px; white-space: nowrap;">
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
	      	  		echo'<td class="list" style="padding-right:10px; white-space: nowrap;">';
	         		
	         		echo '
	         			<span title="'.$date_uts;    if(($m==1 and $n==0) or ($noplay==1 and $m==0)){echo '" class="'.$user[1].' last'; }    echo'" style="vertical-align:bottom; float:left; padding-right:8px;">
	         				'.$gmdate.'
	         			</span>';
	         		if($date_decode=="jetzt") {
	       				echo '
	       	   			<figure style="">
									<img src="pic/test.gif" width="15px" height="20px">
								</figure>
							';
	         		}
	         		echo '	
						</td>';
						echo '</tr>
						
	   			';
	      		if($i==0){$i++;}
	      		else {$i--;}
	      		if($date_decode!="jetzt") {
	      			$check_date=$date_uts;
	      			$playing=0;
	     			}
	     		 	else {
						$playing=1;           			
	      		}
	  			$m++;
				} 
			}
			if($user[3] > 1 and $user[2]==1) {
				echo '
  					</tbody>
  				</table>
  			</div>
  		'; 
  			}
  		
  	}
  	else { echo 1;}//close head()

?>