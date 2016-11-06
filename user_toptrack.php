<?php
	$i=0;
	$page_n=$user[2]+1;
	$page_l=$user[2]-1;
	$place=(($page_in-1)*$limit_in)+1;
	$counter_cont=1;
	echo head();
			foreach($user[0] as $track){
				$track_name= $track->name;
				$count= $track->playcount;
				$artist_name= $track->artist->name;
				$url= $track->url;
				$image_decode= $track->image;
				$image_array = get_object_vars($image_decode[0]);
				$images=$image_array['#text'];
				$album="";
				$image=image($images, $artist_name, $db, $album);
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
									<a href="'.$url.'" title="'.$artist_name.'-'.$track_name.'" target="_blank" class="link-block-target">                                                         
    									'.$track_name.'
  	  								</a>
 	  	 						</span>
      	   			</td>
      	   		'; 
      	   		echo lyric($artist_name, $track_name);	
      	   		echo play($track_name, $artist_name, $db, $method_in, $limit_in, $page_in, $user_in);							
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