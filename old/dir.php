<?php
	if(isset($_GET['file']))
	{
		highlight_file($_GET['file']);	
		die();
	}
	
	header("content-type: text/xml"); // XML Header

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	print_dir($_GET['glob']);
	
	function print_dir($glob)
	{
		echo "<dir name=\"".str_replace("%2F", "/", urlencode($glob))."\">\n";
			
		$dirlist = array();
		foreach(glob($glob."*", GLOB_ONLYDIR) as $dir)
		{				
			$dirlist[] = $dir;				
			print_dir($dir."/");
		}
		
		foreach(glob($glob."*") as $file)
		{
			if(in_array($file, $dirlist)) continue;
			echo "<file url=\"".str_replace("%2F", "/", urlencode($file))."\" size=\"".filesize($file)."\" />\n";
		}
		
		echo "</dir>\n";
	}
?>