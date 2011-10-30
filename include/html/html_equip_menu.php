<?php
function get_equip_points()
    {
    return array(
        'lhand'=>array(400,225),
        'lammo'=>array(400,275),
        'rhand'=>array(  0,225),
        'rammo'=>array(  0,275),
        'larm' =>array(400,125),
        'rarm' =>array(  0,125),
        'body' =>array(200,175),
        'head' =>array(400, 25),
        'back' =>array(  0, 50),
        'feet' =>array(200,400)
        );
    }
function html_equip_menu()
    {
    $images=MENU_IMAGES_DIR;
    $locations=get_equip_points();
    echo "<div class=\"equipwindow\" id=\"equipwindow\" style=\"background-image:url({$images}body.png)\">\n";
    $none_url="{$images}bignone.png";
    foreach($locations as $index=>$location)
        {
        list($left,$top)=$location;
        echo <<<EOD
    <div class="equiprow" id="equiprow$index" style="top:{$top}px;left:{$left}px" onmouseover="equip_menu_highlight('$index');" onmouseout="equip_menu_unhighlight('$index');" onclick="equip_menu_select('$index');">
        <img class="equippic" id="equippic$index" src="{$none_url}">
        <div class="equipitem" id="equipitem$index">.</div>
    </div>
EOD;
        }
    $canc_url="{$images}bigcanc.png";
    echo <<<EOD
    <img class="equipcanc" id="equipcanc" src="{$canc_url}" onmouseover="highlight_equip_menu_icon(this);" onmouseout="unhighlight_equip_menu_icon(this);" onclick="equip_menu_cancel();">
</div>
EOD;
    }
?>