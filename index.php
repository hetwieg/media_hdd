<?php
require_once("config.php");
require_once("functions.php");

// Action laden en defualt als action niet bestaat
$action = "";
$y = "";
if(isset($_GET['action'])) $y = $_GET['action'];
if(array_key_exists($y, $actions)) {
  $action = $actions[$y];
  $action['key'] = $y;
}
else {
  $y = $actions['default'];
  $action = $actions[$y];
  $action['key'] = $y;
}

// Sub action
$subaction = array();
$subaction['title'] = "";	
$subaction['key'] = "";	
if(array_key_exists("submenu", $action)) {
  $t = "";
  if(isset($_GET['subaction'])) $t = $_GET['subaction'];
  
  if(array_key_exists($t, $action['submenu'])) {
    $subaction['key'] = $t;
    $subaction['title'] = $action['submenu'][$t];
  }
  else {			
    $subaction['key'] = $action['default_submenu'];
    $subaction['title'] = $action['submenu'][$action['default_submenu']];
  }
  
  if(substr($subaction['title'], 0, 1) == "!") $subaction['title'] = substr($subaction['title'], 1);
}

// Menu maken
$menu = "";
foreach($actions as $key => $value) {
  if($key == "default") continue;
  
  $active = ($value == $action) ? " class=\"active\"" : "";
  $menu .= "<li".$active."><a href=\"/".$key.".html\">".$value['title']."</a> ";
  
  if(array_key_exists("submenu", $value)) {
    $menu .= "\n<ul>\n";
    foreach($value['submenu'] as $ikey => $ivalue) {
	  if(substr($ivalue,0,1) == "!") continue;
      $menu .= "<li><a href=\"/".$key."/".$ikey.".html\">".$ivalue."</a></li>\n";
    }
    $menu .= "</ul>\n";
  }
  
  $menu .= "</li>\n";
}	

// Template aanmaken
$template = implode("", file("template.html"));
$template = str_replace("{%:MENU}",  $menu, $template);

$title = ($subaction['title'] != "") ? $action['title']." | ".$subaction['title'] : $action['title'];
$template = str_replace("{%:TITLE}", $title, $template);
$template = str_replace("{!:DATUM}", date('j M Y H:i:s'), $template);

ob_start();
include("actions/".$action['file']);
$site = trim(ob_get_clean());
$template = str_replace("{%:SITE}", $site, $template);
	
$template = str_replace("{%:HEADER}", "", $template);

echo $template;	
?>