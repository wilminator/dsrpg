<?php

/**
 * constants.php
 * global constants
 * @version $Id$
 * @copyright 2003
 **/

define ("FLY_BOUND_Y", 550);
define ("FLY_BOUND_X", 800);
define ("FLY_COUNT", 256);
define ("FLY_TIMEOUT", 100);
define ("BLOOD_COUNT", 128);
define ("DAMAGE_COUNT", 27);

define('GROUP_MAX_COUNT',4);

define('CHARACTER_MAX_ITEMS',12);
define('CHARACTER_MAX_ITEM_QTY',250);

#AI actions
$GLOBALS['ai_action']=array(
    0=>'Stupid',
    1=>'Normal',
    2=>'Healer',
    3=>'Protector',
    4=>'Pummeler',
    5=>'Fighter',
    6=>'Hinderer',
    7=>'Caster',
    8=>'Mage',
    9=>'Sharp',
   10=>'Smart',
   11=>'Omnipotent'
    );

#AI goal
$GLOBALS['ai_goal']=array(
    0=>'Random',
    1=>'Destructor',
    2=>'Schemer',
    3=>'Preventor',
    4=>'Protector'
    );

#AI target
$GLOBALS['ai_target']=array(
    0=>'Stupid',
    1=>'Vulture',
    2=>'Normal',
    3=>'Group',
    4=>'Smart',
    5=>'Team',
    6=>'Wise',
    7=>'Omnipotent'
    );

#AI Experience
$GLOBALS['ai_experience']=array(
    50=>'Recruit',
    40=>'Private',
    30=>'Sergeant',
    20=>'Lieutenant',
    10=>'Colonel',
    5=>'General',
    0=>'Omnipotent'
    );

$GLOBALS['character_stats']=array('HP','MP','Speed','Accuracy','Strength','Dodge','Block','Power','Resistance','Focus');
$GLOBALS['character_equipment']=array("rhand","rammo","rarm","lhand","lammo","larm","body","head","back","feet");
$GLOBALS['attributes']=array(
    0=>'Normal',
    1=>'Fire',
    2=>'Water',
    3=>'Earth',
    4=>'Wind',
    5=>'Electricity',
    6=>'Light',
    7=>'Kinetic',
    8=>'Magnetism',
    9=>'Freeze',
   10=>'Explosion',
   11=>'Magic',
   12=>'Holy',
   13=>'Apocalypse',
   14=>'Graviton'
    );
$GLOBALS['character_types']=array(
    0=>'None',
    1=>'Fire',
    2=>'Water',
    3=>'Earth',
    4=>'Wind',
    5=>'Electricity',
    6=>'Light',
    7=>'Kinetic',
    8=>'Magnetism',
    9=>'Flyer',
   10=>'Evil',
   11=>'Plant',
   12=>'Animal',
   13=>'Machine'
    );
//Range options
$GLOBALS['ranges']=array(
   -5=>'All parties',
   -4=>'All parties',
   -3=>'All parties',
   -2=>'One party',
   -1=>'One group',
    0=>'One character',
    1=>'One character plus 1 on either side',
    2=>'One character plus 2 on either side',
    3=>'One character plus 3 on either side',
    4=>'One character plus 4 on either side',
    5=>'One character plus 5 on either side'
    );
//Equipment location options
$GLOBALS['equip_loc']=array(
    ''                  =>"Not equippable",
    'lhand-rhand-rammo' =>"Two handed expendable",
    'lhand-rhand'       =>"Two handed",
    'hand-ammo'         =>"One handed expendable",
    'hand'              =>"One handed",
    'ammo'              =>"Ammunition for weapon",
    'body'              =>"Body (Torso & Legs)",
    'head'              =>"Helmet",
    'feet'              =>"Footwear",
    'back'              =>"Back (Cloak)",
    'arm'               =>"Arm (Gauntlet, Shield)",
    'lhand-lammo'       =>"Left handed expendable",
    'lhand'             =>"Left handed",
    'rhand-rammo'       =>"Right handed expendable",
    'rhand'             =>"Right handed"
    );
//use mode options
$GLOBALS['use_modes']=array(
    'false'=>'Unlimited uses',
    'true'=>'One use'
    );
//Ability type options
$GLOBALS['ability_types']=array(
    '0'=>'Spell',
    '1'=>'Skill'
    );

//Effect types and their variable names
$GLOBALS['effects']=array(
    0=>'Do Nothing',
    1=>'Heal',
    2=>'Damage',
    3=>'Revive',
    4=>'Slay',
    5=>'Increase Stats',
    6=>'Decrease Stats',
    7=>'Steal Stats',
    8=>'Cause Good Status',
    9=>'Remove Good Status',
   10=>'Cause Bad Status',
   11=>'Remove Bad Status',
   12=>'Restore MP'
    );

//Effect types and their variable names
$GLOBALS['effect_var_names']=array(
    0=>array('N/A'              ,'N/A'              ),
    1=>array('Base Healing'     ,'Random Healing'   ),
    2=>array('Base Damage'      ,'Random Damage'    ),
    3=>array('Revival %'        ,'% HP Recovered'   ),
    4=>array('Death %'          ,'% MHP Damage'     ),
    5=>array('N/A'              ,'N/A'              ),
    6=>array('N/A'              ,'N/A'              ),
    7=>array('N/A'              ,'N/A'              ),
    8=>array('N/A'              ,'N/A'              ),
    9=>array('N/A'              ,'N/A'              ),
   10=>array('N/A'              ,'N/A'              ),
   11=>array('N/A'              ,'N/A'              ),
   12=>array('N/A'              ,'N/A'              )
    );

//size of each map tile in pixels
define ('TILE_HEIGHT',64);
define ('TILE_WIDTH',64);

//size of each minimap in tiles
define ('MINIMAP_WIDTH',7);
define ('MINIMAP_HEIGHT',6);

//Size of each submap in minimaps
define ('SUBMAP_WIDTH',6);
define ('SUBMAP_HEIGHT',6);

//Speed of map refresh (in ms)
define ('REFRESH_SPEED',100);
?>
