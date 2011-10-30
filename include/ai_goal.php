<?php
function ai_goal(&$fighter,&$target,$command,$subcommand,$alignment,$effect,$is_bad)
    {
    #If this must work on someone who is alive and the target is dead, return 0.
    if(only_living_targets($effect)&&$target->is_dead())
        return 0;
    #Develop a weight
    $weight=((($alignment===true&&!$is_bad)||($alignment===false&&$is_bad))?1:-1);
    #Get the value and weight it
    switch($fighter->ai_goal)
        {
        case 0: //Random
            return ai_goal_random($fighter,$target,$command,$subcommand,$alignment,$effect,$is_bad)*$weight;
        case 1: //Destructor
            return ai_goal_destructor($fighter,$target,$command,$subcommand,$alignment,$effect,$is_bad)*$weight;
        case 2: //Schemer
            return ai_goal_schemer($fighter,$target,$command,$subcommand,$alignment,$effect,$is_bad)*$weight;
        case 3: //Preventor
            return ai_goal_preventor($fighter,$target,$command,$subcommand,$alignment,$effect,$is_bad)*$weight;
        case 4: //Protector
            return ai_goal_protector($fighter,$target,$command,$subcommand,$alignment,$effect,$is_bad)*$weight;
        }
    return null;
    }

function ai_goal_random(&$fighter,&$target,$command,$subcommand,$alignment,$effect,$is_bad)
    {
    return mt_rand(1,1000);
    }

function ai_goal_protector(&$fighter,&$target,$command,$subcommand,$alignment,$effect,$is_bad)
    {
    return $target->pxp*$target->get_current('HP');
    }

function ai_goal_destructor(&$fighter,&$target,$command,$subcommand,$alignment,$effect,$is_bad)
    {
    #Determine the value for this action against this target
    switch($command)
        {
        case 0: //Attack left hand
        case 1: //Attack right hand
            $value=ai_weigh_attack_damage($fighter,$target,$command);
            break;
        case 2: //Use item
            $item=&$GLOBALS['items'][$fighter->inventory[$subcommand]['item']];
            $value=ai_weigh_effect_damage($fighter,$target,$item->effect,$item->base,$item->added,$item->attribute);
            break;
        case 4: //Use skill
        case 5: //Cast spell
            $ability=&$GLOBALS['abilities'][$fighter->abilities[$subcommand]];
            $value=ai_weigh_effect_damage($fighter,$target,$ability->effect,$ability->base,$ability->added,$ability->attribute);
            break;
        case 3: //Equip item
        case 8: //Equip weapon and ammo
            //Maybe we should look at this as an option if equipping a better weapon
            //will provide better damage.
            return 0; //This option has a value of 0
        case 6: //Defend
            //Maybe we should look at this as an option if the opposition
            //is strong enough.
            return 0; //This option has a value of 0
        case 7: //Flee
            //Maybe we should look at this as an option if the opposition
            //is strong enough.
            return 0; //This option has a value of 0
        default:
            return null;
        }
    return $value;
    }

function ai_goal_schemer(&$fighter,&$target,$command,$subcommand,$alignment,$effect,$is_bad)
    {
    #Determine the value for this action against this target
    switch($command)
        {
        case 0: //Attack left hand
        case 1: //Attack right hand
            $value=ai_weigh_attack_damage($fighter,$target,$command)
                *ai_weigh_attack_success($fighter,$target,$command);
            break;
        case 2: //Use item
            $item=&$GLOBALS['items'][$fighter->inventory[$subcommand]['item']];
            $value=ai_weigh_effect_damage($fighter,$target,$item->effect,$item->base,$item->added,$item->attribute);
            break;
        case 4: //Use skill
            $ability=&$GLOBALS['abilities'][$fighter->abilities[$subcommand]];
            $value=ai_weigh_effect_damage($fighter,$target,$ability->effect,$ability->base,$ability->added,$ability->attribute)
                *ai_weigh_attack_success($fighter,$target,$command);
            break;
        case 5: //Cast spell
            $ability=&$GLOBALS['abilities'][$fighter->abilities[$subcommand]];
            $value=ai_weigh_effect_damage($fighter,$target,$ability->effect,$ability->base,$ability->added,$ability->attribute)
                *ai_weigh_cast_success($fighter,$target,$command);
            break;
        case 3: //Equip item
        case 8: //Equip weapon and ammo
            //Maybe we should look at this as an option if equipping a better weapon
            //will provide better damage.
            return 0; //This option has a value of 0
        case 6: //Defend
            //Maybe we should look at this as an option if the opposition
            //is strong enough.
            return 0; //This option has a value of 0
        case 7: //Flee
            //Maybe we should look at this as an option if the opposition
            //is strong enough.
            return 0; //This option has a value of 0
        default:
            return null;
        }
    return $value;
    }

