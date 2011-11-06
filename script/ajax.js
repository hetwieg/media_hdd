// JavaScript Document

var mian_url_imdb = "http://tv.home.hetwieg.nl/server/imdb/";

function LoadFilmList()
{
    $(document).ready(function(){
	$.ajax({
	    type: "GET",
	    url: "/server/list.xml",
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

function openDir(dir)
{
    var tmp = "";
   
    $(xmlDoc).find('dir').each(function() {
	if($(this).attr('url') == dir)
	{
	    $(this).children().each(function(index, node) {
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
    if(node.nodeName == "dir")
    {
		var name = node.attributes['name'].value;
		var url = node.attributes['url'].value;
		var img = node.attributes['cover'].value;
		
		if(img == "[GEEN]") {
			img = "/images/blank.png"
		} 
		else {		
			img = "/images/covers/folders" + url + img; 
		}

		return "<div class=\"folder_film\" onclick=\"openDir('" + url + "')\"><img src=\"" + img + "\" alt=\"" + name + "\" /><div class=\"file_name\">" + name + "</div></div>";
    }
    else {
		var name = node.attributes['name'].value;
		var url = node.attributes['url'].value;
		var cover = node.attributes['cover'].value;
		var img = "/images/covers/" + cover.replace(/&/g,"%26");	
	
		return "<div class=\"film\" onclick=\"openFilm('" + url + "')\"><img src=\"" + img + "\" alt=\"" + name + "\" /><div class=\"file_name\">" + name + "</div></div>";
    }
    
    return "";
} 

function update_form_imdb() 
{
	var imdb = document.getElementById('imdb').value;
	
	$.ajax({
	    type: "GET",
	    url: mian_url_imdb + imdb + ".xml",
	    dataType: "xml",
	    success: function(xml) {
			$(xml).find('data').each(function(){
 				var id = $(this).attr('id');
				var data = $(this).text();
				
				document.getElementById(id).value = data;
			});
	    }
	});
}

function play_movie(source, id)
{
	$.ajax({
	    type: "GET",
	    url: "/server/vlc/" + source + "/play.xml?id=" + id,
	    dataType: "xml",
	    success: function(xml) {
	    }
	});
}