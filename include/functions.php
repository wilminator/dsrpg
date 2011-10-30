<?php
/**
 * function.php
 * global functions
 * @version $Id$
 * @copyright 2003
 **/

require_once INCLUDE_DIR.'ai.php';

require_once INCLUDE_DIR.'js_rip.php';


function randomize($value,$deviation=15)
    {
    return floor(mt_rand(100-$deviation,100+$deviation)*$value/100.0);
    }

function make_input($name,$objval,$attrs=null)
    {
    echo "<input name=\"$name\" value=\"{$objval}\"";
    if(is_array($attrs))
        foreach($attrs as $attr=>$value)
            echo " $attr=\"$value\"";
    echo ">\n";
    }

function make_textarea($name,$objval,$attrs=null)
    {
    echo "<textarea name=\"$name\"";
    if(is_array($attrs))
        foreach($attrs as $attr=>$value)
            echo " $attr=\"$value\"";
    echo ">{$objval}</textarea>";
    }

function make_select($name,$objval,&$source,$attrs=null)
    {
    echo "<select name=\"$name\"";
    if(is_array($attrs))
        foreach($attrs as $attr=>$value)
            echo " $attr=\"$value\"";
    echo ">\n";
    foreach($source as $value=>$tag)
        {
        $tag=htmlentities($tag);
        $value=htmlentities($value);
        echo "<option value=\"$value\"".($objval==$value?' selected':'').">$tag</option>\n";
        }
    echo "</select>\n";
    }

function make_checkbox($name,$checked,$attrs=null)
    {
    echo "<input type=\"checkbox\" name=\"$name\"";
    if($checked)
        echo " checked=\"checked\"";
    if(is_array($attrs))
        foreach($attrs as $attr=>$value)
            echo " $attr=\"$value\"";
    echo ">\n";
    }

function sum_n($n)
    {
    return $n*($n+1)/2;
    }

function count_ones($value)
    {
    $bit=1;
    $count=0;
    while($bit<=$value)
        if($value&$bit)
            $count++;
    return $count;
    }

function get_target_rating($targets)
    {
    switch($targets)
        {
        case -1: //Group
            return 3;
        case -2: //Party
            return 9;
        case -3: //All parties
        case -4: //All enemies
        case -5: //All allies
            return 27;
        default:
            return ($targets<3?$targets*.5+1:2.5);
        }
    }

function get_effect_rating($effect,$base,$added,$attribute)
    {
    switch($effect)
        {
        case 1://Heal
        case 2://Hurt
            return $base+$added/2;
        case 3://Revive
        case 4://Slay
            return $base*2.5+$added*2.5;
            break;
        case 5://Inc Stat
        case 6://Dec Stat
            return ($base+$added/2)*sqrt(count_ones($attribute)+1);
        case  7://Good Status Cause
        case  8://Good Status Remove
        case  9://Bad Status Cause
        case 10://Bad Status Remove
            return $base*$added;
        }
    return 0;
    }

function &make_hero($name,$job,$level,$personality,$inventory,$equipment,$charid=0)
    {
    $character=hero($name,$job,$level,$personality,$charid);
    foreach($inventory as $item)
       $character->add_item($item['item'],$item['qty']);
    foreach($equipment as $equipment)
       $character->equip_item ($equipment['slot'],$equipment['side']);
    if(!is_null($character->equipment['lhand']))
       $character->command=0;
    if(!is_null($character->equipment['rhand']))
       $character->command=1;
    return $character;
    }

function &make_hero_party($hero_array)
    {
    $groups=array();
    foreach($hero_array as $index=>$hero)
        {
        list($name,$job,$level,$personality,$group,$inventory,$equipment)=$hero;
        $character=make_hero($name,$job,$level,$personality,$inventory,$equipment,$index);
        if(!isset($groups[$group]))
            $groups[$group]=new GROUP;
        while($groups[$group]->add_hero($character))
            {$group=($group+1)%3;}
        }
    $party=new PARTY;
    foreach(array_keys($groups) as $index)
        $party->add($groups[$index]);
    return $party;
    }

function array_keys_strict($haystack,$needle)
    {
    $retval=array();
    foreach($haystack as $key=>$value)
        if($value===$needle)
            $retval[]=$key;
    return $retval;
    }

