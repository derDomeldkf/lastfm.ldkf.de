<?php
	include "functions.php";
	$artist_got=$_GET["artist"];

	$artist=str_replace("_", " ", $artist_got);
	if(strpos($artist, "the")==0){

		$artist=str_replace("The ", "", $artist); //get name for later replacement
	}
//echo $artist;
	$url="http://www.azlyrics.com/lyrics/". strtolower(preg_replace ( '/[^a-z0-9]/i', '', $artist))."/".  strtolower(preg_replace ( '/[^a-z0-9]/i', '', $_GET["song"]).".html");
	if(get_headers($url, 1)[0]!="HTTP/1.1 200 OK") {
		$url2="http://www.plyrics.com/lyrics/". strtolower(preg_replace ( '/[^a-z0-9]/i', '', $artist))."/".  strtolower(preg_replace ( '/[^a-z0-9]/i', '', $_GET["song"]).".html");
		if(get_headers($url2, 1)[0]!="HTTP/1.1 200 OK") {
			$url3="https://www.musixmatch.com/lyrics/". str_replace("_", "-",$artist_got)."/". str_replace("_", "-",$_GET["song"]);
			if(get_headers($url3, 1)[0]!="HTTP/1.1 200 OK") {
				
				echo '
					<div class="modal-content">
      				<div class="modal-header" style="padding-top:5px; padding-bottom:20px; padding-right:10px;">
        					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      				</div>
      				<div class="modal-body"><h3>Lyrics für diesen Song nicht gefunden.</h3><br>'.$url.'<br>'.$url2 .'<br>'.$url3 .'</div><div class="modal-footer">
        					<button type="button" class="btn btn-default" data-dismiss="modal">Schlie&szlig;en</button>
      				</div>
      			</div>
      		';
      	}
      	else {
       		$response=  file_get_contents($url3);
															
				$ly=substr(preg_replace('/^(.*)nstrumental":0,"explicit":.,"body":"(.*)"languageDescription"(.*)/s', "$2", $response), 0, -18);
    			$ly =  str_replace("\\n", "<br>",$ly);
    			$ly='<div><h4><b>'. str_replace("_", " ",$artist_got).'</b></h4>
    			</div><b>"'. str_replace("_", " ",$_GET["song"]). '"</b><br><br>'.$ly;


				$link='<a href="https://www.musixmatch.com/" target="_blank">musixmatch</a></div><div class="modal-footer">';			
           	echo lyrics_text($ly, $link);
      	}   	      	
  		}
		else {  										
   		$response=  file_get_contents($url2);                      
    		$ly=substr(preg_replace("/^(.*)END OF JANGO PLAYER -->(.*)<!-- end of lyrics -->(.*)/s", "$2", $response), 2, -23);
   		$ly =  str_replace(strtoupper($artist)." LYRICS", $artist,$ly);
    		$ly =  str_replace("<h1>", "<h4><b>",$ly);
    		$ly =  str_replace("</h1>", "</b></h4>",$ly);
    		$ly =  str_replace("</h3>", "<br><br>",$ly);
    		$ly =  str_replace("<h3>", "",$ly);
    		$link='<a href="http://www.plyrics.com" target="_blank">PLyrics</a></div><div class="modal-footer">';
    		echo lyrics_text($ly, $link);
    		
		}
	}
	else {
    	$response=  file_get_contents($url);
    	$ly=substr(preg_replace("/^(.*)END OF JANGO PLAYER -->(.*)<!-- MxM banner -->(.*)/s", "$2", $response), 2, -23);
    	$ly =  str_replace(strtoupper($artist)." LYRICS", $artist,$ly);
    	$ly =  str_replace("<h2>", "<h4>",$ly);
    	$ly =  str_replace("</h2>", "</h4>",$ly);
    	$link='<a href="http://azlyrics.com" target="_blank">AZLyrics</a></div><div class="modal-footer">';
    	echo lyrics_text($ly, $link);  
    	

	}

?> 
