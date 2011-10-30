<?php
function html_map($submap_width,$submap_height,$minimap_width,$minimap_height,$tile_width,$tile_height)
    {
    echo "<div id=\"map\" class=\"map\" style=\"visibility:hidden\">\n";
    $minimap_width_px=$tile_width*$minimap_width;
    $minimap_height_px=$tile_height*$minimap_height;
    $submap_width_px=$minimap_width_px*$submap_width;
    $submap_height_px=$minimap_height_px*$submap_height;
    for($count=0;$count<2;$count++)
    	{
    	echo "<div id=\"submap$count\" class=\"sm\" style=\"width:{$submap_width_px}px;height:{$submap_height_px}px\">";
    	for($sub=0;$sub<$submap_width*$submap_height;$sub++)
            {
            echo "<div id=\"minimap$count-$sub\" class=\"mm\" style=\"width:{$minimap_width_px}px;height:{$minimap_height_px}px\">";
        	echo "</div>\n";
            }
    	echo "</div>\n";
    	}
    IF(DEBUG_MAP)
        {
        echo <<<EOD
  <div style="z-order:1;position:absolute;top:0px;left:0px">
      <input id="x"><br>
      <input id="y"><br>
      <input id="ox"><br>
      <input id="oy">
      <textarea id="code"></textarea>
  </div>

EOD;
        }
    echo "</div>\n";
    }
?>