function perform_action(&$fighter,$pindex,$gindex,$cindex,&$target,$tpindex,$tgindex,$tcindex,$command,$intensity,$divisor,&$action_list)
    {
    $target->affected_by($fighter,$pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$command,$intensity,$divisor,$action_list);
    return true;
    }
    
function ability_sort($a,$b)
    {
    return $GLOBALS['abilities'][$a]->name>$GLOBALS['abilities'][$b]->name;
    }

function find_slot($slot,$side)
    {
    if(in_array($slot,array('hand','ammo','arm')))
        return ($side==0?'l':'r').$slot;
    return $slot;
    }
    
function halflife($start,$end)
    {
    if (is_null($end))
        $final = 2;
    else
        $final = 2 - pow(.5, $end - 1);
    if ($start < 1)
       return $final;
    $initial = 2 - pow(.5, $start - 1);
    return $final - $initial;
    }
    
function speed_multiplier($times)
   {
   return ($times + 1) / 2.0;
   }

function process_fight(&$fight,&$sequence,$players)
    {
    //Determine which parties have NPCs.
    //log_error("Player list:".var_export($players,true)." fight:".var_export($fight,true));
    $pc_teams=array();
    $player_as_monster=array();
    foreach($players as $player)
        if($player['teamid']>0)
            $pc_teams[]=$player['teamid'];
        else
            $player_as_monster[]=$player['player_party'];
    //log_error("Player list:".var_export($players,true)." pc teams:".var_export($pc_teams,true)." player monsters:".var_export($player_as_monster,true));

    //Give NPCs a job to do.
    foreach($fight->parties as $ptynumber=>$party)
        if(!in_array($ptynumber,$player_as_monster))
            {
            foreach($party->groups as $grpnumber=>$group)
                foreach($group->characters as $chrnumber=>$hero)
                    //If not dead and not a controlled team or part of a player-monster party, then process
                    if(!$fight->parties[$ptynumber]->groups[$grpnumber]->characters[$chrnumber]->is_dead()
                        && (is_null($fight->parties[$ptynumber]->groups[$grpnumber]->characters[$chrnumber]->teamid))
                            || !in_array($fight->parties[$ptynumber]->groups[$grpnumber]->characters[$chrnumber]->teamid,$pc_teams))
                        {
                        #Hey, let's try the AI!
                        $enemy=&$fight->parties[$ptynumber]->groups[$grpnumber]->characters[$chrnumber];
                        $mycmd=ai($fight,$ptynumber,$grpnumber,$chrnumber);
                        list($cmd,$use,$pty,$grp,$chr)=explode(',',$mycmd);
                        $enemy->command=$cmd;
                        $enemy->using=$use;
                        $enemy->target=array($pty,$grp,$chr);
                        //log_error("Ran AI for $ptynumber-$grpnumber-$chrnumber");
                        }
                    else
                        {
                        //log_error("Skipped AI for $ptynumber-$grpnumber-$chrnumber teamid=".var_export($fight->parties[$ptynumber]->groups[$grpnumber]->characters[$chrnumber]->teamid,true));
                        }
            }


    //generate a combat sequence to playback.
    $action_list=$fight->do_combat();
    $sequence++;
    return $action_list;
    }

