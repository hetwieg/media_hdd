<?php	
	//error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	error_reporting(E_ALL);
	ini_set("display_errors", 1); 

	/* --- MySQL Gegevens -------------------------------- */
	$db = array();
	$db['host'] = "localhost";
	$db['port'] = "3306";
	$db['user'] = "media_player";
	$db['pass'] = "4DVEUK7DtbPR3Ncf";
	$db['data'] = "media_player";
	
	/* --- IMDB Gegevens --------------------------------- */
	$imdb = array();
	$imdb['url'] = "http://www.imdb.com/title/tt";
	$imdb['title'] = '/<title>(.*)\([0-9]{4}\).*- IMDb<\/title>/isU';
	$imdb['cover'] = '/<td[^>]*id="img_primary".+?<img[^>]*src="([^_]+).*(jpg)"/s';
	$imdb['release']  = '/<h4[^>]*>Release Date:<\/h4>.*datetime="(.*)".*<\/div>/isU';
	//<span itemprop="ratingValue">6.4</span>
	//$imdb['rating'] = '/.*<span itemprop="ratingValue"> ([0-9\.]+) </span>.*/isU';
	
	/* --- Map Gegevens ---------------------------------- */
	$dirs['main_movies'] = "/media_hdd/films";
	
	/* --- Menu Pages ------------------------------------ */
	$actions = array();
	$actions['afspelen']['title'] = "Afspelen";
	$actions['afspelen']['file'] = "afspelen.php";
	
	$db_file = "films";
	$actions[$db_file]['title'] = "Films";
	$actions[$db_file]['file'] = $db_file.".php";
	$actions[$db_file]['submenu'] = array();
	$actions[$db_file]['submenu']['manage'] = "Beheren";
	$actions[$db_file]['submenu']['add'] = "Toevoegen";
	$actions[$db_file]['submenu']['edit'] = "!Aanpassen";
	$actions[$db_file]['submenu']['delete'] = "!Delete";
	$actions[$db_file]['default_submenu'] = "manage";
	
	$db_file = "lists";
	$actions[$db_file]['title'] = "Films (D)";
	$actions[$db_file]['file'] = $db_file.".php";
	$actions[$db_file]['submenu'] = array();
	$actions[$db_file]['submenu']['manage'] = "Beheren";
	$actions[$db_file]['submenu']['add'] = "Toevoegen";
	$actions[$db_file]['submenu']['edit'] = "!Aanpassen";
	$actions[$db_file]['submenu']['delete'] = "!Delete";
	$actions[$db_file]['default_submenu'] = "manage";
		
	$actions['default'] = 'afspelen';
?>