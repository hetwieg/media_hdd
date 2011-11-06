<?php
// --------------------------------------------------------------
$table = array();
$table['name'] = "movies";
$table['sort'] = "title";

$velden = array();
$velden['title']['title'] = "Titel";
$velden['title']['def'] = "";
$velden['imdb']['title'] = "IMDB Tag";
$velden['imdb']['def'] = "tt";
$velden['file']['title'] = "File";
$velden['file']['def'] = "/media_hdd/films/";
$velden['file']['select'] = "FILE:R:/media_hdd/films/";
$velden['rating']['title'] = "Rating %";
$velden['rating']['def'] = "50";
$velden['release']['title'] = "Release";
$velden['release']['def'] = "2011-12-02";
$velden['cover']['title'] = "Cover";
$velden['cover']['def'] = "";
$velden['kast']['title'] = "Staat in kast";
$velden['kast']['def'] = "";

$defaults = array();
$defaults['playcount'] = 0;

$id_list = array();
$id_list[] = "id";

$file = 'films';
// --------------------------------------------------------------
$sql = "";
if(isset($_GET['do'])){
  switch($_GET['do']) {	
  case "remove":		
	dbConnect($db);
	
	$sql = "";
	foreach($id_list as $value) {
	  $id = "";
	  if(isset($_POST[$value])) $id = $_POST[$value];
	  $id = mysql_real_escape_string($id, $db['link']);

	  $sql .= ($sql != "") ? " AND" : "";
	  $sql .= " `".$value."` = '".$id."'";
	}
	$sql = "DELETE FROM `".$table['name']."` WHERE ".$sql;
	$rs = mysql_query($sql,$db['link']);
    break;
	
  case "update_file":
	dbConnect($db);
	
	$id = "";
	if(isset($_GET['id'])) $id = $_GET['id'];
	$id = mysql_real_escape_string($id, $db['link']);
	$rs = mysql_query("SELECT * FROM `".$table['name']."` WHERE `id` = '".$id."'",$db['link']);		
	$row = mysql_fetch_array($rs);	
	
	$path = pathinfo($row['file']);
	$ext = $path['extension'];
	
	if(strtolower($ext) == "iso")
	{
		$year = date("Y", strtotime($row['release']));
		$title = $row['title']." (".$year.")";
		
		$not_allow = array("/", "\\", ":", "*", "?", "\"", "<", ">", "|");
		$title = str_replace($not_allow, "_", $title);
		
		$tfile = $path['dirname']."/".$title.".iso";
		rename($row['file'], $file);
		
		$path = pathinfo($row['cover']);
		$ext = $path['extension'];
		$img = $title.".".$ext;
		rename("images/covers/".$row['cover'], "images/covers/".$img);
		
		// SQL
		$img = mysql_real_escape_string($img, $db['link']);
		$tfile = mysql_real_escape_string($tfile, $db['link']);
		$rs = mysql_query("UPDATE `".$table['name']."` SET `cover` = '".$img."', `file` = '".$tfile."' WHERE `id` = '".$id."'",$db['link']);			
	} else {
		echo "Volgende ext kan niet gebruikt worden<br />EXT: ".$ext;
	}
	break;
	
  case "addnew":		
	dbConnect($db);
	
	$data = array();
	foreach($defaults as $key => $value)
	{
		$data[$key] = mysql_real_escape_string($value, $db['link']);
	}
	
	foreach($velden as $key => $value)
	{
		$data[$key] = $value['def'];
		if(isset($_POST[$key])) $data[$key] = $_POST[$key];
		$data[$key] = mysql_real_escape_string($data[$key], $db['link']);
	}
	
	$sql_velden = "";
	$sql_values = "";
	foreach($data as $key => $value)
	{
		$sql_velden .= ', `'.$key.'`';
		$sql_values .= ", '".$value."'";
	}	
	$sql_velden = substr($sql_velden, 2);
	$sql_values = substr($sql_values, 2);
	$sql = "REPLACE INTO `".$table['name']."` (".$sql_velden.") VALUES (".$sql_values.");";
	
	$rs = mysql_query($sql,$db['link']) or dbError($db);
    break;
	
  case "update":
	dbConnect($db);
	
	$id = "";
	if(isset($_POST['id'])) $id = $_POST['id'];
	$id = mysql_real_escape_string($id, $db['link']);
	
	$sql = "";
	foreach($velden as $key => $value)
	{
		$data[$key] = $value['def'];
		if(isset($_POST[$key])) $data[$key] = $_POST[$key];
		$data[$key] = mysql_real_escape_string($data[$key], $db['link']);
		
		$sql .= ", `".$key."` = '".$data[$key]."'";
	}
	$sql = substr($sql, 2);
	$sql = "UPDATE `".$table['name']."` SET ".$sql." WHERE `id` = '".$id."';";
	
	$rs = mysql_query($sql,$db['link']) or dbError($db);
    break;
  }
  
  echo "<pre>".$sql."</pre>";
  $template = str_replace("{%:HEADER}", '<META HTTP-EQUIV=Refresh CONTENT="0; URL=/'.$file.'.html">'."\n{%:HEADER}", $template);
}

