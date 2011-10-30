<?php
define ('INCLUDE_DIR','../include/');
require INCLUDE_DIR.'constants.php';
require INCLUDE_DIR.'paths.php';
?>
var javadir='<?php echo JAVA_DIR; ?>';
var swfdir='<?php echo SWF_DIR; ?>';
var images='<?php echo IMAGES_DIR; ?>';
var fighter_images='<?php echo FIGHTER_IMAGES_DIR; ?>';
var menu_images='<?php echo MENU_IMAGES_DIR; ?>';
var ability_images='<?php echo ABILITY_IMAGES_DIR; ?>';
var item_images='<?php echo ITEM_IMAGES_DIR; ?>';
var effect_images='<?php echo EFFECT_IMAGES_DIR; ?>';
var background_images='<?php echo BACKGROUND_IMAGES_DIR; ?>';
var sound='<?php echo SOUND_DIR; ?>';
var music='<?php echo MUSIC_DIR; ?>';

//Flying blood and damage data
var FLY_BOUND_Y=<?php echo FLY_BOUND_Y; ?>;
var FLY_BOUND_X=<?php echo FLY_BOUND_X; ?>;
var FLY_COUNT=<?php echo FLY_COUNT; ?>;
var FLY_TIMEOUT=<?php echo FLY_TIMEOUT; ?>;
var BLOOD_COUNT=<?php echo BLOOD_COUNT; ?>;
var DAMAGE_COUNT=<?php echo DAMAGE_COUNT; ?>;


//size of each tile
var tile_width=<?php echo TILE_WIDTH; ?>;
var tile_height=<?php echo TILE_HEIGHT; ?>;

//size of each minimap in tiles
var minimap_width=<?php echo MINIMAP_WIDTH; ?>;
var minimap_height=<?php echo MINIMAP_HEIGHT; ?>;

//Size of each submap in minimaps
var submap_width=<?php echo SUBMAP_WIDTH; ?>;
var submap_height=<?php echo SUBMAP_HEIGHT; ?>;

//Speed of refresh
var refresh_speed=<?php echo REFRESH_SPEED; ?>;
