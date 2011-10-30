<?php
define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';

#Prep the game objects.
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'mysql.php';
require_once INCLUDE_DIR.'map.php';

//Map parameters.
require_once 'maptest.inc.php';

//Delete old map data.
$query="TRUNCATE map_data";
mysql_do_query($query);

$tiles=array(
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

$map=array();

//Create map
$data=array('width'=>$width,'height'=>$height,'map'=>'','tiles'=>$tiles);
for($y=0;$y<$height;$y++)
    for($x=0;$x<$width;$x++)
        $map[$y][$x]=mt_rand(0,10);

//Encode map the old way.
$data['map']=array_to_string($map,$data['height'],$data['width']);

//Save the old map.
$output=serialize($data);
$handle=fopen(MAPS_DIR."{$mapid}.map",'w');
fwrite($handle,$output);
fclose($handle);

//Create shared memory segment.
$shared_mem= shmop_open($mapid, "c", 0644, strlen($output));
shmop_write($shared_mem,$output,0);
shmop_close($shared_mem);

//Store the map in the database.
foreach($map as $y=>$row)
    {
    $rowstring='';
    foreach($row as $x=>$tile)
        $rowstring.=chr($tile);
    $rowdata=mysql_real_escape_string($rowstring);
    $query="INSERT INTO map_data(mapid,row,data) VALUES ($mapid,$y,'$rowdata')";
    mysql_do_query($query);
    }

$retval=array();
for($ypos=0;$ypos<$GLOBALS['minimap_height'];$ypos++)
    for($xpos=0;$xpos<$GLOBALS['minimap_width'];$xpos++)
        $retval[$ypos][$xpos]=ord($data['map'][($yloc+$ypos)*$data['width']+($xloc+$xpos)]);

//Store a cache.
//Write the cache file.
$filename=MAPS_DIR."cache/{$mapid}_{$yloc}_{$xloc}.map.cache";
$handle=fopen($filename,'w');
fwrite($handle,serialize($retval));
fclose($handle);

echo "Done.<br>";
?>