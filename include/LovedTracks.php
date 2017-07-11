<?php
	$i=0;
	$playing=0;
	$m=0;
	$page_n=$user[2]+1;
	$page_l=$user[2]-1;
	if($user[3] > 1 and $user[2]==1) {
		echo head("LovedTracks", $user_in);
	}	
			foreach($user[0] as $track){
				$album_name="";
				$artist_decode= $track->artist;
				$track_name= $track->name;
				$url= $track->url;
				$image_decode= $track->image;
				$date_decode= $track->date;
				$artist_array = get_object_vars($artist_decode);
				$date_array = get_object_vars($date_decode); 
				$image_array = get_object_vars($image_decode[0]);
				$artist_name=$artist_array['name'];
				$date_uts=$date_array['uts'];
				$images=$image_array['#text'];
				$album="";
				$image=image($images, $artist_name, $db, $album);
				$gmdate = gmdate("H:i", $date_uts);
				$ch_m_in=gmdate("d", $date_uts);
				$show_date=0;
				if(!isset($check_date) or $check_date=="") {
					$ch_m=$ch_m_in;
					$show_date=1;
				}
				else {
					$ch_m=gmdate("d", $check_date);
				}
				if((($ch_m_in!=$ch_m or $show_date==1) and $user[2]==1) or (($ch_m_in!=$ch_m or $show_date==1) and $user[2]!=1 and $m!=0)){
					$date_eng=gmdate("l, j. F Y", $date_uts);
					$date_eng=month_rename($date_eng);
					echo'
						<tr>
							<td colspan="4" style="'; if($show_date!=1){ echo' padding-top:18px;'; }echo' padding-bottom:7px; font-size:15pt; border-top:0"">
								'.$date_eng.'
							</td>
						</tr>
					';								
				}
				echo'
					<tr frame="hsides" class="" style="">
						<td class="list">
           	   		<span class="">
            	  			<span class="chartlist-image">
     	   						<a href="https://www.last.fm/de/user/'.$user[1].'/library/music/'. urlencode($artist_name).'" title="'.$artist_name.'" target="_blank"><img src="'.$image.'"></a>
   							</span>
 							</span>              		
      	      	</td>
      	      	<td class="list" style="padding-left:10px;">
   	  					<span class="">
           					<span class="chartlist-image">
      						'; 
        						echo love(1,$artist_name, $track_name, $limit_in, $user_in, $page_in);
           					echo '
         						
        						</span>
 	  	 					</span>              		
      				</td>       			
 	  	   			<td class="chartlist-ellipsis-wrap list" style="padding-left:10px; white-space: nowrap;">
           	   		<span class="chartlist-ellipsis-wrap">
            	  			<span class="chartlist-artists">
     	   						<a href="https://www.last.fm/de/user/'.$user[1].'/library/music/'. urlencode($artist_name).'" title="'.$artist_name.'" target="_blank">'.$artist_name.'</a>
   							</span>
								<span class="artist-name-spacer"> — </span>
								<a href="'.$url.'" title="'.$artist_name.'-'.$track_name.'" target="_blank" class="link-block-target">                                                         
   								'; echo (strlen($track_name)>40) ? "<br>" : ""; echo $track_name.'
  								</a>
 							</span>
						</td>
					';
					echo'
						<td class="list" style="padding-right:2px;">
     	   	     		<span title="'.$date_uts.'">'.$gmdate.'</span>
        	   	   </td>
					</tr>
     			';
    			if($i==0){$i++;}
     			else {$i--;}
         	$check_date=$date_uts;
         	$m++;
			} 
			if($user[3] > 1 and $user[2]==1) {
				echo '
  					</tbody>
  				</table>
  			</div>
  		'; 
  	}
?>