<?php
include("config.php");

$action = "";
if(isset($_GET['action'])) $action = $_GET['action'];

switch($action) {
case "listdir":
  $dir = $dirs['main_movies'];
  if(isset($_GET['dir'])) $dir .= str_replace("..", "", $_GET['dir']);
  //echo "DEBUG: ".$dir;
  listDir($dir);
  break;
  
case "listfilm":
  $dir = $dirs['main_movies'];
  if(isset($_GET['dir'])) $dir .= str_replace("..", "", $_GET['dir']);
  //echo "DEBUG: ".$dir;
  listFilm($dir);
  break;
  
case "img":
  $file = "";
  if(isset($_GET['name'])) $file .= $_GET['name'];
  else return;
  
  $imgs = file("images/images.lst");
  foreach($imgs as $img)
  {
	  //if(strlen($img) < 3) continue;
	  $t = explode("|", $img);
	  
	  if($t[0] == $file)
	  {
		$file = trim($t[1]);	  
		
		header("Content-Type: image/jpg");
		echo implode('', file($file));
		return;
	  }
  }
  break;
  
case "cover":
  $file = "";
  if(isset($_GET['name'])) $file .= $_GET['name'];
  else return;

  //header("Content-Type: image/jpg");
  echo implode('', file($dirs['main_movies']."/.COVERS/".$file.".jpg"));
  break;
  
default:
  echo $action;
}

function StartsWith($Haystack, $Needle){
    // Recommended version, using strpos
    return strpos($Haystack, $Needle) === 0;
}


function listDir($dir)
{
  global $dirs;
  echo str_replace("//", "/", str_replace($dirs['main_movies'], "", $dir)."/\n");
  
  foreach(glob($dir."/*", GLOB_ONLYDIR) as $dir) {
	if(StartsWith($dir, ".")) continue;
	  
    $path = pathinfo($dir);
    echo $path['basename'];
	if(file_exists($dir."/index.jpg"))
	{
		echo "*";
	}
	echo "\n";
  }
}

function listFilm($dir)
{
  global $dirs;
  echo str_replace("//", "/", str_replace($dirs['main_movies'], "", $dir)."/\n");
  
  $dirs[] = "";
  foreach(glob($dir."/*", GLOB_ONLYDIR) as $dir) {
	  $dirs[] = $dir;
  }
  
  foreach(glob($dir."/*") as $file) {
	if(StartsWith($dir, ".")) continue;
	if(in_array($file, $dirs)) continue;
	
	$tmp = pathinfo($file);
    $path = substr($tmp['basename'], 0, strlen($tmp['basename']) - (strlen($tmp['extension'])+1));
	
	echo $path;
	
	if(file_exists(str_replace("//", "/", $dirs['main_movies']."/.COVERS/".$path.".jpg")))
	{
		echo "*";
	}
	echo "\n";
  }
}
?>