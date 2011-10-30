<?php
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'map_consts.php';
require_once INCLUDE_DIR.'map.php';

function fetch_map_data($map_name,$x,$y,$minimap_index,$loader)
    {
    /*
    OK, here's the deal:
    If we had a caching system, we could cache the minimaps.
    Until then, just find the data on the fly.
    */
    //log_error("$map_name,$x,$y,$minimap_index");
    //See if we have a cache file.  If so, then load it and be done.
    $filename=MAPS_DIR."cache/{$map_name}_{$y}_{$x}.map.cache";
    if(file_exists($filename))
        {
        $retval=unserialize(file_get_contents($filename));
        }
    else
        {
        //Load the entire map into RAM for fast access.
        if(!isset($GLOBALS['maps'][$map_name]))
            {
            $map=unserialize(@file_get_contents(MAPS_DIR."$map_name.map"));
            if(!$map)
                return;
            //$map['map']=string_to_array($map['map'],$map['height'],$map['width']);
            $GLOBALS['maps'][$map_name]=&$map;
            }
        else
            $map=&$GLOBALS['maps'][$map_name];
        //Generate the array to return
        $retval=array();
        for($ypos=0;$ypos<$GLOBALS['minimap_height'];$ypos++)
            for($xpos=0;$xpos<$GLOBALS['minimap_width'];$xpos++)
                //$retval[$ypos][$xpos]=$map['map'][$y+$ypos][$x+$xpos];
                $retval[$ypos][$xpos]=ord($map['map'][($yloc+$ypos)*$map['width']+($xloc+$xpos)]);
        //Write the cache file.
        $handle=fopen($filename,'w');
        fwrite($handle,serialize($retval));
        fclose($handle);
        }
    queue_response('receive_minimap_data',$retval,$minimap_index,$loader);
    }
?>
