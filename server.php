<?php
require("config.php");
require("functions.php");

header("Content-Type: text/xml");
	
switch($_GET['action'])
{
case "vlc":
	include("actions/vlc.php");
	break;
	
case "imdb":
	$data = get_imdb_id($_GET['id']);
	
	// http://ia.media-imdb.com/images/M/MV5BMTkwOTU5MTA2M15BMl5BanBnXkFtZTYwMjYyNTc3._V1._SY317_.jpg
	// http://ia.media-imdb.com/images/M/MV5BMjE0MDU2MTQ2M15BMl5BanBnXkFtZTYwOTc5OTI3.jpg
	$url = $data['cover'];
	$data['cover'] = md5(get_match('/.*\/[A-Z]\/([0-9A-Za-z@]+)\.jpg/', $data['cover'])).".jpg";
	
	if(!file_exists("images/covers/".$data['cover'])) {
		$fp = fopen("images/covers/".$data['cover'], "wb");
		fwrite($fp, get_data($url));
		fclose($fp);
	}
	
	echo "<imdb>\n";
    foreach($data as $key => $value) {
		echo '<data id="'.$key.'">'.trim($value)."</data>\n";
	}
	echo "</imdb>";
	
	break;
case "list":
	dbConnect($db);	
	$sql = "SELECT * FROM `movies`";
	$rs = mysql_query($sql,$db['link']);
	
	$movies = array();
	while($row = mysql_fetch_array($rs)) {
		$movies[$row['file']] = $row;
	}
	
	// Alle files afgaan
	$dir_list = array();
	$dir_list = directory_list($dirs['main_movies'],false,false,".|..|.DS_Store|.svn|index.jpg|index.png");
	
	echo makeNodes($movies, $dir_list);
  break;
case "filmlist":
  printFilmList();
  break;
case "cover":	
	$img = (isset($_GET["img"])) ? $_GET["img"] : "";
	$ext = (isset($_GET["ext"])) ? $_GET["ext"] : "jpg";
		
	$file = "images/covers/".$img.".".$ext;
	
	if(strstr($file, "..")) $file = "images/covers/d41d8cd98f00b204e9800998ecf8427e.jpg";
	
	showCover($file);
	break;
case "dir_cover":	
	$img = (isset($_GET["img"])) ? $_GET["img"] : "";
	$ext = (isset($_GET["ext"])) ? $_GET["ext"] : "jpg";
		
	$file = "/media_hdd/films/".$img.".".$ext;
	
	if(strstr($file, "..")) $file = "images/covers/d41d8cd98f00b204e9800998ecf8427e.jpg";
	
	showCover($file);
	break;
default:	
	header("Content-Type: text/html");
	phpinfo();
}

function showCover($file)
{
	header('Content-Type: image/jpeg');
	if(!file_exists($file)) $file = "images/covers/d41d8cd98f00b204e9800998ecf8427e.jpg";
	echo implode("", file($file));
}

function printFilmList()
{
  global $dirs;
  
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  print_dir($dirs['main_movies']."/");
}

function print_dir($glob)
{
  global $dirs;
  
  $tmp = pathinfo($glob);
  $path = $tmp['basename'];	
  
  echo "<dir name=\"".$path."\" url=\"".str_replace($dirs['main_movies'], "", str_replace("%2F", "/", urlencode($glob)))."\" ".$cover.">\n";
  
  $dirlist = array();
  foreach(glob($glob."*", GLOB_ONLYDIR) as $dir) {
    $dirlist[] = $dir;				
    print_dir($dir."/");
  }
  
  foreach(glob($glob."*") as $file) {
    if(in_array($file, $dirlist)) continue;
    if(strstr($file, "index.jpg")) continue;
    if(strstr($file, "index.png")) continue;
    
    $tmp = pathinfo($file);
    $path = substr($tmp['basename'], 0, strlen($tmp['basename']) - (strlen($tmp['extension'])+1));			
    
    echo "<file url=\"".urlencode(str_replace($dirs['main_movies'], "", $file))."\" name=\"".htmlspecialchars($path)."\" cover=\"";
    
    if(file_exists(str_replace("//", "/", $dirs['main_movies']."/.COVERS/".$path.".jpg"))) {
      echo urlencode(str_replace("//", "/", $path.".jpg"));
    }
    
    echo "\">";
    
    echo "todo: IMDB INFO";
    
    echo "</file>";
  }
  
  echo "</dir>\n";
}
?>