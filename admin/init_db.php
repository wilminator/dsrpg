<?php
#Do LOGIN stuff :)
require_once 'common_auth.php';
$userid=authenticate_permission_access('dse','RESET_DB','/auth/login.php','../');
redirect_on_hold($userid,'dse','on_hold.php','../../index.php');

define ('INCLUDE_DIR','../include/');

require_once INCLUDE_DIR.'job_store.php';
require_once INCLUDE_DIR.'monster_store.php';
require_once INCLUDE_DIR.'ability_store.php';
require_once INCLUDE_DIR.'personality_store.php';
require_once INCLUDE_DIR.'item_store.php';

if(isset($_GET['confirm']))
    {
    switch($_GET['confirm'])
        {
        case "YES":
        case "DEFAULT":
        case "FILE":
            //delete all tables in the databse
            echo 'Recreating items table.<br>';
            $item_store=new ITEM_STORE(true);
            //Add just the items if need be
            if($_GET['confirm']=="FILE")
                {
                $item_store=new ITEM_STORE;
                echo 'Importing old items into namespace.<br>';
                require_once INCLUDE_DIR.'items.php';
                echo 'Adding old items to table.<br>';
                foreach($GLOBALS['items'] as $index=>$item)
                    if($index!=0)
                        {
                        $item_store->set_item($item,$index);
                        //echo "Added item $index<br>";
                        }
                }
            echo 'Done with items table.<br>';
            //echo "Init'd items.<br>";
            $GLOBALS['items']=$item_store->get_all_items();
            echo 'Recreating abilities table.<br>';
            $ability_store=new ABILITY_STORE(true);
            //echo "Init'd abilities.<br>";
            echo 'Recreating jobs table.<br>';
            $job_store=new JOB_STORE(true);
            echo 'Recreating personalities table.<br>';
            $personality_store=new PERSONALITY_STORE(true);
            //echo "Init'd jobs.<br>";
            echo 'Recreating monsters table.<br>';
            $monster_store=new MONSTER_STORE(true);
            //echo "Init'd monsters.<br>";

            //Add everything else if need be.
            if($_GET['confirm']=="FILE")
                {
                $ability_store=new ABILITY_STORE;
                echo 'Importing old abilities into namespace.<br>';
                require_once INCLUDE_DIR.'abilities.php';
                echo 'Adding old abilities to table.<br>';
                foreach($GLOBALS['abilities'] as $index=>$ability)
                    {
                    if (! in_array($ability->skill_effect_type, array('close','throw','shoot','none'))) $ability->skill_effect_type = 'none';
                    $ability_store->set_ability($ability,$index);
                    //echo "Added ability $index<br>";
                    }

                //$job_store=new JOB_STORE;
                echo 'Importing old jobs into namespace.<br>';
                require_once INCLUDE_DIR.'jobs.php';
                echo 'Adding old jobs to table.<br>';
                foreach($GLOBALS['jobs'] as $index=>$job)
                    {
                    $job_store->set_job($job,$index);
                    //echo "Added job $index<br>";
                    }

                //$personality_store=new PERSONALITY_STORE;
                echo 'Importing old personalities into namespace.<br>';
                require_once INCLUDE_DIR.'personalities.php';
                echo 'Adding old personalities to table.<br>';
                foreach($personalities as $index=>$personality)
                    {
                    $personality_store->set_personality($personality,$index);
                    //echo "Added job $index<br>";
                    }

                $monster_store=new MONSTER_STORE;
                echo 'Importing old monsters into namespace.<br>';
                require_once INCLUDE_DIR.'monsters.php';
                echo 'Adding old monsters to table.<br>';
                foreach($GLOBALS['monsters'] as $index=>$monster)
                    {
                    $monster_store->set_monster($monster,$index);
                    //echo "Added monster $index<br>";
                    }
                }
            if ($_GET['confirm']=="DEFAULT")
                {
                //$ability_store=new ABILITY_STORE;
                echo 'Creating default abilities.<br>';
                $abilities=array();
/*
effect:
0=Do Nothing
1=Heal
2=Hurt

type:
0=Spell
1=Skill

range:
-2=entire party
-1=one group
0+=number of enemy spaces on either side.
*/
//                                         name                ,type,effect,base,added,attribute,MP used,range,description                                                                                                           , menu pic      , skill_effect_type, impact_animation         , impact_data
                $abilities[ 1]=new ABILITY("Electric Shockwave",   0,     2,  70,   55,        5,     17,   -1,"Causes an arc of electricity to run trough one group of enemies for about 100 HP."                                   ,'shockwave.png','none'            ,'static_impact'           ,array('images'=>array('lightening_storm.gif'),'sounds'=>array('electrocute3.mp3'),'times'=>array(1500,2500)));
                $abilities[ 2]=new ABILITY("Endless Wail"      ,   1,     2,  60,   30,        0,     37,   -2,"This agonizing wail torments all enemies, causing about 75 HP of damage."                                            ,'wail.png'     ,'none'            ,'static_impact'           ,array('images'=>array(''),'sounds'=>array('Cow.mp3'),'times'=>array(500,1000)));
                $abilities[ 3]=new ABILITY("Heal"              ,   0,     1,  25,   15,        0,      5,    0,"Heals one ally for about 30 HP."                                                                                     ,'heal.png'     ,'none'            ,'static_individual_impact',array('images'=>array('heal.gif'),'sounds'=>array('Holy2.mp3'),'times'=>array(700,900)));
                $abilities[ 4]=new ABILITY("Healsome"          ,   0,     1,  80,   60,        0,     12,    0,"Heals one ally for about 110 HP."                                                                                    ,'healsome.png' ,'none'            ,'static_individual_impact',array('images'=>array('heal.gif'),'sounds'=>array('Holy2.mp3'),'times'=>array(700,900)));
                $abilities[ 5]=new ABILITY("Life"              ,   0,     1,  20,   15,        0,     14,   -1,"Heals one group for about 25 HP."                                                                                    ,'life.png'     ,'none'            ,'static_individual_impact',array('images'=>array('heal.gif'),'sounds'=>array('Holy8.mp3'),'times'=>array(700,900)));
                $abilities[ 6]=new ABILITY("Fireball"          ,   0,     2,  30,   20,        1,      6,    0,"Throws a small ball of fire causing about 40 HP damage to one enemy."                                                ,'fireball.png' ,'none'            ,'ranged_arc_impact'       ,array('images'=>array('fireball.png','explosion.gif','small_fireball.png'),'sounds'=>array('Flame4.mp3','Flame1.mp3'),'times'=>array(15,1000,0.25,30,50)));
                $abilities[ 7]=new ABILITY("Firebomb"          ,   0,     2,  30,   15,        1,     10,    1,"A single ball of fire explodes causing about 35 HP damage up to three enemies."                                      ,'firebomb.png' ,'none'            ,'ranged_arc_impact'       ,array('images'=>array('fireball.png','explosion.gif','small_fireball.png'),'sounds'=>array('Flame4.mp3','Flame1.mp3'),'times'=>array(15,1000,0.25,30,70)));
                $abilities[ 8]=new ABILITY("Fire Nuke"         ,   0,     2,  80,   40,        1,     32,   -2,"A huge fireball explodes in the center of the target party, causing about 100 HP damage to each person in the party.",'firenuke.png' ,'none'            ,'slide_right_impact'      ,array('images'=>array('huge_fireball.png'),'sounds'=>array('Flame7.mp3'),'times'=>array(2000,25)));
                $abilities[ 9]=new ABILITY("Fireballs"         ,   0,     2,  45,   30,        1,     20,   -1,"Many fireballs leap from the caster's fingers causing about 60 HP damage to the target group."                       ,'fireball.png' ,'none'            ,'multi_impact'            ,array('images'=>array('small_fireball.png','explosion.gif',''),'sounds'=>array('Flame4.mp3','Flame1.mp3'),'times'=>array(15,500,50,'')));
                $abilities[10]=new ABILITY("Hot-dukin'"        ,   1,     2,  30,   50,        1,      9,    4,"Street fighters eat your hearts out! Many fireballs pummel a target plus four on either side for about 55 HP."       ,'fireball.png' ,'throw'           ,'multi_impact'            ,array('images'=>array('small_fireball.png','explosion.gif',''),'sounds'=>array('Flame2.mp3',''),'times'=>array(30,1000,70,1)));

                foreach($abilities as $index=>$ability)
                    $ability_store->set_ability($ability,$index);

                echo 'Creating default items.<br>';
                $items=array();
/*
equip_type: array of locations that need to be equiped
equip_type array values:
hand,arm,ammo,body,head,back,feet
$name,$price,$effect,$use_targets,$base,$added,$attribute,
        $one_use,$equip_type,$statinc,$statpercinc,$targets,$atkcnt,
        $attack_attribute,$ammo_type,$description,
        $menu_pic,$fight_effect_type,$fight_impact_animation,$fight_impact_data,$use_impact_animation,$use_impact_data
//                                  name               ,price ,use effect,use targets,base ,added,attribute,one_use,equip locations             ,stat increase                                 ,stat percent increase                   ,targets,attack count,atk_attr,ammo_type           ,description                                                                , menu_pic     , fight_effect_type, fight_impact_animation   , fight_impact_data                                                                                                                              , use_impact_animation,$use_impact_data
//                                                                                                                                               HP ,MP ,Acc,Str,Dod,Blo,Spd,Pow,Res,Foc        HP ,MP ,Acc, Str,Dod,Blo,Spd,Pow,Res,Foc
*/
                $items[ 1]=new ITEM("Laser Lance"      ,   225,         0,         -2,    0,    0,        0,false  ,array('lhand','rhand'),array(  0,  0, 25, 20,  0,  0, -5,  0,  0,  0),array(  0,  0,  0,   0,  0,  0,  0,  0,  0,  0),      0,           1,       0,''                 ,"A typical lance with a laser as the blade."                               ,'sword.png'   ,'close'           ,'static_impact'           ,array('images'=>array('slash.gif'),'sounds'=>array('blobs_slash.mp3'),'times'=>array(100,300))                                                  ,'static_impact'    ,array('images'=>array(''),'sounds'=>array(''),'times'=>array('','')));
                $items[ 2]=new ITEM("Spark Rod"        ,   125,         0,         -2,    0,    0,        0,false  ,array('hand')         ,array(  0,  0, 10, 13,  0,  0, -3,  0,  0,  0),array(  0,  0,  0,   0,  0,  0,  0,  0,  0,  0),      0,           1,       0,''                 ,"A rod that electrocutes whatever it hits.  A magician's cattle prod."     ,'sword.png'   ,'close'           ,'static_individual_impact',array('images'=>array('explosion.gif'),'sounds'=>array('blobs_punch.mp3'),'times'=>array(0,500))                                                ,'static_impact'    ,array('images'=>array(''),'sounds'=>array(''),'times'=>array('','')));
                $items[ 3]=new ITEM("Wraith Touch"     ,  2000,         0,         -2,    0,    0,        0,false  ,array('hand')         ,array(  0,  0,  4,  5,  0,  0, 25,  0,  0,  0),array(  0,  0,  1,   2,  0,  0,  0,  0,  0,  0),      0,           1,       0,''                 ,"The touch of a banchee that causes whither and decay."                    ,'sword.png'   ,'shoot'           ,'static_impact'           ,array('images'=>array('slash.gif'),'sounds'=>array('Sword3.mp3'),'times'=>array(100,300))                                                       ,'static_impact'    ,array('images'=>array(''),'sounds'=>array(''),'times'=>array('','')));
                $items[ 4]=new ITEM("M16A2 Rifle"      ,  6200,         0,         -2,    0,    0,        0,false  ,array('lhand','rhand'),array(  0,  0, 50,  0,  0,  0, 20,  0,  0,  0),array(  0,  0,  0,-100,  0,  0,  0,  0,  0,  0),      0,           3,       0,'5.62mm Round'     ,"The standard issue weapon for the USMC. Uses 5.62mm rounds as ammunition.",'m16rifle.png','throw'           ,'ranged_arc_impact'       ,array('images'=>array('blood.png','',''),'sounds'=>array('Blow2.mp3',''),'times'=>array(25,100,0,30,1))                                         ,'static_impact'    ,array('images'=>array(''),'sounds'=>array(''),'times'=>array('','')));
                $items[ 5]=new ITEM("Small Bomb"       ,    25,         0,         -2,    0,    0,        0,false  ,array('hand','ammo')  ,array(  0,  0, 15,150,  0,  0,-20,  0,  0,  0),array(  0,  0,  0,-100,  0,  0,  0,  0,  0,  0),      1,           1,       0,''                 ,"A small bomb made from household materials."                              ,'bigbomb.png' ,'close'           ,'ranged_arc_impact'       ,array('images'=>array('bomb.gif','explosion.gif','explosion.gif'),'sounds'=>array('Flame4.mp3','fireball.mp3'),'times'=>array(15,100,0.1,30,70)),'ranged_arc_impact',array('images'=>array('bomb.gif','explosion.gif','explosion.gif'),'sounds'=>array('Flame4.mp3','fireball.mp3'),'times'=>array(15,100,0.1,15,70)));
                $items[ 6]=new ITEM("5.62mm Ball Round",     3,         0,         -2,    0,    0,        0,false  ,array('ammo')         ,array(  0,  0,  0,300,  0,  0,  0,  0,  0,  0),array(  0,  0,  0,   0,  0,  0,  0,  0,  0,  0),      0,           1,       0,'5.62mm Round'     ,"5.62mm standard anti-infantry rounds."                                    ,'556round.png','close'           ,'ranged_arc_impact'       ,array('images'=>array('blood.png','',''),'sounds'=>array('',''),'times'=>array(1,100,0,30,1))                                                   ,'ranged_arc_impact',array('images'=>array('','',''),'sounds'=>array('',''),'times'=>array('','','','','')));
                $items[ 7]=new ITEM("Medical Kit"      ,    15,         1,          0,   50,   20,        0,true   ,array()               ,array(  0,  0,  0,  0,  0,  0,  0,  0,  0,  0),array(  0,  0,  0,   0,  0,  0,  0,  0,  0,  0),      0,           0,       0,''                 ,"A first aid kit capable of restoring about 60 HP."                        ,'medkit.png'  ,'close'           ,'static_impact'           ,array('images'=>array(''),'sounds'=>array(''),'times'=>array('',''))                                                                            ,'static_impact'    ,array('images'=>array('heal.gif'),'sounds'=>array('Item2.mp3'),'times'=>array(100,300)));
                $items[ 8]=new ITEM("9mm Baretta"      ,  3500,         0,         -2,    0,    0,        0,false  ,array('hand')         ,array(  0,  0, 20,  0,  0,  0, 40,  0,  0,  0),array(  0,  0,  0,-100,  0,  0,  0,  0,  0,  0),      0,           4,       0,'9mm Round'        ,"Standard issue pistol for the USMC.  Light and easy to use."              ,'fire.png'    ,'shoot'           ,'ranged_arc_impact'       ,array('images'=>array('blood.png','',''),'sounds'=>array('Blow3.mp3',''),'times'=>array(45,100,0,30,1))                                         ,'ranged_arc_impact',array('images'=>array('','',''),'sounds'=>array('',''),'times'=>array('','','','','')));
                $items[ 9]=new ITEM("9mm Bullet"       ,     2,         0,         -2,    0,    0,        0,false  ,array('ammo')         ,array(  0,  0,  0,200,  0,  0,  0,  0,  0,  0),array(  0,  0,  0,   0,  0,  0,  0,  0,  0,  0),      0,           1,       0,'9mm Round'        ,"Normal 9mm bullet."                                                       ,'bignone.png' ,'close'           ,'ranged_arc_impact'       ,array('images'=>array('','',''),'sounds'=>array('','',''),'times'=>array(1,100,0.0,30,1))                                                       ,'ranged_arc_impact',array('images'=>array(''),'sounds'=>array(''),'times'=>array('')));
                $items[10]=new ITEM("Kabar Knife"      ,   525,         0,         -2,    0,    0,        0,false  ,array('hand')         ,array(  0,  0, 15, 35,  0,  0, 20,  0,  0,  0),array(  0,  0,  0,   0,  0,  0,  0,  0,  0,  0),      0,           1,       0,''                 ,"A sharp, trusty hunter's knife used to gut monsters."                     ,'fight.png'   ,'close'           ,'ranged_arc_impact'       ,array('images'=>array('','',''),'sounds'=>array('','',''),'times'=>array(1,100,0.0,30,1))                                                       ,'ranged_arc_impact',array('images'=>array(''),'sounds'=>array(''),'times'=>array('')));

                foreach($items as $index=>$item)
                    $item_store->set_item($item,$index);

                echo 'Creating default personalities.<br>';
                $personalities=array();
/*
        $name,
        $base_animation,$base_data,
        $equip_animation,$equip_data,
        $flee_animation,$flee_data,
        $hit_animation,$hit_data,
        $die_animation,$die_data,
        $attack_close_animation,$attack_close_data,
        $attack_throw_animation,$attack_throw_data,
        $attack_shoot_animation,$attack_shoot_data,
        $skill_animation,$skill_data,
        $spell_animation,$spell_data,
        $item_animation,$item_data,
        $overworld_stand_images,$overworld_move_images
*/
                //heroes
                $personalities[0]=new PERSONALITY('Hahn',
                    'base_animation',array('images'=>array('hahn.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('hahn.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('hahn.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('hahn.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('hahn.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('hahn.png','hahn_attack.gif','hahn.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('hahn_attack.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('hahn_attack.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('hahn_cast.gif'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('hahn_cast.gif'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('hahn_cast.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));
                $personalities[1]=new PERSONALITY('Rolf',
                    'base_animation',array('images'=>array('rolf.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('rolf.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('rolf.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('rolf.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('rolf.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('rolf.png','rolf_attack.gif','rolf.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('rolf_attack.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('rolf_attack.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('rolf_cast.gif'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('rolf_cast.gif'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('rolf_cast.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));
                $personalities[2]=new PERSONALITY('Nei',
                    'base_animation',array('images'=>array('nei.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('nei.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('nei.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('nei.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('nei.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('nei.png','nei_attack.gif','nei.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('nei_attack.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('nei_attack.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('nei_cast.gif'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('nei_cast.gif'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('nei_cast.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));
                $personalities[3]=new PERSONALITY('Alys',
                    'base_animation',array('images'=>array('alys.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('alys.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('alys.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('alys.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('alys.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('alys.png','alys_attack.gif','alys.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('alys_attack.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('alys_attack.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('alys_cast.gif'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('alys_cast.gif'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('alys_cast.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));
                $personalities[4]=new PERSONALITY('Chaz',
                    'base_animation',array('images'=>array('chaz.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('chaz.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('chaz.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('chaz.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('chaz.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('chaz.png','chaz_attack.gif','chaz.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('chaz_attack.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('chaz_attack.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('chaz_cast.gif'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('chaz_cast.gif'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('chaz_cast.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));

                //$monsters
                $personalities[1000]=new PERSONALITY('Troll',
                    'base_animation',array('images'=>array('woodman.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('woodman.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('woodman.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('woodman.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('woodman.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('woodman.png','woodman.gif','woodman.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('woodman.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('woodman.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('woodman.gif'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('woodman.gif'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('woodman.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));
                $personalities[1001]=new PERSONALITY('Goblin',
                    'base_animation',array('images'=>array('goblin.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('goblin.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('goblin.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('goblin.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('goblin.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('goblin.png','goblin.gif','goblin.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('goblin.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('goblin.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('goblin.gif'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('goblin.gif'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('goblin.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));
                $personalities[1002]=new PERSONALITY('Gremlin',
                    'base_animation',array('images'=>array('gremlin.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('gremlin.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('gremlin.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('gremlin.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('gremlin.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('gremlin.png','gremlin.gif','gremlin.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('gremlin.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('gremlin.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('gremlin.gif'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('gremlin.gif'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('gremlin.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));
                $personalities[1003]=new PERSONALITY('Hippo',
                    'base_animation',array('images'=>array('hungry_hippo.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('hungry_hippo.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('hungry_hippo.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('hungry_hippo.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('hungry_hippo.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('hungry_hippo.png','hungry_hippo.gif','hungry_hippo.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('hungry_hippo.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('hungry_hippo.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('hungry_hippo.gif'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('hungry_hippo.gif'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('hungry_hippo.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));
                $personalities[1004]=new PERSONALITY('Jordie',
                    'base_animation',array('images'=>array('jordie.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('jordie.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('jordie.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('jordie.png'),'sounds'=>array('jordie_squeak.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('jordie_die.gif'),'sounds'=>array('Earth3.mp3','jordie_shreak.mp3'),'times'=>array(700,500,5)),
                    'attack_close_animation',array('images'=>array('jordie.png','jordie_ani.gif','jordie.png'),'sounds'=>array('','jordie_hey.mp3',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('jordie_ani.gif'),'sounds'=>array('jordie_hey.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('jordie_ani.gif'),'sounds'=>array('jordie_hey.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('jordie.png'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('jordie.png'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('jordie.png'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));
                $personalities[1005]=new PERSONALITY('Q Bird',
                    'base_animation',array('images'=>array('qbird.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('qbird.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('qbird.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('qbird.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('qbird.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('qbird.png','qbird_head.gif','qbird.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('qbird_head.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('qbird_head.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('qbird_head.gif'),'sounds'=>array('qbird_peeka_boo_echo.mp3'),'times'=>array(1000,500)),
                    'pose_animation',array('images'=>array('qbird_head.gif'),'sounds'=>array('qbird_peeka_boo_echo.mp3'),'times'=>array(1000,500)),
                    'pose_animation',array('images'=>array('qbird_head.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));
                $personalities[1006]=new PERSONALITY('Crystal',
                    'base_animation',array('images'=>array('crystal_boom.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('crystal_boom.png'),'sounds'=>array(),'times'=>array(500,500)),
                    'flee_animation',array('images'=>array('crystal_boom.png'),'sounds'=>array(),'times'=>array(0)),
                    'pose_animation',array('images'=>array('crystal_boom.png'),'sounds'=>array('Earth3.mp3'),'times'=>array(500,500)),
                    'die_animation' ,array('images'=>array('crystal_boom.png'),'sounds'=>array('Earth3.mp3','die.mp3'),'times'=>array(500,10,5)),
                    'attack_close_animation',array('images'=>array('crystal_boom.png','crystal_boom_head.gif','crystal_boom.png'),'sounds'=>array('','',''),'times'=>array(25,500,500)),
                    'pose_animation',array('images'=>array('crystal_boom_head.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('crystal_boom_head.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('crystal_boom_head.gif'),'sounds'=>array('skill.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('crystal_boom_head.gif'),'sounds'=>array('cast_2.mp3'),'times'=>array(500,500)),
                    'pose_animation',array('images'=>array('crystal_boom_head.gif'),'sounds'=>array(),'times'=>array(500,500)),
                    array('up'=>'','down'=>'','left'=>'','right'=>''),
                    array('up'=>'','down'=>'','left'=>'','right'=>''));

                    foreach($personalities as $index=>$personality)
                        $personality_store->set_personality($personality,$index);

                echo 'Creating default monsters.<br>';
/*
$name,
$stats,
$abilities,
$items,
$equipment,
$gold,
$personalityid,$ai_action,$ai_goal,$ai_target,$ai_experience
*/
                $monsters=array();
                $monsters[1]=new MONSTER('Troll',
                    array(125,  0, 90, 70, 40, 35, 40,  0, 90,  0),
                    array(),
                    array(),
                    array(),
                    50, 
                    1000    ,0  ,0  ,0  ,50);
                $monsters[2]=new MONSTER('Goblin',
                    array( 75,  0, 80, 60,100, 50,100,  0, 70,  0),
                    array(),
                    array(),
                    array(),
                    20,
                    1001    ,0  ,0  ,0  ,50);
                $monsters[3]=new MONSTER('Gremlin',
                    array( 50,  0,100, 45, 80, 80, 80,  0, 80,  0),
                    array(),
                    array(),
                    array(),
                    15,
                    1002    ,0  ,0  ,0  ,50);
                $monsters[4]=new MONSTER('Hippo',
                    array(150,  0, 60,120, 60,100, 60,  0, 40,  0),
                    array(),
                    array(),
                    array(),
                    70,
                    1003    ,0  ,0  ,0  ,50);
                foreach($monsters as $index=>$monster)
                    $monster_store->set_monster($monster,$index);

                echo 'Creating default jobs.<br>';
                $jobs=array();
//'HP','MP','Accuracy','Strength','Dodge','Block','Speed','Power','Resistance','Focus'
                $jobs[1]=new JOB('Inventor',100,
                    array(10.98, 4.97, 8.00, 7.00, 8.20, 6.50, 8.00, 7.00, 8.25, 5.00),
                    array(
                        array('ability'=> 3,'level'=> 3),
                        array('ability'=> 6,'level'=> 4),
                        array('ability'=> 5,'level'=> 7),
                        array('ability'=> 4,'level'=> 9),
                        array('ability'=> 7,'level'=>11)
                        ));
                $jobs[2]=new JOB('Technomancer',100,
                    array( 7.58, 9.87, 6.00, 6.00, 7.00, 6.50, 7.00,10.00, 9.75,10.15),
                    array(
                        array('ability'=> 1,'level'=> 4),
                        array('ability'=> 7,'level'=> 7)
                        ));
                $jobs[3]=new JOB('Banshee',100,
                    array(15.56, 6.00, 7.00, 5.00, 8.75, 6.75,10.00, 9.30,10.13, 8.50),
                    array(
                        array('ability'=> 2,'level'=>13)
                        ));
                $jobs[4]=new JOB('Marine',100,
                    array(13.57, 0.00,10.00, 8.00, 7.50, 8.50, 9.00, 0.00, 6.50, 0.00),
                    array(
                        ));
                $jobs[5]=new JOB('Bouncer',100,
                    array(19.89, 0.00, 9.00,11.00, 6.50, 9.00, 6.00, 0.00, 6.00, 0.00),
                    array(
                        ));
                foreach($jobs as $index=>$job)
                    $job_store->set_job($job,$index);
                }
            echo 'Done.<br>';
            echo "<a href=\"./\">Return to the previous menu.</a>";
            exit;
        }
    }
?>
<table>
  <tr><th>
    <table>
      <caption><b>Are you sure you want to reinitialize the database?</b></caption>
      <tr>
        <td><a href="init_db.php?confirm=YES">Yes, start all over.</a></td>
        <td><a href="init_db.php?confirm=DEFAULT">Yes, but create some default objects.</a></td>
        <td><a href="init_db.php?confirm=FILE">Yes, but use the existing variable files.</a></td>
        <td><a href="./">Do not touch a thing!</a></td>
      </tr>
    </table>
  </th></tr>
</table>

