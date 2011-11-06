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

$actions = array();
$actions['!default']['type']   = "page";
$actions['!default']['title']  = "";
$actions['!default']['image']  = "";
$actions['!default']['width']  = "16px";
$actions['!default']['height'] = "16px";

$actions['edit']['title'] = "Aanpassen";
$actions['edit']['image'] = "/images/edit.png";

$actions['delete']['title'] = "Verwijder";
$actions['delete']['image'] = "/images/delete.png";

$actions['update_file']['type']  = "do";
$actions['update_file']['title'] = "Update";
$actions['update_file']['image'] = "/images/update.png";

$file = 'films';
// --------------------------------------------------------------
$sql = "";
if(isset($_GET['do'])){
  switch($_GET['do']) {	
  case "remove":		
	dbConnect($db);
	
	$where = "";
	foreach($id_list as $value) {
	  $id = "";
	  if(isset($_POST[$value])) $id = $_POST[$value];
	  $id = mysql_real_escape_string($id, $db['link']);

	  $where .= ($sql != "") ? " AND" : "";
	  $where .= " `".$value."` = '".$id."'";
	}
	$sql = "DELETE FROM `".$table['name']."` WHERE ".$where;
	$rs = mysql_query($sql,$db['link']);
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
	
	$where = "";
	foreach($id_list as $value) {
	  $id = "";
	  if(isset($_POST[$value])) $id = $_POST[$value];
	  $id = mysql_real_escape_string($id, $db['link']);

	  $where .= ($where != "") ? " AND" : "";
	  $where .= " `".$value."` = '".$id."'";
	}
	$sql = "UPDATE `".$table['name']."` SET ".$sql." WHERE ".$where.";";
	
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
		
		if(isset($value['select'])) $field = makeSelect($value['select'], $key, $value['def']);
		
		echo '<tr><th scope="row" width="100">'.$value['title'].':</th><td>'.$field.'</td></tr>'."\n";
	}
	echo '<tr><th scope="row">&nbsp;</th><td><input class="half_input" type="submit" value="Toevoegen" /></td></tr></table></form>';
	break;
	
case "edit":
	dbConnect($db);
	
	$where = "";
	$html = "";
	foreach($id_list as $value) {
	  $id = "";
	  if(isset($_GET[$value])) $id = $_GET[$value];
	  $id = mysql_real_escape_string($id, $db['link']);

	  $where .= ($where != "") ? " AND" : "";
	  $where .= " `".$value."` = '".$id."'";
	  $html .= '<input type="hidden" name="'.$value.'" value="'.$id.'" />';
	}
	$rs = mysql_query("SELECT * FROM `".$table['name']."` WHERE ".$where,$db['link']);	
	
	$row = mysql_fetch_array($rs);	
	echo '<form name="form" id="form" action="/'.$file.'/manege.html?do=update" method="post">'.$html;	
	echo '<table width="500" border="0" cellspacing="2" cellpadding="0">';
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
	echo '<tr><th scope="row">&nbsp;</th><td>';
	echo '<input class="half_input" type="submit" value="Aanpasen" /> ';
	echo '<input class="half_input" type="reset" value="Reset" />';
	echo '</td></tr></table></form>';
	break;
	
case "delete":
	dbConnect($db);
	
	$where = "";
	$html = "";
	foreach($id_list as $value) {
	  $id = "";
	  if(isset($_GET[$value])) $id = $_GET[$value];
	  $id = mysql_real_escape_string($id, $db['link']);

	  $where .= ($where != "") ? " AND" : "";
	  $where .= " `".$value."` = '".$id."'";
	  $html .= '<input type="hidden" name="'.$value.'" value="'.$id.'" />';
	}
	$rs = mysql_query("SELECT * FROM `".$table['name']."` WHERE ".$where,$db['link']);
	
	$row = mysql_fetch_array($rs);	
	echo '<form name="form" id="form" action="/'.$file.'/manege.html?do=remove" method="post"><input type="hidden" name="id" value="'.$id.'" /><table width="300" border="0" cellspacing="2" cellpadding="0">';
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
	foreach($velden as $key => $value) {
		echo '<th scope="col">'.$value['title'].':</th>';
	}
	echo '<th scope="col">Actie</th></tr>';
	
	$count = 1;
	while ($row = mysql_fetch_array($rs)) {
	  $indent_link = "";
	  foreach($id_list as $value) {
	    $id = $row[$value];

	    $indent_link .= ($indent_link != "") ? "&amp;" : "";
	    $indent_link .= $value."=".$id;
	  }
	  
	  foreach($velden as $key => $value) {
	    echo '<td>'.$row[$key].'</td>';
	  }
	  
	  echo '<td>';
	  foreach($actions as $key => $value) {
	    if($key == "!default") continue;
		
		$value = array_merge($actions['!default'], $value);
		
		$page = "";
		if($value['type'] == "page") $page = "/".$key;
		if($value['type'] == "do")   $indent_link .= "&amp;do=".$key;
		
	  	echo '<a href="/'.$file.$page.'.html?'.$indent_link.'"><img src="'.$value['image'].'" width="'.$value['width'].'" height="'.$value['height'].'" alt="'.$value['title'].'" /></a>';
	  }	  
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