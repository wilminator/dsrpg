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

$filename=MAPS_DIR."cache/{$mapid}_{$yloc}_{$xloc}.map.cache";

$retval=unserialize(file_get_contents($filename));

$file_end_time=microtime(true);

$script_diff=$file_end_time-$script_start_time;
$access_diff=$file_end_time-$file_start_time;
$overhead=$script_diff-$access_diff;

echo "Test 4: Load and unserialize cache file.<br>";
echo "Script:$file_end_time-$script_start_time=".($script_diff)."<br>";
echo "Access:$file_end_time-$file_start_time=".($access_diff)."<br>";
echo "Overhead: $overhead";
?>