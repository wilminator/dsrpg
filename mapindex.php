<?php
define ('INCLUDE_DIR','include/');

require INCLUDE_DIR.'constants.php';
require INCLUDE_DIR.'paths.php';
require INCLUDE_DIR.'map_consts.php';
require INCLUDE_DIR.'html/html_map.php';
require INCLUDE_DIR.'html/html_loader.php';
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Overworld demo v 0.2.0</title>
<link type="text/css" rel="stylesheet" href="<?php echo CSS_DIR; ?>overworld.css">
<link type="text/css" rel="stylesheet" href="<?php echo CSS_DIR; ?>loader.css">
<script type="text/javascript" src="<?php echo JAVASCRIPT_DIR;?>object_api.js"></script>
<script type="text/javascript" src="<?php echo JAVASCRIPT_DIR;?>proxy.js"></script>
<script type="text/javascript" src="<?php echo JAVASCRIPT_DIR;?>sprite.js"></script>
<script type="text/javascript" src="<?php echo JAVASCRIPT_DIR;?>map.js.php"></script>
<script type="text/javascript" src="<?php echo JAVASCRIPT_DIR;?>map.js"></script>
<script type="text/javascript" src="<?php echo JAVASCRIPT_DIR;?>loader.js"></script>
<script type="text/javascript">

var images='<?php echo IMAGES_DIR; ?>';

var tilefiles=Array(
	"mountains.png",
	"water.png",
	"prairie.png",
	"forest.png",
	"poison.png",
	"cave.png",
	"castle.png",
	"hill.png",
	"town.png",
	"tower.png",
	"poison2.png",
	"desert.png");
var tileimgs;

//Current screen position
var xpos=4096;
var ypos=4096;
var currmap='test';

//Current screen scroll speed
var xspeed=0;
var yspeed=0;

//Screen destination
var xdestpos=0;
var ydestpos=0;

//Size of visual display
var screen_width=800;
var screen_height=600;

function load_tiles()
	{
	var count;
	tileimgs=Array();
	for(count=0;count<tilefiles.length;count++)
		{
		tileimgs[count]=new Image;
		tileimgs[count].src=images+tilefiles[count];
		}
	}

function init()
	{
	//Loading Tiles
	load_tiles();
	//Linking HTML Tiles and making data structures
	initial_link_submap_part_1(0);
	return true;
	}

function initial_link_submap_part_1(submap)
    {
    if(submaps==null)
        {
    	submaps=new Array(2);
    	set_loader_message('Linking HTML data');
    	}
    submaps[submap]={html:object_get('submap'+submap),minimaps:new Array(submap_height*submap_width),layout:new Array(submap_height),ready:false,location:[null,null,null]};
    var base=submap_width*submap_height+2;
    var perc=(base*submap+1)*100/(2*base);
    set_loader_percentage(perc);
    setTimeout("initial_link_minimap("+submap+",0)",10);
    }

function initial_link_minimap(submap,minimap)
    {
    submaps[submap].minimaps[minimap]=make_minimap(submap,minimap);
    var base=submap_width*submap_height+2;
    var perc=(base*submap+2+minimap)*100/(2*base);
    set_loader_percentage(perc);
    if(minimap+1==submap_width*submap_height)
        setTimeout("initial_link_submap_part_2("+submap+")",10);
    else
        setTimeout("initial_link_minimap("+submap+","+(minimap+1).toString()+")",10);
    }

function initial_link_submap_part_2(submap)
    {
    var minimap,minimap1;
    for(minimap=0;minimap<submap_height;minimap++)
        {
        submaps[submap].layout[minimap]=new Array(submap_width);
        for(minimap1=0;minimap1<submap_width;minimap1++)
            submaps[submap].layout[minimap][minimap1]=null;
        }
    var base=submap_width*submap_height+2;
    var perc=(base*submap+base)*100/(2*base);
    set_loader_percentage(perc);
    if(submap+1<2)
        setTimeout("initial_link_submap_part_1("+(submap+1).toString()+")",10);
    else
        {
        visible_submap=submaps[0];
        refreshing_submap=submaps[1];
        //setTimeout("initial_create_map(0)",10);
        setTimeout("initial_link_done()",10);
        }
    }

function initial_link_done()
    {
	//Create the cursor sprite.
	sprite_create('cursor','square.png',xpos,ypos,1,null,{onclick:function(){return onclick_cursor(this);}});

	//Create the player sprite.
	sprite_create('player','x.png',xpos,ypos,2,null,{onclick:function(){return onclick_player(this);}});

	//Start the proxy
	init_tranciever('processor.php',data_dispatch);
	//Now load the map
    init_load_map();
    }

function onclick_player(player)
    {
    alert('Clicked on player');
    }

</script>
</head>
<body onload="init();">
<?php
html_map($submap_width,$submap_height,$minimap_width,$minimap_height,$tile_width,$tile_height);
html_loader();
?>
</body>
</html>
