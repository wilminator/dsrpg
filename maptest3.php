<?php
$script_start_time=microtime(true);

define ('INCLUDE_DIR','include/');
require_once INCLUDE_DIR.'errorlog.php';

#Prep the game objects.
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'constants.php';
//require_once INCLUDE_DIR.'mysql.php';
//require_once INCLUDE_DIR.'map.php';

//Map parameters.
require_once 'maptest.inc.php';

$file_start_time=microtime(true);

$map=unserialize(@file_get_contents(MAPS_DIR."$mapid.map"));

$retval=array();
for($ypos=0;$ypos<$GLOBALS['minimap_height'];$ypos++)
    {
    $offset=($yloc+$ypos)*$map['width']+$xloc;
    for($xpos=0;$xpos<$GLOBALS['minimap_width'];$xpos++)
        $retval[$ypos][$xpos]=ord($map['map'][$offset+$xpos]);
    }

$file_end_time=microtime(true);


$script_diff=$file_end_time-$script_start_time;
$access_diff=$file_end_time-$file_start_time;
$overhead=$script_diff-$access_diff;

echo "Test 3: Load and unserialize whole file, parse partial structure with optimized index.<br>";
echo "Script:$file_end_time-$script_start_time=".($script_diff)."<br>";
echo "Access:$file_end_time-$file_start_time=".($access_diff)."<br>";
echo "Overhead: $overhead";
?>