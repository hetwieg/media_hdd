<?php
switch($_GET['subaction'])
{
case "play":
	$source = $_GET['source'];
	
	dbConnect($db);
	
	$id = "";
	if(isset($_GET['id'])) $id = $_GET['id'];
	$id = mysql_real_escape_string($id, $db['link']);
	$rs = mysql_query("SELECT * FROM `movies` WHERE `id` = '".$id."'",$db['link']);		
	$row = mysql_fetch_array($rs);
	
	$stream = "";
	$file   = $row['file'];
	
	echo '<vlc file="'.$file.'" stream="'.$stream.'">';
	$empty = get_request("http://vlc.home.hetwieg.nl/requests/status.xml?command=pl_empty");
    $play = get_request("http://vlc.home.hetwieg.nl/requests/status.xml?command=in_play&input=".str_replace("+","%20",urlencode($file))." ".str_replace("+","%20",urlencode($stream)));	
	sleep(1);
	$full_screen = get_request("http://vlc.home.hetwieg.nl/requests/status.xml?command=fullscreen");
	
	echo str_replace('<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>',"",$play['content']);
	echo '</vlc>';
	
	break;
}
?>