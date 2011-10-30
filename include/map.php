<?php
class MAP
    {
    var $width;
    var $height;
    var $data;
    var $zones;
    var $tileset;
    var $events;
    var $npcs;
    var $monsters;

    function MAP($width,$height,$data,$zones,$tileset,$events,$npcs,$monsters)
        {
        $this->width=$width;
        $this->height=$height;
        $this->data=$data;
        $this->zones=$zones;
        $this->tileset=$tileset;
        $this->events=$events;
        $this->npcs=$npcs;
        $this->monsters=$monsters;
        
        if(is_null($data))
            {
            for($y=0;$y<$height;$y++)
                for($x=0;$x<$width;$x++)
                    {
                    $map[$y][$x]=0;
                    }
            $this->data=array_to_string($map,$height,$width);
            $this->zone=$this->data;
            $this->tileset=array(array('filename'=>"water.png",'pass'=>1,'poison'=>0,'encounter'=>3));
            }
        }
}
?>
