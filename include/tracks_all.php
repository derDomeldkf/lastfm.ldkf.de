<?php
	$td='<td class="list table_head"';
	include "config.php";
	include "db_connect.php";
	include "functions.php";
	
	$db_name="last_fm_charts_track_all";
	$period="Gehört von";
	$id="tracks_all";
	echo group2($db_name, $period, $db, $td, $id, "");	   

?> 