function ai_goal_preventor(&$fighter,&$target,$command,$subcommand,$alignment,$effect,$is_bad)
    {
    $value=ai_goal_destructor($fighter,$target,$command,$subcommand,$alignment,$effect,$is_bad);
    $hp=$target->get_current('HP');
    if($hp==0)
        return 0;
    return floor($value/$hp*1000);
    }

function ai_weigh_attack_success(&$fighter,&$target,$command)
    {
    $acc=$fighter->get_current('Accuracy',$command);
    $dod=$fighter->get_perceived_stat($target,'Dodge');
    return $acc/($acc+$dod);
    }

function ai_weigh_attack_damage(&$fighter,&$target,$command)
    {
    $str=$fighter->get_current('Strength',$command);
    $blo=$fighter->get_perceived_stat($target,'Block');
    $dmg=($str-floor($blo/2));
    #Figure in elemental bonus
    //$dmg*=$GLOBALS['elemental_bonus'][$fighter->get_attack_element($command)][$target->get_defend_element()];
    //Minimum damage is 1.
    if($dmg<1)
        $dmg=1;
    return $dmg;
    }

function ai_weigh_effect_damage(&$fighter,&$target,$effect, $base, $added,$attribute)
    {
    switch($effect)
        {
        case 0: //Nothing
            return 0;
        case 1: //Heal
            if($target->get_current('HP')==0)
                return 0;
            //Find amount healed
            $amt=$added/2+$base;
            #Figure in elemental bonus
            //$amt*=$GLOBALS['elemental_bonus'][$attribute][$target->get_defend_element()];
            #NOTE:Always return positive.  The calling function handles negative action.
            return $amt;
        case 2: //Hurt
            if($target->get_current('HP')==0)
                return 0;
            //Find amount damaged
            $dmg=$added/2+$base;
            #Figure in elemental bonus
            //$dmg*=$GLOBALS['elemental_bonus'][$attribute][$target->get_defend_element()];
            return $dmg;
        case 3: //Revive
            if($target->get_current('HP')==0)
                return $target->get_current('HP')-$target->get_base('HP');
            $amt=round($target->get_base('HP')*400.0/$added);
            #Figure in elemental bonus
            //$amt*=$GLOBALS['elemental_bonus'][${heal attribute}][$target->get_defend_element()];
            #NOTE:Always return positive.  The calling function handles negative action.
            return $amt;
        case 4: //Slay
            break;
        case 5: //Increase Stats
            break;
        case 6: //Decrease Stats
            break;
        case 7: //Steal Stats
            break;
        case 8: //Cause Good Status
            break;
        case 9: //Remove Good Status
            break;
        case 10: //Cause Bad Status
            break;
        case 11: //Remove Bad Status
            break;
        case 12: //Restore MP
            if($target->get_current('HP')==0)
                return 0;
            //Find amount healed
            $amt=$added/2+$base;
            #NOTE:Always return positive.  The calling function handles negative action.
            return $amt*$amt;
        }
    return null;
    }

function ai_weigh_cast_success(&$fighter,&$target)
    {
    $pow=$fighter->get_current('Power');
    $res=$fighter->get_perceived_stat($target,'Resistance');
    $base=$pow+$res;
    return $pow/$base+min($pow,$res)/$base/2;
    }
?>