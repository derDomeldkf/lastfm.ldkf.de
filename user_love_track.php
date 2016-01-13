<?php
	$i=0;
	$playing=0;
	$page_n=$page+1;
	$page_l=$page-1;
	echo head();
					foreach($tracks as $track){
						$album_name="";
						$artist_decode= $track->artist;
						$track_name= $track->name;
						$url= $track->url;
						$image_decode= $track->image;
						$date_decode= $track->date;
						$artist_array = get_object_vars($artist_decode);
						//$album_array = get_object_vars($album_decode);
						//$url_array = get_object_vars($album_decode);
						$date_array = get_object_vars($date_decode); 
						$image_array = get_object_vars($image_decode[0]);
						$artist_name=$artist_array['name'];
						if($artist_name=="Royal Rebublic") {$artist_name="Royal Republic";}
						//$album_name=$album_array['#text'];
						$date_uts=$date_array['uts'];
						$images=$image_array['#text'];
						$image=image($images, $artist_name);
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
						if($ch_m_in!=$ch_m or $show_date==1){
							$date_eng=gmdate("l, j. F Y", $date_uts);
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
							$date_eng =  str_replace("May", "Juni",$date_eng);
							$date_eng =  str_replace("May", "Juli",$date_eng);
							$date_eng =  str_replace("October", "Oktober",$date_eng);
							$date_eng =  str_replace("December", "Dezember",$date_eng);
							echo'
								<tr>
									<td colspan="4" style="'; if($show_date!=1){ echo' padding-top:18px;'; }echo' padding-bottom:7px; font-size:15pt;">
										'.$date_eng.'
									</td>
								</tr>
							';								
						}
						echo'
								<tr frame="hsides" class="" style="'; if($i==0) { echo'background-color: #F2F2F2;';} if($ch_m_in!=$ch_m or $show_date==1) {echo' border-top: 1px solid #D8D8D8; ';} echo'">
									<td class="list">
   	         	    			<span class="">
      	         	     			<span class="chartlist-image">
         	   							<a href="https://www.last.fm/de/user/'.$username.'/library/music/'. urlencode($artist_name).'/'. urlencode($album_name).'" title="'.$artist_name.' - '.$album_name.'" target="_blank"><img src="'.$image.'"></a>
        									</span>
 	  	 								</span>              		
      	   	       		</td>
      	   	       		<td class="list" style="padding-left:10px;>
   	         	    			<span class="">
      	         	     			<span class="chartlist-image">
         	   							<img width="18px" height="18px;" src="'; 
         	   							//	if($love==1) {
														echo "pic/love.png";
         	   							//	}
         	   							//	else {
         	   							//		echo "pic/nolove.png";
         	   							//	}
         	   							echo '">
        									</span>
 	  	 								</span>              		
      	   	       		</td>  	   	       			
 	        	   				<td class="chartlist-ellipsis-wrap list" style="padding-left:10px; min-width:600px;">
   	         	    			<span class="chartlist-ellipsis-wrap">
      	         	     			<span class="chartlist-artists">
         	   							<a href="https://www.last.fm/de/user/'.$username.'/library/music/'. urlencode($artist_name).'" title="'.$artist_name.'" target="_blank">'.$artist_name.'</a>
        									</span>
											<span class="artist-name-spacer"> — </span>
											<a href="'.$url.'" title="'.$artist_name.'-'.$track_name.'" target="_blank" class="link-block-target">                                                         
    											'.$track_name.'
  	  										</a>
 	  	 								</span>
									</td>';
									echo lyric($artist_name, $track_name);
									echo'
									<td class="list" style="padding-right:2px;">
            	   	     		<span title="'.$date_uts.'">'.$gmdate.'</span>
               	   	   </td>
								</tr>
           			';
           			if($i==0){$i++;}
           			else {$i--;}
           			$check_date=$date_uts;
           		} 
           		echo '
           	</tbody>
			</table>
		</div>
	';
?>