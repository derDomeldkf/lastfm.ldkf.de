<?php
	$i=0;
	$page_n=$user[2]+1;
	$page_l=$user[2]-1;
	$place=(($page_in-1)*$limit_in)+1;
	$counter_cont=1;
	$getid = $db->query("SELECT id FROM `ldkf_lastfm` WHERE username LIKE '$user_in'"); 
	$id=$getid->fetch_assoc()['id'];
	if($user[3] > 1 and $user[2]==1) {
		echo head("TopTracks", $user_in);
	}
			foreach($user[0] as $track){
				$playtime=0;
				$track_name= $track->name;
				$count= $track->playcount;
				$artist_name= $track->artist->name;
				$url= $track->url;
				$image_decode= $track->image;
				$image_array = get_object_vars($image_decode[0]);
				$images=$image_array['#text'];
				$album="";
				$image=image($images, $artist_name, $db, $album);

				$artist_name=str_replace("'", "_", $artist_name);
				$playtime=0;
				$getid = $db->query("SELECT id FROM `lastfm_artists` WHERE name = '$artist_name'"); 
				$aid=$getid->fetch_assoc()['id'];
				$tdb=str_replace("'", "_", $track_name);
				#echo $aid.$track_name;
				$getids = $db->query("SELECT id FROM `lastfm_tracks` WHERE aid= '$aid' and name LIKE '$tdb'"); 
				while(isset($getids->num_rows) and  $getids->num_rows!= 0 and $tracks = $getids->fetch_assoc()){
					$trackid[]=$tracks['id'];
				} 
				#echo $artist_name;
				#var_dump($trackid );
				$playtime=0;
				#echo "<br>";
				if(isset($trackid) and $trackid) {
					foreach ($trackid as $tid) {
						$getplaytime = $db->query("SELECT playtime FROM `".$id."_tracks` WHERE tid = '$tid'"); 
               	if(isset($getplaytime->num_rows) and  $getplaytime->num_rows!= 0) {
							$playtime=$getplaytime->fetch_assoc()['playtime'];
						}
					}
					
				}
				$trackid="";
				if($counter_cont==1 and $user[2]==1) {$count_max=$count;}
				elseif($counter_cont==1 and $user[2]!=1) {$count_max=$_POST[5] ;}
				echo'
					<tr class="">
						<td class="list">
   	  	    			<span class="">
           	     			<span class="chartlist-image">
        							<img src="'.$image.'">
      						</span>
 	  						</span>              		
             		</td>
						<td class="list" style="padding-left:15px;">
   	        			<span class="">
      	   				<span class="chartlist-image">
         						'.$place.'
        						</span>
 	  	 					</span>         		
      	   		</td>
    					<td class="list" style="padding-right:5px; padding-left:8px;">
   	        			<span class="chartlist-ellipsis-wrap">
   	        			   <span class="chartlist-artists">
         	   				<a href="http://www.last.fm/de/user/'.$user[1].'/library/music/'. urlencode($artist_name).'" title="'.$artist_name.'" target="_blank">'.$artist_name.'</a>
        						</span>
								<span class="artist-name-spacer"> — </span>
									<a href="'.$url.'" title="'.$artist_name.'-'.$track_name.'" target="_blank" class="link-block-target">                                                         
    									'; echo (strlen($track_name)>35) ? "<br>" : ""; echo $track_name.'
  	  								</a>
 	  	 						</span>
      	   			</td>
      	   		'; 
					
      	  			$m=0; 
						$st=((50)*$count/$count_max);	  				
    					echo'      	   
      	   			<td class="list visible-lg-block visible-md-block visible-sm-block" style="padding-right:8px; ">
      	   				<div class="'; 
      	   				if($st>strlen($count)*2){ 
      	   					echo'textunter';
      	   				}
      	   				echo '">'; 
    							while($m<$st) {
    								echo '<img style="'; 
    								if($m==0) {
    								//	echo 'border-top-left-radius:3px; border-bottom-left-radius:3px;';
    								} 
    								if($m+1>=$st) {
    								//	echo 'border-top-right-radius:3px; border-bottom-right-radius:3px';
    								} 
    								echo'" src="pic/count.png" height:15px;>'; 
									$m++; 					
    							}
    							echo '<span style="padding-left:5px;"';
    							if($st>strlen($count)*2){}
    							else { 
    								echo' ';
    							}
    							echo ' id="'.$method.'_'.$counter_cont.'_'.$page_in.'">
    								'.$count.'
    							</span>
    							
    						</div>';
    						
							$m=0; 
						$st=3+(60/$page_in)*$playtime/($count_max*300);    				
    					echo'      	   
      	   				<div class="'; 
      	   				if($st>strlen($count)*2){ 
      	   					echo'textunter';
      	   				}
      	   				echo '">'; 
    							while($m<$st) {
    								echo '<img style="'; 
    								if($m==0) {
    								//	echo 'border-top-left-radius:3px; border-bottom-left-radius:3px;';
    								} 
    								if($m+1>=$st) {
    								//	echo 'border-top-right-radius:3px; border-bottom-right-radius:3px';
    								} 
    								echo'" src="pic/time.png" height:15px;>'; 
									$m++; 					
    							}
    							echo '<span style="padding-left:5px;"';
    							if($st>strlen($count)*2){}
    							else { 
    								echo' ';
    							}
    							echo '
    								>'. floor($playtime/3600) .':'.($playtime /60) % 60 .':'. $playtime % 60 .'    						
    					   							
    							</span>
    						</div>
            		</td>
        			</tr>
         	';
           	if($i==0){$i++;}
           	else {$i--;}
           	$place++;
         	$counter_cont++;
         }  		
			if($user[3] > 1 and $user[2]==1) {
				echo '
  					</tbody>
  				</table>
  			</div>
  		'; 
  	}
?>