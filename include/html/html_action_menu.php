<?php
function html_action_menu()
    {
    $images=MENU_IMAGES_DIR;
    $icon_options=array(
        "Attack with left weapon",
        "Attack with right weapon",
        "Use an Item",
        "Change weapon",
        "Use a skill",
        "Cast a spell",
        "Evade attack",
        "Run"
        );
    $icon_icons=array(
        "none.png",
        "none.png",
        "item.png",
        "no_fight.png",
        "skill.png",
        "spell.png",
        "defend.png",
        "none.png"
        );
    echo"<div class=\"menubox\" id=\"actionmenu\">\n";
    for ($index=0;$index<8;$index++)
        {
        //$img_url=ie_fix_image_url("$images{$icon_icons[$index]}");
        $img_url="$images{$icon_icons[$index]}";
        echo"<img id=\"action$index\" class=\"menuicon\" src=\"{$img_url}\" alt=\"$icon_options[$index]\" title=\"$icon_options[$index]\" style=\"top:".(42*($index>>2)+10)."px;left:".(35*($index&3)+10)."px\" onmouseover=\"highlight_action_menu_icon(this);\" onmouseout=\"unhighlight_action_menu_icon(this);\" onclick=\"action_menu_selection($index);\">\n";
        }
    echo"</div>\n";
    }
?>