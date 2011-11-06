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
/*	$actions['afspelen']['submenu'] = array();
	$actions['afspelen']['submenu']['manage'] = "Beheren";
	$actions['afspelen']['submenu']['add'] = "Toevoegen";
	$actions['afspelen']['submenu']['edit'] = "!Aanpassen";
	$actions['afspelen']['submenu']['delete'] = "!Delete";
	$actions['afspelen']['default_submenu'] = "manage";  // */
	
	$actions['films']['title'] = "Films";
	$actions['films']['file'] = "films.php";
	$actions['films']['submenu'] = array();
	$actions['films']['submenu']['manage'] = "Beheren";
	$actions['films']['submenu']['add'] = "Toevoegen";
	$actions['films']['submenu']['edit'] = "!Aanpassen";
	$actions['films']['submenu']['delete'] = "!Delete";
	$actions['films']['default_submenu'] = "manage";
	
/*	$actions['verhuur']['title'] = "Verhuur";
	$actions['verhuur']['file'] = "verhuur.php";
	$actions['verhuur']['submenu']['manage'] = "Beheren";
	$actions['verhuur']['submenu']['add'] = "Toevoegen";
	$actions['verhuur']['submenu']['delete'] = "!Delete";
	$actions['verhuur']['default_submenu'] = "manage";
	
	$actions['stats']['title'] = "Statestieken";
	$actions['stats']['file'] = "stats.php";
	
	$actions['info']['title'] = "Informatie";
	$actions['info']['file'] = "info.php";	 // */
	
	$actions['default'] = 'afspelen';
?>