<?php
	session_start();
	$td='<td class="list table_head"';
	include "config.php";
	include "db_connect.php";
	include "functions.php";
	
	$db_name="last_fm_charts_all";
	$period="Gehört von";
	$out = group($db_name, $period, $db, 0, 0, $td, "artist_all");	
	echo 	$out[0]; 


?> 
