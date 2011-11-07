<?php	
	/* --- MySQL ----------------------------------------- */	
	function dbConnect(&$db)
	{
		if(isset($db['link'])) return;
		
		$db['link'] = mysql_connect($db['host'].":".$db['port'],$db['user'],$db['pass']);		
		mysql_select_db($db['data'], $db['link']) or dbError($db);
	}
	
	function dbError($db)
	{
		echo mysql_errno($db['link']) . ': ' . mysql_error($db['link']) . "<br />\n";
		die("Verzoek beindigt");
	}
	
	/* --- Files ----------------------------------------- */
	/**
	* directory_list
	* return an array containing optionally all files, only directiories or only files at a file system path
	* @author     cgray The Metamedia Corporation www.metamedia.us
	*
	* @param    $base_path         string    either absolute or relative path
	* @param    $filter_dir        boolean    Filter directories from result (ignored except in last directory if $recursive is true)
	* @param    $filter_files    boolean    Filter files from result
	* @param    $exclude        string    Pipe delimited string of files to always ignore
	* @param    $recursive        boolean    Descend directory to the bottom?
	* @return    $result_list    array    Nested array or false
	* @access public
	* @license    GPL v3
	*/
	$nodecounter = 0;
	function directory_list($directory_base_path, $filter_dir = false, $filter_files = false, $exclude = ".|..|.DS_Store|.svn", $recursive = true){
		global $nodecounter;
		$directory_base_path = rtrim($directory_base_path, "/") . "/";
	
		if (!is_dir($directory_base_path)){
			error_log(__FUNCTION__ . "File at: $directory_base_path is not a directory.");
			return false;
		}
	
		$result_list = array();
		$exclude_array = explode("|", $exclude);
	
		if (!$folder_handle = opendir($directory_base_path)) {
			error_log(__FUNCTION__ . "Could not open directory at: $directory_base_path");
			return false;
		}else{			
			while(false !== ($filename = readdir($folder_handle))) {	
				$result_list["!:DIRNAME"] = $directory_base_path;
							
				if(!in_array($filename, $exclude_array) && substr($filename, 0, 1) != ".") {
					if(is_dir($directory_base_path . $filename . "/")) {
						if($recursive && strcmp($filename, ".")!=0 && strcmp($filename, "..")!=0 ){ // prevent infinite recursion				
							$result_list[$filename] = directory_list("$directory_base_path$filename/", $filter_dir, $filter_files, $exclude, $recursive);
						}elseif(!$filter_dir){
							$result_list[$nodecounter++] = $filename;
						}
					}elseif(!$filter_files){
						$result_list[$nodecounter++] = $filename;
					}
				}
			}
			closedir($folder_handle);
			
			uasort($result_list, 'cmp');
			return $result_list;
		}
	}
	
	function cmp($a, $b) {
		if ($a == $b) {
			return 0;
		}
		
		if(is_array($a) && !is_array($b)) {		
			return -1;
		}
		
		if(!is_array($a) && is_array($b)) {		
			return 1;
		}
		
		if(is_array($a) && is_array($b)) {		
			return ($a["!:DIRNAME"] < $b["!:DIRNAME"]) ? -1 : 1;;
		}
			
		return ($a < $b) ? -1 : 1;
	}
	
	function makeNodes($movies, $dir_list)
	{
		global $dirs;
		$t = "";
				
		if(is_array($dir_list))
		{
			error_log($dir_list['!:DIRNAME']);
			$path = pathinfo($dir_list['!:DIRNAME']);	
			
			$cover = ' cover="[GEEN]"';
			if(file_exists($dir_list['!:DIRNAME']."/index.png")) $cover = ' cover="index.png"';
			if(file_exists($dir_list['!:DIRNAME']."/index.jpg")) $cover = ' cover="index.jpg"';			
			$t .= '<dir url="'.htmlspecialchars(str_replace($dirs['main_movies'], "", $dir_list['!:DIRNAME'])).'" name="'.htmlspecialchars($path['basename']).'"'.$cover.">\n";
			
			foreach($dir_list as $key => $value)
			{
				if(is_array($value)) {
					if(array_key_exists("!:DIRNAME", $value))
					{
						$t .= makeNodes($movies, $value);
					}
				}
				else
				{
					if($key != "!:DIRNAME")
					{
						$sql_tags = ' cover="geen.jpg" filmid="[NotInDB]"';
						
						$f = $dir_list['!:DIRNAME'].$value;
						if(isset($movies[$f]))
						{
							$sql_tags = ' cover="'.$movies[$f]['cover'].'" id="'.$movies[$f]['id'].'"';
						}
						
						$path = pathinfo($value);
						$t .= '<file url="'.htmlspecialchars(str_replace($dirs['main_movies'], "", $dir_list['!:DIRNAME']).$value).'" name="'.htmlspecialchars($path['basename']).'"'.$sql_tags." />\n";
					}
				}
			}
			
			$t .= "</dir>\n";
		}
		
		return $t;
	}
	
	/* --- List Actions ---------------------------------- */
	function makeSelect($type, $name = 'select', $value = '')
	{
		global $db;
				
		$t = '<select id="'.$name.'" name="'.$name.'">';
		
		if(substr($type,0, strlen("FILE:R:")) == "FILE:R:") {
			$dir_list = directory_list(substr($type,strlen("FILE:R:")),false,false,".|..|.DS_Store|.svn|index.jpg|index.png");
			$t .= SelectlistItem($dir_list, $value, "");
		}
		
		if(substr($type,0, strlen("SQL:")) == "SQL:") {
			dbConnect($db);
	        $rs = mysql_query(substr($type,strlen("SQL:")),$db['link']);
			while ($row = mysql_fetch_array($rs)) {
				$select = "";
				if($row['key'] == $value) $select = ' selected="selected"'; 
				
				$t .= '<option value="'.$row['key'].'"'.$select.'>'.$row['value'].'</option>';
			}
		}
		
		$t .= "</select>";
		
		return $t;
	}
	
	function SelectlistItem($dir_list, $selected, $main)
	{	
		$t = "";
		if(is_array($dir_list))
		{
			foreach($dir_list as $key => $value)
			{
				if(is_array($value)) {
					if(array_key_exists("!:DIRNAME", $value))
					{
						$t .= SelectlistItem($value, $selected, $main);
					}
				}
				else
				{
					if($key != "!:DIRNAME")
					{
						$name = htmlspecialchars(str_replace($main, "", $dir_list['!:DIRNAME']).$value);
						
						$select = "";
						if($name == $selected) $select = ' selected="selected"'; 
						
						$t .= '<option value="'.$name.'"'.$select.'>'.$name.'</option>';
					}
				}
			}
		}
		
		return $t;
	}
	/* --- IMDB ------------------------------------------ */
	function get_imdb_id($id)
	{
		global $imdb;
		
		$url = $imdb['url'].$id.'/';
		$imdb_content = get_data($url);
			
		$data = array();
			
		foreach($imdb as $key => $value)
		{
			if($key == "url")	continue;
			$data[$key] = get_match($value,$imdb_content);
		}
		
		return $data;
	}
		
	function get_match($regex,$content)
	{
		preg_match($regex,$content,$matches);
		
		$t = "";
		if(isset($matches[2])) $t=$matches[2];
		
		return $matches[1].$t;
	}
	
	function get_data($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	/* --- VLC ------------------------------------------- */
	
	function get_request($url, $referer='', $session='')
	{ 
		// parse the given URL
		$url = parse_url($url);
		
		//print_r($url);
		
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
?>