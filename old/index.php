<?php include("config.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Media Player</title>
    <link href="/style/main.css" rel="stylesheet" type="text/css">
    <script src="/script/jquery.js" language="javascript" type="application/javascript"></script>
    <script src="/script/nav.js" language="javascript" type="application/javascript"></script>
  </head>
  <body>
    <h1 onclick="openDir('/');">Media Player</h1>
    <form action="#" id="zoekform"><input type="text" name="zoeken" id="zoeken" onkeyup="search(this.value)" /></form>
    <h2 id="path">/</h2>
    <input type="hidden" id="folder_url" value="" />
    <div id="dir_view">&nbsp;</div>
    <div id="filminfo" onclick="this.style.display = 'none'">
      <div id="info">
        <h3 id="info_title">TITLE</h3>
        <img id="info_img" src="#" alt="" />
        <div id="info_description">description</div>
        <div id="info_rating">rating</div>
        <div id="info_keywords">keywords</div>
        <div id="info_buttons"><input type="button" id="start" value="Start de film" onclick="" /></div>
      </div>
    </div>
  ToDo: Import http://lomalogue.com/jquery/quicksearch/
    <script language="javascript">init();</script>
  </body>
</html>