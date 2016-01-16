<?php

$i = 0;
$playing = 0;
$page_n = $page + 1;
$page_l = $page - 1;
echo head();
foreach ($tracks as $track) {
    $artist_decode = $track->artist;
    $love = $track->loved;
    $album_decode = $track->album;
    $track_name = $track->name;
    $url = $track->url;
    $image_decode = $track->image;
    if (isset($track->date)) {
        $date_decode = $track->date;
    } else {
        $date_decode = "wird gerade gehört";
    }
    $artist_array = get_object_vars($artist_decode);
    $album_array = get_object_vars($album_decode);
    $url_array = get_object_vars($album_decode);
    if ($date_decode != "wird gerade gehört") {
        $date_array = get_object_vars($date_decode);
    }
    $image_array = get_object_vars($image_decode[0]);
    $artist_name = $artist_array['name'];
    $album_name = $album_array['#text'];
    if ($date_decode != "wird gerade gehört") {
        $date_uts = $date_array['uts'] + 3600;  //lastfm fehler ausgleichen
    }
    $images = $image_array['#text'];
    $image = image($images, $artist_name);
    if ($date_decode != "wird gerade gehört") {
        $gmdate = gmdate("H:i", $date_uts);
        $ch_m_in = gmdate("d", $date_uts);
        $show_date = 0;
        if (!isset($check_date) or $check_date == "") {
            $ch_m = $ch_m_in;
            $show_date = 1;
        } else {
            $ch_m = gmdate("d", $check_date);
        }
        if ($ch_m_in != $ch_m or $show_date == 1) {
            $date_eng = gmdate("l, j. F Y", $date_uts);
            $date_eng = month_rename($date_eng);  //monate vom englischen ins deutsche umbenennen, weil php-funktion nicht läuft
            echo'
							<tr>
								<td colspan="5" style="';
            if ($show_date != 1 or $playing == 1) {
                echo'padding-top:18px;';
            }
            echo' padding-bottom:7px; font-size:15pt;">
									' . $date_eng . '
								</td>
							</tr>
						';
        }
    }
    echo'
					<tr frame="hsides" class="" style="
				';
    if ($date_decode == "wird gerade gehört") {
        echo'background-color: #F2F5A9;';
    } elseif ($i == 0) {
        echo'background-color: #F2F2F2;';
    }
    if (((isset($ch_m_in) and isset($ch_m)) and $ch_m_in != $ch_m) or ( isset($show_date) and $show_date == 1)) {
        echo' border-top: 1px solid #D2D2D2; ';
    }
    echo'">
            <td class="list">
            <span class="">
            <span class="chartlist-image">
                    <a href="https://www.last.fm/de/user/' . $username . '/library/music/' . urlencode($artist_name) . '/' . urlencode($album_name) . '" title="' . $artist_name . ' - ';

    if ($album_name != "") {
        echo $album_name;
    } else {
        echo $track_name;
    }
    echo '" target="_blank"><img src="' . $image . '"></a>
      						</span>
 	  						</span>              		
       				</td>
        				<td class="list" style="padding-left:10px;">
   	  					<span class="">
           					<span class="chartlist-image">
      							<img width="18px" height="18px;" src="
      						';
    if ($love == 1) {
        echo "pic/love.png";
    } else {
        echo "pic/nolove.png";
    }
    if ($date_decode == "wird gerade gehört") {
        $gmdate = $date_decode;
        $date_uts = "now";
    }
    echo '">
                                </span>
                                </span>              		
        </td>
        <td class="chartlist-ellipsis-wrap list" style="padding-left:10px; padding-right:4px; min-width:600px;">
                <span class="chartlist-ellipsis-wrap">
                <span class="chartlist-artists">
                                <a href="https://www.last.fm/de/user/' . $username . '/library/music/' . urlencode($artist_name) . '" title="' . $artist_name . '" target="_blank">' . $artist_name . '</a>
                                </span>
                                        <span class="artist-name-spacer"> — </span>
                                                <a href="' . $url . '" title="' . $artist_name . '-' . $track_name . '" target="_blank" class="link-block-target">                                                         
                                                ' . $track_name . '
                                                </a>
                                        </span>
                                </td>
                        ';
    echo lyric($artist_name, $track_name);
    echo'<td class="list" style="padding-right:2px;">';
    if ($date_decode == "wird gerade gehört") {
        //gif (playing now)
        echo '
         	   			<figure style="float:left; padding-right:8px;">
  									<img src="pic/test.gif" width="15px" height="20px">
  								</figure>
  							';
        //gif
    }
    echo '
         				<span title="' . $date_uts . '" style="vertical-align:bottom; padding-right:3px;">
         					' . $gmdate . '
         				</span>
						</td>
					</tr>
   			';
    if ($i == 0) {
        $i++;
    } else {
        $i--;
    }
    if ($date_decode != "wird gerade gehört") {
        $check_date = $date_uts;
        $playing = 0;
    } else {
        $playing = 1;
    }
}
echo '			</tbody>
  			</table>
  		</div>
  	'; //close head()
?>