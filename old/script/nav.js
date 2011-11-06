// JavaScript Document
var xmlDoc;

function init(){
    LoadXML();
}

function loadXMLDoc(dname)
{
    if (window.XMLHttpRequest)
    {
	xhttp=new XMLHttpRequest();
    }
    else
    {
	xhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xhttp.open("GET",dname,false);
    xhttp.send();
    return xhttp.responseXML;
} 

function openDir(dir)
{
    var tmp = "";
   
    $(xmlDoc).find('dir').each(function() {
	if($(this).attr('url') == dir)
	{
	    $(this).children().each(function(index, node) {
		xx = node;
		tmp = tmp + getDiv(index, node);
	    });
	}
    });
    
    var dir_view = document.getElementById("dir_view");
    dir_view.innerHTML = tmp;

    // Links maken voor terug
    tmp = "";

    data = dir.split('/');
    var url = "/";
    var name = "Films";
    tmp = "<a href=\"#/\" onclick=\"openDir('" + url + "')\">" + name + "</a> / ";

    for(var i=1;i<data.length-1;i++) {
        name = decodeURIComponent(data[i].replace(/\+/g, " "));
	url = url + data[i] + "/";
	tmp = tmp +  "<a href=\"#" + url + "\" onclick=\"openDir('" + url + "')\">" + name + "</a> / ";
    }

    location.hash = url;

    var path = document.getElementById("path");
    path.innerHTML = tmp;    
}

function getDiv(index, node)
{
    if(xx.nodeName == "dir")
    {
	var name = node.attributes['name'].value;
	var url = node.attributes['url'].value;
	var img = "/server.php?action=img&name=" + url.replace(/&/g,"%26") + "index.jpg";	

	return "<div class=\"folder_film\" onclick=\"openDir('" + url + "')\"><img src=\"" + img + "\" alt=\"" + name + "\" />" + name + "</div>";
    }
    else {
	var name = node.attributes['name'].value;
	var url = node.attributes['url'].value;
	var img = "/server.php?action=cover&name=" + name.replace(/&/g,"%26");	
	
	return "<div class=\"film\" onclick=\"openFilm('" + url + "')\"><img src=\"" + img + "\" alt=\"" + name + "\" />" + name + "</div>";
    }
    
    return "";
} 

function openFilm(url)
{
    $(xmlDoc).find('file').each(function() {
	if($(this).attr('url') == url)
	{
	    var info = document.getElementById("filminfo");
	    info.style.display = "block";    
	    
	    var title = document.getElementById("info_title");
	    var name =  $(this).attr('name');
	    title.innerHTML = name;
	    
	    var img = document.getElementById("info_img");
	    img.src = "/server.php?action=cover&name=" + name.replace(/&/g,"%26");

	    var start = document.getElementById("start");
	    start.setAttribute('onclick', "start('" + url + "')");	    
	}
    });
}

function LoadXML()
{
    $(document).ready(function(){
	$.ajax({
	    type: "GET",
	    url: "/server.php?action=filmlist",
	    dataType: "xml",
	    success: function(xml) {
		xmlDoc = xml;
		
		var tmp = "/";
		if(location.hash != "") {
		    tmp = location.hash.replace(/#/, "");
		}	

		openDir(tmp);
	    }
	});
    });
}

function start(url)
{
    $(document).ready(function(){
	$.ajax({
	    type: "GET",
	    url: "/server.php?action=play&name=" + url,
	    dataType: "xml",
	    success: function(xml) {

	    }
	});
    });
}