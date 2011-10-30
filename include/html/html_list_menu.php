<?php
function html_list_menu()
    {
    $images=MENU_IMAGES_DIR;
    echo "<div class=\"menuwindow\" id=\"menuwindow\">\n";
    $none_url="{$images}bignone.png";
    for ($index=0;$index<8;$index++)
        {
        $top=$index*50;
        echo <<<EOD
    <div class="menurow" id="menurow$index" style="top:{$top}px" onmouseover="list_menu_highlight($index);" onmouseout="list_menu_unhighlight($index);" onclick="list_menu_select($index);">
        <img class="menupic" id="menupic$index" src="{$none_url}">
        <div class="menuitem" id="menuitem$index">.</div>
        <div class="menuqty" id="menuqty$index">.</div>
    </div>

EOD;
    	}
    $next_url="{$images}bignext.png";
    $last_url="{$images}biglast.png";
    $canc_url="{$images}bigcanc.png";
    echo <<<EOD
    <img class="menulast" id="menulast" src="{$last_url}" onmouseover="highlight_list_menu_icon(this);" onmouseout="unhighlight_list_menu_icon(this);" onclick="list_menu_last();">
    <img class="menunext" id="menunext" src="{$next_url}" onmouseover="highlight_list_menu_icon(this);" onmouseout="unhighlight_list_menu_icon(this);" onclick="list_menu_next();">
    <img class="menucanc" id="menucanc" src="{$canc_url}" onmouseover="highlight_list_menu_icon(this);" onmouseout="unhighlight_list_menu_icon(this);" onclick="list_menu_cancel();">
    <div class="menudesc" id="menudesc">.</div>
</div>
EOD;
    }
?>