<?php
include("config.php");

$action = "";
if(isset($_GET['action'])) $action = $_GET['action'];

switch($action) {
case "filmlist":
  printFilmList();
  break;
  
case "img":
  $file = $dirs['main_movies']."/";
  if(isset($_GET['name'])) $file .= $_GET['name'];
  else die("NoName");
  
  if(strstr("..", $file)) die(".. not allowd");
  $ext = pathinfo($file);
  if($ext['extension'] != "jpg") die("no jpg");

  showImg($file, 170, 120, false);
  break;

case "cover":
  $file = $dirs['main_movies']."/.COVERS/";
  if(isset($_GET['name'])) $file .= $_GET['name'].".jpg";
  else die("NoName");
  
  if(strstr("..", $file)) die(".. not allowd");
  $ext = pathinfo($file);
  if($ext['extension'] != "jpg") die("no jpg");

  $resize = true;
  if(isset($_GET['full_size'])) $resize = false;

 showImg($file, 130, 170, $resize);
  break;  

case "play":
  $file = $dirs['main_movies'];
  if(isset($_GET['name'])) $file .= $_GET['name'];
  else die("NoName");

  if(strstr("..", $file)) die(".. not allowd");

  // Streaming hier aan file toevoegen
  $stream = ""; //":sout=#transcode{vcodec=mp4v,vb=2048,scale=1,acodec=mp3,ab=128,channels=2}:std{access=rtp{ttl=1},mux=raw,dst=172.20.1.135:1234,sap,name=\"test\"}";

  // Gnome VLC late starte  --extraintf=http

  // Alleen als VLC niet op full screen staat
  //print_r(get_request("http://vlc.home.hetwieg.nl/requests/status.xml?command=fullscreen"));

  print_r(get_request("http://vlc.home.hetwieg.nl/requests/status.xml?command=pl_empty"));
  print_r(get_request("http://vlc.home.hetwieg.nl/requests/status.xml?command=in_play&input=".str_replace("+","%20",urlencode($file))." ".str_replace("+","%20",urlencode($stream))));
  break;  

default:
  echo $action;
  break;
}

function get_request($url, $referer='', $session='') { 
  // parse the given URL
  $url = parse_url($url);
  
  print_r($url);

  if ($url['scheme'] != 'http') { 
    die('Error: Only HTTP request are supported !');
  }
  
  // extract host and path:
  $host = $url['host'];
  $path = $url['path']."?".$url['query'];
 
  // open a socket connection on port 80 - timeout: 30 sec
  $fp = fsockopen($host, 80, $errno, $errstr, 30);
  
  if ($fp){
    // send the request headers:
    fputs($fp, "GET $path HTTP/1.1\r\n");
    fputs($fp, "Host: $host\r\n");
    
    if ($referer != '')
      fputs($fp, "Referer: $referer\r\n");
    
    if ($session != '')
      fputs($fp, "X-SESSION_ID: $session\r\n");

    fputs($fp, "Connection: close\r\n\r\n");
    
    $result = ''; 
    while(!feof($fp)) {
      // receive the results of the request
      $result .= fgets($fp, 128);
    }
  }
  else { 
    return array(
		 'status' => 'err', 
		 'error' => "$errstr ($errno)"
		 );
  }
  
  // close the socket connection:
  fclose($fp);
  
  // split the result header from the content
  $result = explode("\r\n\r\n", $result, 2);
  
  $header = isset($result[0]) ? $result[0] : '';
  $content = isset($result[1]) ? $result[1] : '';
  
  // return as structured array:
  return array(
	       'status' => 'ok',
	       'header' => $header,
	       'content' => $content
	       );
} 

function showImg($file, $nWidth, $nHeight, $resize)
{
  $ext = pathinfo($file);

  if(file_exists($file) && $resize == true) {
    $file = trim($file);  
    header("Content-Type: image/jpeg");
    
    $im = LoadJpeg($file);

    $oWidth  = imagesx($im);
    $oHeight = imagesy($im);
    
    $nim = imagecreatetruecolor($nWidth, $nHeight);
    //$nim = imagecreate($nDestinationWidth, $nDestinationHeight);
    //$oResult = imagecopyresampled( $oDestinationImage, $im, 0, 0, 0, 0, $nDestinationWidth, $nDestinationHeight, $nWidth, $nHeight);
    imagecopyresized( $nim, $im, 0, 0, 0, 0, $nWidth, $nHeight, $oWidth, $oHeight);
    
    imagejpeg($nim);
    imagedestroy($im);
    imagedestroy($nim);
  } elseif(file_exists($file) && $resize == false) {
    header("Content-Type: image/png");
    echo implode('', file($file));
  } elseif(file_exists(str_replace(".jpg", ".png", $file))) {
    $file = str_replace(".jpg", ".png", $file);
    header("Content-Type: image/png");
    echo implode('', file($file));
  } else {
    $file = trim("images/blank.png");  
    header("Content-Type: image/png");
    echo implode('', file($file));
  }
}

function LoadJpeg($imgname)
{
  /* Attempt to open */
  $im = @imagecreatefromjpeg($imgname);
  
  /* See if it failed */
  if(!$im) {
    /* Create a black image */
    $im  = imagecreatetruecolor(150, 30);
    $bgc = imagecolorallocate($im, 255, 255, 255);
    $tc  = imagecolorallocate($im, 0, 0, 0);
    
    imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
    
    /* Output an error message */
    imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
  }
  
  return $im;
}

function printFilmList()
{
  global $dirs;
  
  header("content-type: text/xml"); // XML Header
  
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  print_dir($dirs['main_movies']."/");
}

function print_dir($glob)
{
  global $dirs;
  
  $tmp = pathinfo($glob);
  $path = $tmp['basename'];		
  echo "<dir name=\"".$path."\" url=\"".str_replace($dirs['main_movies'], "", str_replace("%2F", "/", urlencode($glob)))."\" >\n";
  
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