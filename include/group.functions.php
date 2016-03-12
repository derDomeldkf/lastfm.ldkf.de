<?php
	include "functions.php";
	function group($db_name, $period, $db, $post, $date, $td) {
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
 				'.$td.'>
 				</td>
				'.$td.' style="padding-left:10px;">
					Platz
				</td>
				'.$td.'>		
				</td> 
				'.$td.' style="padding-left:8px;">
					K&uuml;nstler
				</td>
				'.$td.'>
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
			if($place==1) {
				$count_max=$count;
			}
			$getuser = $db->query("SELECT `user` FROM `".$db_name."` WHERE artist LIKE '$artist_name' "); 
			$users_names= $getuser->fetch_assoc()['user'];
			$user=listeners_dropdown($users_names);
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
				<div style="position:fixed; margin-top:50px;">
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
		$data[0]=$content;
		$data[1]=$place;
		return $data;
	}

#################################################################################################################################################################
	
	function group2($db_name, $period, $db, $method_in, $td) {
 		$content=head();
		$getplace = $db->query("SELECT `titel` FROM ".$db_name." ORDER BY playcount DESC "); 
		while($getplaces = $getplace->fetch_assoc()){
			$places[]=$getplaces['titel'];
		}
		$content .='
 			<tr>
 				'.$td.'>
 				</td>
				'.$td.' style="padding-left:10px;">
					Platz
				</td>
				'.$td.'>				
				</td> 
				'.$td.' style="padding-left:8px;">
					K&uuml;nstler — Titel
				</td>
				'.$td.'>
				</td>
				'.$td.'>
				</td>
				'.$td.'>
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
			$user=listeners_dropdown($users_names);
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
					$content .= play($track_name, $artist_name, $db, $method_in, "", "", "");
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


?>
