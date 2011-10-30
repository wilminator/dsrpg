<?php
define ('INCLUDE_DIR','include/');

require INCLUDE_DIR.'paths.php';
require INCLUDE_DIR.'map_consts.php';
require INCLUDE_DIR.'map_store.php';
require INCLUDE_DIR.'monster_store.php';
require INCLUDE_DIR.'job_store.php';
require INCLUDE_DIR.'character.php';

#$mapid=$_GET['mapid'];
#$width=$_GET['width'];
#$height=$_GET['height'];

$mapid=1;

$leveling_area_size=4;
$width=256*$leveling_area_size*2;
$height=$width;

//Get the monster list.
$monster_store=new MONSTER_STORE;
$monsters=&$monster_store->get_all_monsters();
//Get the job list
$job_store=new job_STORE;
$jobs=&$job_store->get_all_jobs();

//Get the average XP per level
for($level=1;$level<256;++$level)
    {
    $xp_at_level=0;
    //Make one hero of each job at this level.
    foreach($jobs as $jobid=>$job)
        {
        $hero=hero($job->name,$jobid,$level,0);
        $xp_at_level+=$hero->calculate_pxp();
        }
    $xp_at_level/=count($jobs);
    
    //Now use this avarage XP to make monster parties.
    }


$tileset=array(
	array('filename'=>"prairie.png"  ,'pass'=> 0,'poison'=> 0,'encounter'=>  2),
	array('filename'=>"hill.png"     ,'pass'=> 0,'poison'=> 0,'encounter'=>  4),
	array('filename'=>"desert.png"   ,'pass'=> 0,'poison'=> 0,'encounter'=>  5),
	array('filename'=>"forest.png"   ,'pass'=> 0,'poison'=> 0,'encounter'=>  6),
	array('filename'=>"cave.png"     ,'pass'=> 0,'poison'=> 0,'encounter'=>  0),
	array('filename'=>"castle.png"   ,'pass'=> 0,'poison'=> 0,'encounter'=>  0),
	array('filename'=>"town.png"     ,'pass'=> 0,'poison'=> 0,'encounter'=>  0),
	array('filename'=>"tower.png"    ,'pass'=> 0,'poison'=> 0,'encounter'=>  0),
	array('filename'=>"poison.png"   ,'pass'=> 0,'poison'=> 2,'encounter'=>  7),
	array('filename'=>"poison2.png"  ,'pass'=> 0,'poison'=>15,'encounter'=> 10),
	array('filename'=>"mountains.png",'pass'=> 2,'poison'=> 0,'encounter'=>  0),
	array('filename'=>"water.png"    ,'pass'=> 1,'poison'=> 0,'encounter'=>  3),
	array('filename'=>"wall.png"     ,'pass'=>-1,'poison'=> 0,'encounter'=>  0)
	);

$map=new MAP($width,$height,null,null,$tileset,array(),array(),array());

//Design-a-map!
#This will take a lot more thought than I want to right now.
#This by default will make a huge expanse of prairie.
#Make a few plots of differing strength monsters on the map.

//Save the map
$map_store=new MAP_STORE();
$map_store->put_map(0,$map);

echo "Done! $map is $width*$height";
?>