function convert_action_list_to_playlist($action_list,$player_party,$base_object)
    {
    $playback_actions=array();

    $grouped_lists=preprocess_action_list($action_list);
    foreach($grouped_lists as $index=>$group)
        {
        $id_list=$group['id'];
        $message_list=$group['message'];
        $effect_list=$group['effect'];
        $event_list=$group['event'];

        //Take a peek to find out who our main actor is.
        $pindex=$id_list[0][1][0];
        $gindex=$id_list[0][1][1];
        $cindex=$id_list[0][1][2];
        //Get this actor's personality.
        log_error('issue here.');
        $char=$base_object->get_character("$pindex;$gindex;$cindex");
        $hero=$GLOBALS['personalities'][$char->personalityid];

        //Process the event list.
        foreach($event_list as $event_index=>$event)
            {
            $type=$event[0];
            $data=$event[1];
            switch($type)
                {
                case 'EquipSlot':
                    $item=$data[0];
                    $slot=$data[1];
                    $playback_actions[]=array(0,'equip_item',$pindex,$gindex,$cindex,$item,$slot);
                    break;
                case 'UnequipSlot':
                    $slot=$data[0];
                    $playback_actions[]=array(0,'unequip_item',$pindex,$gindex,$cindex,$slot);
                    break;
                case 'UseItem':
                    $item=$data[0];
                    $playback_actions[]=array(0,'consume_item',$pindex,$gindex,$cindex,$item,true);
                    break;
                case 'ExpendAmmo':
                    $ammo=$data[0];
                    $playback_actions[]=array(0,'consume_item',$pindex,$gindex,$cindex,$ammo,false);
                    break;
                case 'AlterStat':
                    $tpindex=$data[0];
                    $tgindex=$data[1];
                    $tcindex=$data[2];
                    $stat=$data[3];
                    $amount=$data[4];
                    $playback_actions[]=array(0,'alter_stat',$tpindex,$tgindex,$tcindex,$stat,$amount);
                    break;
                }
            }

        //Move main actor on screen and remove the Turn effect.
        $action=array_shift($id_list);
        $playback_actions[]=array(0,'present_group',$pindex,$gindex);
        //Next get the target.
        $action=array_shift($id_list);
        if($action)
            {
            $tpindex=$action[1][0];
            $tgindex=$action[1][1];
            $tcindex=$action[1][2];
            $range=$action[1][3];
            }
        //If there is an effect, then pull the group id for it.
        //Otherwise, just use 'Target'.
        if(count($effect_list)>0)
            {
            $effect=explode(';',$effect_list[0]['group']);
            $tpindex=$effect[0];
            $tgindex=$effect[1];
            }
        /*
        #!!IMPORTANT!!#
        Either this action affects the same or opposite sides.
        If the action effects the opposite sides,
            ($pindex==$player_party)^($tpindex==$player_party)
        then show the other team, delay, then do the action sequence.
        If not, then do the action sequence THEN call the party.
        */
        if(($pindex==$player_party)^($tpindex==$player_party))
            {
            $playback_actions[]=array(0,'present_group',$tpindex,$tgindex);
            $playback_actions[]=array(1);
            $seen=true;
            }
        else
            $seen=false;
        //Set the function call and target stack.
        $target_list=array();
        $impact_name='';
        $impact_data=null;
        //Do action sequence (the attack actions)
        foreach($message_list as $event_index=>$event)
            {
            $type=$event[0];
            $data=$event[1];
            switch($type)
                {
                //Starting actions (messages)
                case 'Attack':
                    $item=$GLOBALS['items'][$data[0]];
                    //alert(data[1].toString()+data[0]+'-'+data[2]);
                    //Call js that makes the actor attack
                    switch($item->fight_effect_type)
                        {
                        case 'close':
                            $playback_actions[]=array(0,'invoke_animation',$hero->attack_close_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->attack_close_data);
                            break;
                        case 'throw':
                            $playback_actions[]=array(0,'invoke_animation',$hero->attack_throw_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->attack_throw_data);
                            break;
                        case 'shoot':
                            $playback_actions[]=array(0,'invoke_animation',$hero->attack_shoot_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->attack_shoot_data);
                            break;
                        }
                    //Set the js that shows the impact of the attack (sword slashes, etc)
                    $impact_name=$item->fight_impact_animation;
                    $impact_data=$item->fight_impact_data;
                    break;
                case 'Item':
                    $item=$GLOBALS['items'][$data];
                    //Show title
                    $playback_actions[]=array(0,'set_fight_message',$item->name);
                    $playback_actions[]=array(0,'show_fight_message_by_hero',$pindex,$gindex,$cindex);
                    //Call the js that shows the actor using the item
                    $playback_actions[]=array(0,'invoke_animation',$hero->item_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->item_data);
                    //standard JS that causes the message to dissapear before continuing.
                    $playback_actions[]=array(1);
                    //Set the js that shows the impact of the item (bomb tossed, explosions, etc)
                    $impact_name=$item->use_impact_animation;
                    $impact_data=$item->use_impact_data;
                    break;
                case 'Spell':
                    $spell=$GLOBALS['abilities'][$data];
                    //Show title
                    $playback_actions[]=array(0,'set_fight_message',$spell->name);
                    $playback_actions[]=array(0,'show_fight_message_by_hero',$pindex,$gindex,$cindex);
                    //Call JS that makes the person cast a spell.
                    //Call the js that shows the actor casting the spell
                    $playback_actions[]=array(0,'invoke_animation',$hero->spell_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->spell_data);
                    //standard JS that causes the message to dissapear before continuing.
                    $playback_actions[]=array(1);
                    //Set the js that shows the impact of the spell
                    $impact_name=$spell->impact_animation;
                    $impact_data=$spell->impact_data;
                    break;
                case 'Skill':
                    $skill=$GLOBALS['abilities'][$data];
                    //Show title
                    $playback_actions[]=array(0,'set_fight_message',$skill->name);
                    $playback_actions[]=array(0,'show_fight_message_by_hero',$pindex,$gindex,$cindex);
                    //Call the js that shows the actor using the skill
                    $playback_actions[]=array(0,'invoke_animation',$hero->skill_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->skill_data);
                    //standard JS that causes the message to dissapear before continuing.
                    $playback_actions[]=array(1);
                    //Call js that makes the actor attack
                    switch($skill->skill_effect_type)
                        {
                        case 'close':
                            $playback_actions[]=array(0,'invoke_animation',$hero->attack_close_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->attack_close_data);
                            break;
                        case 'throw':
                            $playback_actions[]=array(0,'invoke_animation',$hero->attack_throw_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->attack_throw_data);
                            break;
                        case 'shoot':
                            $playback_actions[]=array(0,'invoke_animation',$hero->attack_shoot_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->attack_shoot_data);
                            break;
                        }
                    //Set the js that shows the impact of the skill
                    $impact_name=$skill->impact_animation;
                    $impact_data=$skill->impact_data;
                    break;
                case 'Equip':
                    $item=$data[0];
                    $ammo=$data[1];
                    $fight_message='Equipping '.$GLOBALS['items'][$item]->name;
                    if($ammo)
                        $fight_message.='and '.$GLOBALS['items'][$ammo]->name;
                    //Call the js that shows the actor changing equipment
                    $playback_actions[]=array(0,'invoke_animation',$hero->equip_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->equip_data);
                    //Should ba a part of personality -OR- a
                    //standard JS that uses a standard pic.
                    $playback_actions[]=array(0,'set_fight_message',$fight_message);
                    $playback_actions[]=array(0,'show_fight_message_by_hero',$pindex,$gindex,$cindex);
                    $playback_actions[]=array(1);
                    break;
                case 'Unequip':
                    $item=$data[0];
                    $ammo=$data[1];
                    $fight_message='Removing '.$GLOBALS['items'][$item];
                    //Call the js that shows the actor changing equipment
                    $playback_actions[]=array(0,'invoke_animation',$hero->equip_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->equip_data);
                    //Should ba a part of personality -OR- a
                    //standard JS that uses a standard pic.
                    $playback_actions[]=array(0,'set_fight_message',$fight_message);
                    $playback_actions[]=array(0,'show_fight_message_by_hero',$pindex,$gindex,$cindex);
                    $playback_actions[]=array(1);
                    break;
                case 'Defend':
                    break;
                case 'Run':
                    $playback_actions[]=array(0,'set_fight_message','Running Scared!');
                    $playback_actions[]=array(0,'show_fight_message_by_hero',$pindex,$gindex,$cindex);
                    //Call the js that shows the actor running
                    $playback_actions[]=array(0,'invoke_animation',$hero->flee_animation,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),0,array(),$hero->flee_data);
                    $playback_actions[]=array(1);
                    break;
                case 'Give':
                    $name=$data[0];
                    $playback_actions[]=array(0,'set_fight_message',$name);
                    $playback_actions[]=array(0,'show_fight_message_by_hero',$pindex,$gindex,$cindex);
                    $playback_actions[]=array(1);
                    break;
                case 'NoMP':
                    $playback_actions[]=array(0,'set_fight_message','Not enough MP');
                    $playback_actions[]=array(0,'show_fight_message_by_hero',$pindex,$gindex,$cindex);
                    $playback_actions[]=array(1);
                    break;
                }
            }
        foreach($effect_list as $event_index=>$effects)
            {
            $effect=explode(';',$effects['group']);
            $pty=$effect[0];
            $grp=$effect[1];
            if($tpindex!=$pty ||$tgindex!=$grp||$seen==false)
                {
                $playback_actions[]=array(0,'present_group',$pty,$grp);
                $playback_actions[]=array(1);
                $seen=true;
                }
            //process_effects($playback_actions,$effects['effects'],$pre_effect_js,$impact_js,$base_object,$range);
            //$playback_actions[]=array(0,impact_js,pindex,gindex,cindex,tpindex,tgindex,tcindex,range,effects['effects']]);
            //Call the js that shows the impact on the target(s)
            if($impact_data)
                $playback_actions[]=array(0,'invoke_animation',$impact_name,array('party'=>$pindex,'group'=>$gindex,'character'=>$cindex),array('party'=>$tpindex,'group'=>$tgindex,'character'=>$tcindex),$range,$effects['effects'],$impact_data);
            else
                error_log('No impact data');
            $playback_actions[]=array(1);
            }
        }
    //log_error("Storing playback actions.\n".php_data_to_js($playback_actions),100);
    return $playback_actions;
    }

