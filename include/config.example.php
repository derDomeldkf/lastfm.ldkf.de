<?php

/* 
 * created by Alwin Ebermann (alwin@alwin.net.au)
 */

$server="localhost";
$username="";
$password="";
$dbname="";
mysql_connect($server, $username, $password);
mysql_select_db($dbname);
 
$db=new mysqli($server, $username, $password, $dbname);

$api_key="";
$secret="";