switch($subaction['key']) {
case "add":
	echo '<form name="form" id="form" action="/'.$file.'/manege.html?do=addnew" method="post"><table width="500" border="0" cellspacing="2" cellpadding="0">';
	foreach($velden as $key => $value)
	{
		$change = ($key == "imdb") ? ' onchange="update_form_imdb()"' : '';		
		$field = '<input type="text" name="'.$key.'" id="'.$key.'" value="'.$value['def'].'"'.$change.'/>';
		
		if(isset($value['select']))
		{
			$field = makeSelect($value['select'], $key);
		}
		
		echo '<tr><th scope="row" width="100">'.$value['title'].':</th><td>'.$field.'</td></tr>'."\n";
	}
	echo '<tr><th scope="row">&nbsp;</th><td><input class="half_input" type="submit" value="Toevoegen" /></td></tr></table></form>';
	break;
	
case "edit":
	dbConnect($db);
	$id = "";
	if(isset($_GET['id'])) $id = $_GET['id'];
	$id = mysql_real_escape_string($id, $db['link']);
	$rs = mysql_query("SELECT * FROM `".$table['name']."` WHERE `id` = '".$id."'",$db['link']);	
	
	$row = mysql_fetch_array($rs);	
	echo '<form name="form" id="form" action="/'.$file.'/manege.html?do=update" method="post"><input type="hidden" name="id" value="'.$id.'" /><table width="500" border="0" cellspacing="2" cellpadding="0">';
	foreach($velden as $key => $value)
	{
		$change = ($key == "imdb") ? ' onchange="update_form_imdb()"' : '';
		$field = '<input type="text" name="'.$key.'" id="'.$key.'" value="'.$row[$key].'"'.$change.'/>';
		
		if(isset($value['select']))
		{
			$field = makeSelect($value['select'], $key, $row[$key]);
		}
		echo '<tr><th scope="row" width="100">'.$value['title'].':</th><td>'.$field.'</td></tr>'."\n";
	}
	echo '<tr><th scope="row">&nbsp;</th><td><input class="half_input" type="submit" value="Aanpasen" /> <input class="half_input" type="reset" value="Reset" /></td></tr></table></form>';
	break;
	
case "delete":
	dbConnect($db);
	$id = "";
	if(isset($_GET['id'])) $id = $_GET['id'];
	$id = mysql_real_escape_string($id, $db['link']);
	$rs = mysql_query("SELECT * FROM `".$table['name']."` WHERE `id` = '".$id."'",$db['link']);	
	
	$row = mysql_fetch_array($rs);	
	echo '<form name="form" id="form" action="/'.$file.'/manege.html?do=remove" method="post"><input type="hidden" name="id" value="'.$id.'" /><table width="500" border="0" cellspacing="2" cellpadding="0">';
	foreach($velden as $key => $value)
	{
		echo '<tr><th scope="row" width="100">'.$value['title'].':</th><td>'.$row[$key].'</td></tr>';
	}
	echo '<tr><th scope="row">&nbsp;</th><td><input class="half_input" type="submit" value="Verwijder" /></td></tr></table></form>';
	break;

case "manage":
	dbConnect($db);
	$rs = mysql_query("SELECT * FROM `".$table['name']."` ORDER BY `".$table['sort']."` ASC",$db['link']);
	
	echo '<form name="form" id="form" action="/'.$file.'/manege.html" method="post"><table id="'.$table['name'].'"><tr>';
	foreach($velden as $key => $value)
	{
		echo '<th scope="col">'.$value['title'].':</th>';
	}
	echo '<th scope="col">Actie</th></tr>';
	
	$count = 1;
	while ($row = mysql_fetch_array($rs)) {
		$id = $row['id'];
		echo '<tr>';
		
		foreach($velden as $key => $value)
		{
			echo '<td>'.$row[$key].'</td>';
		}
						
		echo '<td><a href="/'.$file.'/edit.html?id='.$id.'"><img src="/images/edit.png" width="16px" height="16xp" alt="Edit" /></a>';
		echo '<a href="/'.$file.'/delete.html?id='.$id.'"><img src="/images/delete.png" width="16px" height="16xp" alt="Delete" /></a>';
		echo '<a href="#" onclick="play_movie('."'tv'".', '.$id.');"><img src="/images/play.png" width="16px" height="16xp" alt="Play" /></a>';
		//echo '<a href="/'.$file.'/manage.html?id='.$id.'&amp;do=update_file"><img src="/images/update.png" width="16px" height="16xp" alt="Update" /></a>';
		echo '</td></tr>';
		$count++;
	}
	
	
	echo '<tr><td class="total">Totaal: '.$count.'</td>';
	foreach($velden as $key => $value)
	{
		echo '<td>&nbsp;</td>';
	}
	echo '</tr>';
	
	echo '</table></form>';	
  	break;
	  	
default:
	echo $subaction['key']." not fount!";
	break;
}
?>