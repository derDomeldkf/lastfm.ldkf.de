<?php
	session_start();
	include "config.php";
	include "db_connect.php";
	include "functions.php";
	$content="";
	$getmembers = $db->query("SELECT `username` FROM `ldkf_lastfm` order by `username` ASC"); 
	while($members = $getmembers->fetch_assoc()){
		$member[]=$members['username'];
	}
	foreach($member as $member_name){
		$content .= '
		   <li><a class="mem" id="'.$member_name.'" href="#">'.$member_name.'</a></li>
			';
	}  
	echo $content;

?> 
