<?php
	$i=0;
	$page_n=$user[2]+1;
	$page_l=$user[2]-1;
	$place=(($page_in-1)*$limit_in)+1;
	$counter_cont=1;
	$getid = $db->query("SELECT id FROM `ldkf_lastfm` WHERE username LIKE '$user_in'"); 
	$id=$getid->fetch_assoc()['id'];
	echo head();
	
			foreach($user[0] as $track){
				$playtime=0;
				$album_name= $track->name;
				$count= $track->playcount;
				$artist_name= $track->artist->name;
				$url= $track->url;
				$image_decode= $track->image;
				$image_array = get_object_vars($image_decode[0]);
				$images=$image_array['#text'];
				$album="";
				$image=image($images, $artist_name, $db, $album_name);
				$adb=utf8_encode($artist_name);
				$tdb=utf8_encode($album_name);
				$playtime=0;
				$getid = $db->query("SELECT id FROM `lastfm_artists` WHERE name = '$adb'"); 
				$aid=$getid->fetch_assoc()['id'];
				#echo $aid.$track_name;
				$trackid = array();
				$getids = $db->query("SELECT id FROM `lastfm_album` WHERE aid= '$aid' and name = '$tdb'"); 
				while(isset($getids->num_rows) and  $getids->num_rows!= 0 and $tracks = $getids->fetch_assoc()){
					$trackid[]=$tracks['id'];
				} 
				#echo $artist_name;
				#var_dump($trackid );
				$playtime=0;
				#echo "<br>";
				if(isset($trackid) and $trackid)  {
					foreach ($trackid as $tid) {
						$getplaytime = $db->query("SELECT playtime FROM `".$id."_album` WHERE alid = '$tid'"); #change to playtime
               	if(isset($getplaytime->num_rows) and  $getplaytime->num_rows!= 0) {
							$playtime=$getplaytime->fetch_assoc()['playtime'];#change to playtime
						}
					}
				}
				unset($trackid);
				if($counter_cont==1) {
					$count_max=$count;
				}
				echo'
					<tr class="" style="
				';
				if($i==0) { 
					echo'background-color: #F2F2F2;';
				}
				echo'
					">
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
    					<td class="list" style="padding-right:5px; padding-left:8px; min-width:400px;">
   	        			<span class="chartlist-ellipsis-wrap">
      	         	   <span class="chartlist-artists">
         	   				<a href="http://www.last.fm/de/user/'.$user[1].'/library/music/'. urlencode($artist_name).'" title="'.$artist_name.'" target="_blank">'.$artist_name.'</a>
        						</span>
								<span class="artist-name-spacer"> — </span>
									<a href="'.$url.'" title="'.$artist_name.'-'.$album_name.'" target="_blank" class="link-block-target">                                                         
    									'.$album_name.'
  	  								</a>
 	  	 						</span>
      	   			</td>
      	   		'; 
      	  			$m=0; 
						$st=3+(60/$page_in)*$count/$count_max;    				
    					echo'      	   
      	   			<td class="list" style="padding-right:8px; min-width:200px;">
      	   				<div class="'; 
      	   				if($st>strlen($count)*2){ 
      	   					echo'textunter';
      	   				}
      	   				echo '">'; 
    							while($m<$st) {
    								echo '<img style="'; 
    								if($m==0) {
    									echo 'border-top-left-radius:3px; border-bottom-left-radius:3px;';
    								} 
    								if($m+1>=$st) {
    									echo 'border-top-right-radius:3px; border-bottom-right-radius:3px';
    								} 
    								echo'" src="pic/count.png" height:15px;>'; 
									$m++; 					
    							}
    							echo '<span';
    							if($st>strlen($count)*2){}
    							else { 
    								echo' style="padding-left:5px;"';
    							}
    							echo '
    								>'.$count.'
    							</span>
    							
    						</div>';
    						
							$m=0; 
						$st=3+(60/$page_in)*$playtime/($count_max*300);    				
    					echo'      	   
      	   				<div class="'; 
      	   				if($st>strlen($count)*3){ 
      	   					echo'textunter';
      	   				}
      	   				echo '">'; 
    							while($m<$st) {
    								echo '<img style="'; 
    								if($m==0) {
    									echo 'border-top-left-radius:3px; border-bottom-left-radius:3px;';
    								} 
    								if($m+1>=$st) {
    									echo 'border-top-right-radius:3px; border-bottom-right-radius:3px';
    								} 
    								echo'" src="pic/time.png" height:15px;>'; 
									$m++; 					
    							}
    							echo '<span';
    							if($st>strlen($count)*2){}
    							else { 
    								echo' style="padding-left:5px;"';
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
			echo '
</tbody>
			</table>
		</div>
	';
?>