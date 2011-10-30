<?php
require_once INCLUDE_DIR.'constants.php';

function do_effect($effect,$base,$added,$pindex,$gindex,$cindex,&$target,$tpindex,$tgindex,$tcindex,$intensity,$divisor,$immunity,&$action_list)
    {
    if($divisor>=0)
        $impact_percentage=($divisor+1-$intensity)/($divisor+1.0);
    else
        $impact_percentage=1;
    switch($effect)
        {
        case 0: //Nothing
            break;
        case 1: //Heal
            //echo "healing $impact_percentage";
            //log_error("Effect test");
            //var_dump($target);
            if($target->get_current('HP')==0)
                return false;
            //Find amount healed
            $amt=mt_rand(0,$added)+$base;
            $target->restore_HP($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$amt,$action_list);
            break;
        case 2: //Hurt
            if($target->get_current('HP')==0)
                return false;
            //Find amount damaged
            $dmg=floor((mt_rand(0,$added)+$base)*$immunity*$impact_percentage);
            //log_error("$added $base $immunity $impact_percentage $dmg",1);
            $target->inflict_damage($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$dmg,false,$action_list);
            break;
        case 3: //Revive
            if($target->get_current('HP')==0)
                {
                $target->restore_HP($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$target->get_base('HP'),$action_list);
                break;
                }
            //Here's the restore rub:
            //$base is the chance out of 100 to be revived.
            //$added is the % life added to the target when successful.

            //Check to see if we revive the target.
            $chance=mt_rand(1,100);
            if($chance<$base)
                {
                //Find amount healed
                $amt=round($target->get_base('HP')*100.0/$added);
                $target->restore_HP($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$amt,$action_list);
                }
            break;
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
                return false;
            //Find amount healed
            $amt=mt_rand(0,$added)+$base;
            $target->restore_MP($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$amt,$action_list);
            break;
        }
    return true;
    }

function is_effect_bad($effect)
    {
    /*
    Good effects
        0,  //Do Nothing
        1,  //Heal
        3,  //Revive
        5,  //Increase Stats
        8,  //Cause Good Status
        11, //Remove Bad Status
        12  //Restore MP
    */
    $bad_effects=array(
        2,  //Hurt
        4,  //Slay
        6,  //Decrease Stats
        7,  //Steal Stats
        9,  //Remove Good Status
        10  //Cause Bad Status
        );
    return in_array($effect,$bad_effects);
    }

function describe_effect($effect,$base,$added,$attribute)
    {
    global $attributes;

    switch($effect)
        {
        case 0: //Nothing
            return "Does nothing.";
        case 1: //Heal
            return "Heals between $base and ".($base+$added)." HP.";
        case 2: //Hurt
            return "Causes ".strtolower($attributes[$attribute])." damage between $base and ".($base+$added)." HP.";
        case 3: //Revive
            return "$base% chance to be revived to $added% HP. Restores 100% HP if alive.";
            break;
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
            return "Restores between $base and ".($base+$added)." MP.";
        }
    }
    
function only_living_targets($effect)
    {
    switch($effect)
        {
        case 1: //Heal
        case 2: //Hurt
        case 4: //Slay
        case 5: //Increase Stats
        case 6: //Decrease Stats
        case 7: //Steal Stats
        case 8: //Cause Good Status
        case 9: //Remove Good Status
        case 10: //Cause Bad Status
        case 11: //Remove Bad Status
        case 12: //Restore MP
            return true;
        }
    return false;
    }

function describe_range($range)
    {
    if($range==-2)
        return "Affects one party.";
    if($range==-1)
        return "Affects one group.";
    if($range==0)
        return "Affects one character.";
    return "Affects one character plus $range on either side, including dead characters.";
    }


function field_effect($effect)
    {
    switch($effect)
        {
        case 1: //Heal
        case 3: //Revive
        case 11: //Remove Bad Status
        case 12: //Restore MP
            return true;
        }
    return false;
    }

function combat_effect($effect)
    {
    switch($effect)
        {
        case 1: //Heal
        case 2: //Hurt
        case 3: //Revive
        case 4: //Slay
        case 5: //Increase Stats
        case 6: //Decrease Stats
        case 7: //Steal Stats
        case 8: //Cause Good Status
        case 9: //Remove Good Status
        case 10: //Cause Bad Status
        case 11: //Remove Bad Status
        case 12: //Restore MP
            return true;
        }
    return false;
    }

function get_max_effect($effect,$base,$added,$attribute)
    {
    global $attributes;

    switch($effect)
        {
        case 0: //Nothing
            return 0;
        case 1: //Heal
            return $base+$added;
        case 2: //Hurt
            return $base+$added;
        case 3: //Revive
            return 0;
        case 4: //Slay
            return 0;
        case 5: //Increase Stats
            return $base+$added;
        case 6: //Decrease Stats
            return $base+$added;
        case 7: //Steal Stats
            return $base+$added;
        case 8: //Cause Good Status
            return 0;
        case 9: //Remove Good Status
            return 0;
        case 10: //Cause Bad Status
            return 0;
        case 11: //Remove Bad Status
            return 0;
        case 12: //Restore MP
            return $base+$added;
        }
    }
?>