function preprocess_action_list($action_list)
    {
    $retval=array();

    while(count($action_list)>0)
        {
        //Make a list of just one fighter's actions.
        $action_group=array();
        do {
            $action_group[]=array_shift($action_list);
            } while(count($action_list)>0 && $action_list[0][0]!="Turn");
        //Run thru the list again.  Now separate into lists of
        //Things that just need to be done and things that affect
        //others.
        $event_list=array();  //Lists things like ammo use, item loss, slot mods
        $effect_list=array(); //Lists things like damage, equipping
        $message_list=array();//Lists what is about to be done.
        $id_list=array();     //Lists who does the action and who is the primary target.
        foreach($action_group as $index=>$action)
            {
            switch($action[0])
                {
                case 'Attack':
                case 'Item':
                case 'Spell':
                case 'Skill':
                case 'Equip':
                case 'Unequip':
                case 'Defend':
                case 'Run':
                case 'NoMP':
                    $message_list[]=$action;
                    break;
                case 'Turn':
                case 'Target':
                    $id_list[]=$action;
                    break;
                case 'Miss':
                case 'Damage':
                case 'Restore':
                case 'Died':
                case 'NoEffect':
                    $effect_list[]=$action;
                    break;
                case 'EquipSlot':
                case 'UnequipSlot':
                case 'UseItem':
                case 'ExpendAmmo':
                case 'UseMP':
                case 'AlterStat':
                    $event_list[]=$action;
                    break;
                default:
                    log_error("Unknown action in action list. $action[0]");
                    break;
                }
            }
        //Sub-parse the effect list into groups
        $groups=array();
        foreach($effect_list as $index=>$effect)
            {
            $pty=$effect[1][0];
            $grp=$effect[1][1];
            $target=$pty.';'.$grp;
            if(!in_array($target,$groups))
                $groups[$target]=array();
            $groups[$target][]=$effect;
            }
        $effect_list=array();
        foreach($groups as $group=>$effects)
            $effect_list[]=array('group'=>$group,'effects'=>$effects);
        //Only bother if there are other events or effects.
        if(count($event_list)>0 || count($effect_list)>0)
            $retval[]=array('id'=>$id_list,'message'=>$message_list,'effect'=>$effect_list,'event'=>$event_list);
        }
    return $retval;
    }

function string_to_array($string,$height,$width)
    {
    $retval=array();
    $count=0;
    for($y=0;$y<$height;$y++)
        for($x=0;$x<$width;$x++)
            {
            $retval[$y][$x]=ord($string[$count]);
            $count++;
            }
    return $retval;
    }

function array_to_string($array,$height,$width)
    {
    $retval='';
    for($y=0;$y<$height;$y++)
        for($x=0;$x<$width;$x++)
            $retval.=chr($array[$y][$x]);
    return $retval;
    }
?>
