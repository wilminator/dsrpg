<?php
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'map_consts.php';
require_once INCLUDE_DIR.'map.php';

function fetch_map_size($map_name)
    {
    //Load the entire map into RAM for fast access.
    if(!isset($GLOBALS['maps'][$map_name]))
        {
        $map=unserialize(@file_get_contents(MAPS_DIR."$map_name.map"));
        if(!$map)
            return;
        $map['map']=string_to_array($map['map'],$map['height'],$map['width']);
        $GLOBALS['maps'][$map_name]=&$map;
        }
    else
        $map=&$GLOBALS['maps'][$map_name];
    queue_response('load_map_data',$map['width'],$map['height'],$map['tiles']);
    }
?>
