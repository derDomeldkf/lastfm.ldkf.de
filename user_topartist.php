<?php
	$i=0;
	$page_n=$user[2]+1;
	$page_l=$user[2]-1;
	$place=(($page_in-1)*$limit_in)+1;
	$counter_cont=1;
	echo '
		<div style="margin-left:40px;">
			<table>
   			<tbody>
   		';
			foreach($user[0] as $track){
				$artist_name= rep($track->name);
				$count= $track->playcount;
				$url= $track->url;
				$image_decode= $track->image;
				$image_array = get_object_vars($image_decode[0]);
				$images=$image_array['#text'];
				if(isset($images) and $images!="") {
					$getimage = $db->query("SELECT `name` FROM `last_fm_covers` WHERE artist LIKE '$artist_name' and album LIKE 'NULL'"); 
					if(isset($getimage->num_rows) and  $getimage->num_rows!= 0) {
						$getimages = $getimage->fetch_assoc()['name'];
						$image="covers/".$getimages; 
					}
					else {
						$image_db =  str_replace("http://img2-ak.lst.fm/i/u/34s/", "",$images);
						$getimage = $db->query("SELECT `id` FROM `last_fm_covers` WHERE name LIKE '$image_db'"); 
						$getimage_row = $getimage->fetch_assoc();
						if(!isset($getimage_row) or $getimage_row=="") {
							$pfad="covers/".$image_db;
							copy($images, $pfad);
							$insert = $db->query("INSERT INTO last_fm_covers (name, artist, album) VALUES ('$image_db', '$artist_name', 'NULL')"); 
						}
						$image="covers/".$image_db; 
					}
				}
				else {
					$image="pic/empty.png";
				}
				if($counter_cont==1) {$count_max=$count;}
				echo'
					<tr class="" style="
				';
				if($i==0) { 
					echo'
						background-color: #F2F2F2;
					';
				}
				echo '
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
        							<a href="http://www.last.fm/de/music/'. urlencode($artist_name).'" title="'.$artist_name.'" target="_blank">'.$artist_name.'</a>
      						</span>
 	  						</span>	
      				</td>
      			'; 
      			$m=0; 
					$st=(80/$page_in)*$count/$count_max;    				
    				echo'      	   
      				<td class="list" style="padding-right:8px; min-width:200px;">
      						<div class="
      					';
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
    							>
    								'.$count.'
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