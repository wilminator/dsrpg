<?php
require_once INCLUDE_DIR.'html/html_common.php';
require_once INCLUDE_DIR.'html/html_loader.php';
require_once INCLUDE_DIR.'html/html_action_menu.php';
require_once INCLUDE_DIR.'html/html_list_menu.php';
require_once INCLUDE_DIR.'html/html_equip_menu.php';
require_once INCLUDE_DIR.'html/html_javascript.php';
require_once INCLUDE_DIR.'html/html_css.php';
require_once INCLUDE_DIR.'browser.php';

if(!function_exists('php_data_to_js'))
    {
    require_once INCLUDE_DIR.'js_rip.php';
    }

function html_commit()
    {
    echo <<<EOD
<div class="submit">
    <input class="commit" id="commit" type="submit" value="Commit" onclick="commit_commands();">
</div>
EOD;
    }

function html_target_cancel()
    {
    echo <<<EOD
<div class="cancel" id="cancel">
    <input class="cancel" type="submit" value="Cancel Target" onclick="target_cancel();">
</div>
EOD;
    }

function html_fight_message()
    {
    echo <<<EOD
<div id="fight_message" class="fight_message">&nbsp;</div>
EOD;
    }

function html_combat($fightid, &$fight, $player_party, $teamid)
    {
    //Prep the javascript and css stacks
    //Push the entire css directory.
    push_css_directory('',CSS_DIR);

    //Push the entire javascript directory.
    push_js_directory('',JAVASCRIPT_DIR);

    //Push the entire images/icon/menu directory.
    push_image_directory(MENU_IMAGES_DIR);
    //push_image_directory(ABILITY_IMAGES_DIR);
    //push_image_directory(ITEM_IMAGES_DIR);
    //push_image_directory(EFFECT_IMAGES_DIR);

    //Get the list of images
    $image_js=image_stack_to_js();

    //Import objects into js
    $itemsjs=$GLOBALS['items_js'];
    $abilitiesjs=$GLOBALS['abilities_js'];
    $jobsjs=$GLOBALS['jobs_js'];
    $personalitiesjs=$GLOBALS['personalities_js'];
    $equip_points_js=php_data_to_js(array_keys(get_equip_points()));

    //Output the HTML now
    echo <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>HTML RPG</title>
<script>
window.SM2_DEFER = true;
</script>
EOD;

    //display the css and script tags.
    html_css(CSS_DIR);
    html_js(JAVASCRIPT_DIR);
    echo "<script type=\"text/javascript\">\n";

    //output the generated script
    echo "
//public constants
var javadir='".JAVA_DIR."';
var swfdir='".SWF_DIR."';
var images='".IMAGES_DIR."';
var fighter_images='".FIGHTER_IMAGES_DIR."';
var menu_images='".MENU_IMAGES_DIR."';
var ability_images='".ABILITY_IMAGES_DIR."';
var item_images='".ITEM_IMAGES_DIR."';
var effect_images='".EFFECT_IMAGES_DIR."';
var background_images='".BACKGROUND_IMAGES_DIR."';
var sound='".SOUND_DIR."';
var music='".MUSIC_DIR."';

//Flying blood and damage data
var FLY_BOUND_Y=".FLY_BOUND_Y.";
var FLY_BOUND_X=".FLY_BOUND_X.";
var FLY_COUNT=".FLY_COUNT.";
var FLY_TIMEOUT=".FLY_TIMEOUT.";
var BLOOD_COUNT=".BLOOD_COUNT.";
var DAMAGE_COUNT=".DAMAGE_COUNT.";
";

    echo <<<EOD

window.SM2_DEFER = true;


//Audio variables
var sound_vol=100;
var sound_voices=16;
var music_vol=100;

//Equip menu options
var equip_points=$equip_points_js;

//Game objects
var items=$itemsjs;
var abilities=$abilitiesjs;
var jobs=$jobsjs;
var personalities=$personalitiesjs;
var player_party=$player_party;
var teamid=$teamid;
var fightid=$fightid;
var static_images=$image_js;


function init()
    {
    //Init game variables.
    init_game();
    
    //Init the audio
    init_audio(init2);
    }

function init2()
    {
    set_sound_volume(sound_vol);
    set_music_volume(music_vol);
    load_static_sounds();

	//Start the proxy
	if(init_tranceiver('processor.php',data_dispatch)==null)
        alert('Error starting the background tranceiver thread.');

  	return init_fight();
    }

function kill()
    {
	//Stop the proxy
	kill_tranceiver();
    //Alow the page to unload.
    return true;
    }
</script>
</head>
<body class="body" onload="init();" onunload="kill();">
EOD;
    html_loader();

    //Make an action menu for the heroes
    html_action_menu();

    //Make a list menu
    html_list_menu();

    //Make an equipment menu
    html_equip_menu();

    html_code();

    html_jukebox();

echo <<<EOD
<div id="fight" class="screen">
EOD;
    //Make a commit object
    //html_commit($sequence,$return_url);
    html_commit();

    //Make a target cancel object
    html_target_cancel();

    //Make a fight message object
    html_fight_message();

    //Close HTML page
    echo <<<EOD
    </div>
</body>
</html>
EOD;
    }
?>
