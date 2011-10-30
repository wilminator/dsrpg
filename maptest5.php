<?php
$script_start_time=microtime(true);

define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';

#Prep the game objects.
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'mysql.php';
//require_once INCLUDE_DIR.'map.php';

//Map parameters.
require_once 'maptest.inc.php';

$file_start_time=microtime(true);

$ylen=$yloc+$GLOBALS['minimap_height']-1;
$xlen=$xloc+$GLOBALS['minimap_width']-1;
$query="SELECT row-$ylen AS ypos, data FROM map_data where row BETWEEN $yloc AND $ylen AND mapid=$mapid";
$result=mysql_do_query($query);
$retval=array();
while($data=mysql_fetch_assoc($result))
    {
    for($xpos=0;$xpos<$GLOBALS['minimap_width'];$xpos++)
        $retval[$data['ypos']][$xpos]=ord($data['data'][$xloc+$xpos]);
    }

$file_end_time=microtime(true);

$script_diff=$file_end_time-$script_start_time;
$access_diff=$file_end_time-$file_start_time;
$overhead=$script_diff-$access_diff;

echo "Test 5: DB based map, parse restricted return.<br>";
echo "Script:$file_end_time-$script_start_time=".($script_diff)."<br>";
echo "Access:$file_end_time-$file_start_time=".($access_diff)."<br>";
echo "Overhead: $overhead";
?>