<?php
	$td='<td class="list table_head"';
	include "config.php";
	include "db_connect.php";
	include "functions.php";
	
	if(!isset($_POST['tableselect'])) {
		$gethighest = $db->query("SELECT MAX(id) FROM `tables_title`"); 
		$getplaces = $gethighest->fetch_assoc();
		$id=$getplaces['MAX(id)'];
		$post=$id;
	}
	else {
		$id=$_POST['tableselect'];
		$post="";
	}
	$gethighest = $db->query("SELECT time FROM `tables_title` where id LIKE '$id'"); 
	$getplaces = $gethighest->fetch_assoc();
	$time=$getplaces['time'];
	$date=date('d.m.Y',strtotime($time));
	$getname = $db->query("SELECT `table_name` FROM `tables_title` WHERE id LIKE '$id' "); 
	$name = $getname->fetch_assoc();
	$db_name=$name['table_name'];
	$period="In der letzten Woche gehört von";
	$id="tracks_week";
	echo group2($db_name, $period, $db, $td, $id, $date);
?> 
