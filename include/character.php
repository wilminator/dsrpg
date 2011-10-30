<?php
require_once INCLUDE_DIR.'personality.php';
require_once INCLUDE_DIR.'effects.php';

require_once INCLUDE_DIR.'errorlog.php';

require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'paths.php';
require_once INCLUDE_DIR.'functions.php';
require_once INCLUDE_DIR.'array.php';
require_once INCLUDE_DIR.'job.php';

require_once INCLUDE_DIR.'js_rip.php';

class CHARACTER{
    //character data
    var $charid;
    var $teamid;
    var $name;
    var $jobid;
    var $level;
    var $exp;
    var $need;
    var $current;
    var $base;
    var $inventory;
    var $equipment;
    var $abilities;
    var $pxp;
    var $personalityid;

    //Fight data
    var $xp_debts;
    var $old_exp;

    //For monsters only
    var $gold;
    var $ai_action;
    var $ai_goal;
    var $ai_target;
    var $ai_experience;

    //gameplay data
    var $command;
    var $using;
    var $target;

    //gui data
    var $personality;

    function CHARACTER()
        {
        $this->base=create_array($GLOBALS['character_stats'],array(),0);
        $this->current=create_array($GLOBALS['character_stats'],array(),0);
        $this->equipment=create_array($GLOBALS['character_equipment'],array(),null);
        $this->abilities=array();
        $this->inventory=array();
        $this->command=0;
        $this->using=0;
        $this->target=array(1,0,0);
        $this->charid=null;
        $this->teamid=null;
        $this->calculate_pxp();
        $this->personalityid=null;
        $this->gold=0;
        $this->ai_action=0; //Stupid
        $this->ai_goal=0; //Random
        $this->ai_target=0; //No filtering
        $this->ai_experience=50; //50% stat skewing
        $this->xp_debts=array(); //We owe nobody xp yet.
        $this->old_exp=0; //This gets set when the party joins a fight.
        }

    function make_leveled_hero($name,$jobid,$level,$personalityid,$charid=null)
        {
        $this->charid=$charid;
        $this->name=$name;
        $this->jobid=$jobid;
        $this->personalityid=$personalityid;

        if(array_key_exists($this->jobid,$GLOBALS['jobs']))
            {
            $job=&$GLOBALS['jobs'][$this->jobid];
            $job->don($this);
            while($this->level<$level)
                {
                $this->exp=$this->need;
                $this->level();
                }
            $this->current=$this->base;

            $this->personality=&$GLOBALS['personalities'][$personalityid];
            $this->calculate_pxp();
            }
        else
            {
            log_error("Trying to make a character with unknown job {$this->jobid}.");
            }
        }

    function make_hero($charid,$name,$jobid,$level,$exp,$need,$base,$current,$abilities,$inventory,$equipment,$personalityid)
        {
        $this->charid=$charid;
        $this->name=$name;
        $this->level=$level;
        $this->jobid=$jobid;
        $this->exp=$exp;
        $this->need=$need;
        $this->personalityid=$personalityid;

        $this->base=create_array($GLOBALS['character_stats'],$base,0);
        $this->current=create_array($GLOBALS['character_stats'],$current,0);
        $this->equipment=array_merge(create_array($GLOBALS['character_equipment'],array(),null),$equipment);

        $this->inventory=$inventory;
        $this->abilities=$abilities;
        uasort($this->abilities,'ability_sort');
        //$this->personality=&$GLOBALS['personalities'][$personalityid];
        $this->calculate_pxp();
        }

    function make_monster($name,$base,$current,$abilities,$personalityid,$gold,$ai_action,$ai_goal,$ai_target,$ai_experience)
        {
        $this->name=$name;
        $this->level=-1;
        $this->jobid=null;
        $this->personalityid=$personalityid;

        $this->base=$base;
        $this->current=$current;
        $this->equipment=create_array($GLOBALS['character_equipment'],array(),null);

        $this->inventory=array();
        $this->abilities=$abilities;
        $this->personality=&$GLOBALS['personalities'][$personalityid];
        $this->calculate_pxp();
        $this->gold=$gold;
        $this->ai_action=$ai_action;
        $this->ai_goal=$ai_goal;
        $this->ai_target=$ai_target;
        $this->ai_experience=$ai_experience;

        //Sort the list so abilities are alphabetic
        uasort($this->abilities,'ability_sort');
       }

    function regenerate_stats() //WARNING- dangerous
        {
        //Recreate this character's stats and abilities.
        //Leave items and exp alone.
        //Level as needed.

        //Reset stats
        $this->base=create_array($GLOBALS['character_stats'],array(),0);
        $this->current=create_array($GLOBALS['character_stats'],array(),0);

        //Remove abilities.
        $this->abilities=array();
        
        //Keep old exp.
        $exp=$this->exp;
        
        //Reset to level 0.
        $this->level=0;

        //Get this job (re)started.
        $job=&$GLOBALS['jobs'][$this->jobid];
        $job->don($this);

        //Reset the old exp.
        $this->exp=$exp;

        //Level this character as needed.
        $retval=$this->level();
        
        //Max all stats.
        $this->reset_stats(true);

        //return the leveling results.
        return $retval;
        }

    function calculate_pxp()
        {
        //This function finds the current Potential eXperience Points
        //of this character.  Equipment but not status effects are
        //taken into acount.

        //First collect default values:
        //Hit Point Max
        $hp=$this->get_base('HP',true);
        //Magic Point Max
        $mp=$this->get_base('MP',true);
        //Accuracy
        $ac=$this->get_base('Accuracy',true);
        if($ac<1) $ac=1;
        //Dodge
        $do=$this->get_base('Dodge',true);
        if($do<1) $do=1;
        //Strength
        $st=$this->get_base('Strength',true);
        if($st<1) $st=1;
        //Block
        $bl=$this->get_base('Block',true);
        if($bl<1) $bl=1;
        //Speed
        $sp=$this->get_base('Speed',true);
        if($sp<1) $sp=1;
        //Power
        $po=$this->get_base('Power',true);
        if($po<1) $po=1;
        //Resistance
        $re=$this->get_base('Resistance',true);
        if($re<1) $re=1;
        //Focus
        $fo=$this->get_base('Focus',true);
        if($fo<1) $fo=1;

        //Part 1: Physical Defense
        //(Dodge*Block*HP)^(1/3)
        $pdef=round(pow($do,1.0/3)*pow($bl,1.0/3)*pow($hp,1.0/3));

        //Part 2: Physical Offense
        //(Speed*Accuracy*Offense)^(1/3)
        //Offense=Strength*Targets*Times
        $weapons = array();
        #Precalc all the stats
        for($index = 0; $index < 3; $index++)
            {
            # Get the weapon and ammo count
            if ($index<2)
                {
                $weapon = $this->get_equipped_weapon($index);
                $ammo_count = $this->get_ammo_count($index);
                }
            else
                {
                $weapon = $GLOBALS['items'][0];
                $ammo_count = null;
                }
            # If the weapon is null, then skip.
            if (is_null($weapon))
                continue;
            $name = $weapon->name;
            # Get the number of attacks per turn.
            $times=$weapon->attack_count;
            # If the weapon has ammo, divide by the number of attacks per turn and round up.
            if (!is_null($ammo_count))
                $ammo_count = ceil($ammo_count / $times);
            #Weapon rating based on targets
            $targets=get_target_rating($weapon->targets);
            #Get this weapon's strength
            $wst=$this->get_base('Strength',$index);
            if($wst<1) $wst=1;
            #Get this weapon's speed
            $wsp=$this->get_base('Speed',$index);
            if($wsp<1) $wsp=1;
            #Modify the speed based on the number of attacks per turn.
            $wsp *= speed_multiplier($times);
            #Get this weapon's accuracy
            $wac=$this->get_base('Accuracy',$index);
            if($wac<1) $wac=1;
            #Store stats
            $weapons[$index] = array($times, $ammo_count, $times, $targets, $wsp, $wac, $wst, $name);
            }
        #Now score the offensive power
        $offense = array();
        for($index = 0; $index < 2; $index++)
            {
            if (!isset($weapons[$index]))
                continue;
            list($times, $ammo_count, $times, $targets, $wsp, $wac, $wst, $name) = $weapons[$index];
            $turn = $ammo_count;
            # Calculate the offense multiplier (based on the number of turns that may use this attack.)
            $omultiplier = halflife(0, $turn);
            //Compute attack damage
            $total_damage = $targets * $wst;
            $o = pow($wsp,1.0/3)*pow($wac,1.0/3)*pow($total_damage,1.0/3) * $omultiplier;
            //echo "<br>o1:$o<br>\n";
            # Check for no ammo condition
            if (is_null($turn))
                {
                $offense[] = $o;
                continue;
                }
            # Use alternate weapon, if possible
            if (!isset($weapons[1 - $index]))
                continue;
            list($times, $ammo_count, $times, $targets, $wsp, $wac, $wst, $name) = $weapons[1 - $index];
            #If ammo_count is a number, then add turn to it.
            if (!is_null($ammo_count))
                $ammo_count += $turn;
            # Calculate the offense multiplier (based on the number of turns that may use this attack.)
            $omultiplier = halflife($turn, $ammo_count);
            $turn = $ammo_count;
            //Compute attack damage
            $total_damage = $targets * $wst;
            $o += pow($wsp,1.0/3)*pow($wac,1.0/3)*pow($total_damage,1.0/3) * $omultiplier;
            //echo "<br>o2:$o<br>\n";
            # Check again for no ammo condition
            if (is_null($turn))
                {
                $offense[] = $o;
                continue;
                }
            # Use final weapon if needed: Nothing
            list($times, $ammo_count, $times, $targets, $wsp, $wac, $wst, $name) = $weapons[2];
            # Calculate the offense multiplier (based on the number of turns that may use this attack.)
            $omultiplier = halflife($turn, $ammo_count);
            //Compute attack damage
            $total_damage = $targets * $wst;
            $o += pow($wsp,1.0/3)*pow($wac,1.0/3)*pow($total_damage,1.0/3) * $omultiplier;
            //echo "<br>o3:$o<br>\n";
            $offense[] = $o;
            }
        //var_dump($weapons);
        //var_dump($offense);
        $poff=round(array_sum($offense) / count($offense));

        //Part 3: Magical Defense
        //(Resistance*HP)^(1/2)
        $mdef=round(pow($re,.5)*pow($hp,.5));

        //Part 4: Magical Offense
        //(Magic Use*Speed*Focus*Power)^(1/4)
        //OR (Magic Use*Speed*Accuracy)^(1/3)
        //AVERAGED.
        //Magic Use=pow(sum(floor(MPM/MP Used)*spell rating),(1/3))
        $spells=0;
        $skills=0;
        foreach($this->abilities as $index)
            {
            $ability=$GLOBALS['abilities'][$index];
            $times=floor($mp>=$ability->mp_used?1:0);
            //Spell rating based on targets
            $targets=get_target_rating($ability->targets);
            //Spell rating based on effect
            $rating=get_effect_rating($ability->effect,$ability->base,$ability->added,$ability->attribute);
            //Compute ability usefulness
            $muse=$times*sqrt($targets*$rating);
            //Calculate ability score
            if($ability->type==0)
                $spells+=$muse;
            else
                $skills+=$muse;
            }
        $spells=round(pow($spells,.25)*pow($sp,.25)*pow($fo,.25)*pow($po,.25));
        $skills=round(pow($skills,1.0/3)*pow($sp,1.0/3)*pow($ac,1.0/3));
        $moff=$spells+$skills;
        //$moff=round(array_sum($score)/max(1,count($score)));

        //PXP=pdef+poff+mdef+moff
        $this->pxp=$pdef+$poff+$mdef+$moff;

        return $this->pxp;
        }

    function level()
        {
        $retval=array('leveled'=>false,'data'=>array());
        if($this->level==100)
            $this->exp=$this->need;
        while($this->exp>=$this->need && $this->level<100)
            {
            $this->need+=round($GLOBALS['jobs'][$this->jobid]->need/2.0*((($this->level+3)*($this->level+2)/2)-1));
            $result=$GLOBALS['jobs'][$this->jobid]->level($this);
            $retval['leveled']=true;
            $retval['data'][]="{$this->name} has reached level {$this->level}!";
            $retval['data']=array_merge($retval['data'],$result);
            }
        $this->calculate_pxp();
        return $retval;
        }

    function add_item($item,$qty)
        {
       $temp=array();
        foreach($this->inventory as $key=>$value)
            $temp[$key]=$value['item'];
        $keys=array_keys($temp,$item);
        foreach($keys as $index)
            if($this->inventory[$index]['qty']<CHARACTER_MAX_ITEM_QTY
                && (count($GLOBALS['items'][$item]->equip_type)==0
                || count(array_intersect($GLOBALS['items'][$item]->equip_type,array('ammo','lammo','rammo')))>0))
                {
                if($this->inventory[$index]['qty']+$qty<=CHARACTER_MAX_ITEM_QTY)
                    {
                    $this->inventory[$index]['qty']+=$qty;
                    return 0;
                    }
                else
                    {
                    $qty-=CHARACTER_MAX_ITEM_QTY-$this->inventory[$index]['qty'];
                    $this->inventory[$index]['qty']=CHARACTER_MAX_ITEM_QTY;
                    }
                }
        if(count($this->inventory)+1<=CHARACTER_MAX_ITEMS)
            {
            array_push($this->inventory,array('item'=>$item,'qty'=>$qty));
            return 0;
            }
        return $qty;
        }

    function remove_item($index,$qty)
        {
        if($index<count($this->inventory))
            {
            if($this->inventory[$index]['qty']>$qty)
                $this->inventory[$index]['qty']-=$qty;
            else
                {
                //Remove item
                $qty=$this->inventory[$index]['qty'];
                array_splice($this->inventory,$index,1);
                $this->inventory=array_values($this->inventory);
                //Fix equipment.
                foreach($this->equipment as $key=>$value)
                    if ($value>$index)
                        $this->equipment[$key]--;
                    elseif($value==$index)
                        $this->equipment[$key]=null;
                $this->calculate_pxp();
                }
            return $qty;
            }
        return 0;
        }

    function add_ability($ability)
        {
        if(!in_array($ability,$this->abilities))
            {
            array_push($this->abilities,$ability);
            uasort($this->abilities,'ability_sort');
            return true;
            }
        return false;
        }

    function &get_item($index)
        {
        if(!isset($this->inventory[$index]))
            return true;
        return $GLOBALS['items'][$this->inventory[$index]['item']];
        }

    function get_item_qty($index)
        {
        if(!isset($this->inventory[$index]))
            return true;
        return $this->inventory[$index]['qty'];
        }

    function equip_item($index,$side)
        {
        if(($item=$this->get_item($index))===true)
            return true;
        if(count($item->equip_type)>0)
            {
            $retval=array();
            $equip_type=$item->equip_type;
            //Check to see if all slots that are needed are empty.
            foreach($equip_type as $loc)
                {
                $fix_side=find_slot($loc,$side);
                if(is_null($this->equipment[$fix_side])===false)
                    return $this->equipment[$fix_side];
                }
            //Check to see if this item is already equipped.
            foreach($this->equipment as $loc=>$value)
                {
                if($value===$index && !in_array($loc,$equip_type))
                    return $index;
                }
            //If this is ammo only, check to see if there is a
            //weapon that takes this ammo already equipped.
            if(count($equip_type)==1&&$equip_type[0]=='ammo')
                {
                $fix_side=find_slot('hand',$side);
                $eitem=$this->get_item($this->equipment[$fix_side]);
                if(is_null($this->equipment[$fix_side])
                    || $item->ammo_type!=$eitem->ammo_type)
                    return true;
                //A quick side correction for ammo in a two handed weapon
                if(count(array_intersect(array('lhand','rhand'),$eitem->equip_type))==2)
                    $side=0;
                }
            //Equip this weapon.
            foreach($item->equip_type as $loc)
                {
                $slot=find_slot($loc,$side);
                $this->equipment[$slot]=$index;
                $retval[]=$slot;
                }
            $this->calculate_pxp();
            return $retval;
            }
        return false;
        }

    function unequip_item($index)
        {
        if(($item=$this->get_item($index))===true)
            return true;
        if(count($item->equip_type)>0)
            {
            $side='';
            $retval=array();
            foreach($this->equipment as $loc=>$value)
                {
                if($value==$index)
                    {
                    if (strpos($loc,'hand')>=0)
                        {
                        if($side=='')
                            $side=substr($loc,0,1);
                        else
                            $side='r';
                        }
                    $this->equipment[$loc]=null;
                    $retval[]=$loc;
                    }
                }
            if ($side!=''
                && $item->ammo_type!=''
                && count(array_intersect(array('ammo','lammo','rammo'),$item->equip_type))==0)
                {
                $this->equipment[$side.'ammo']=null;
                $retval[]=$side.'ammo';
                }
            $this->calculate_pxp();
            return $retval;
            }
        return false;
        }

    function get_equipped_item($slot,$side)
        {
        $fix_slot = find_slot($slot, $side);
        $index = $this->equipment[$fix_slot];
        if (is_null($index))
           return null;
        return $this->get_item($index);
        }

    function get_equipped_weapon($side)
        {
        $weapon = $this->get_equipped_item('hand', $side);
        if (is_null($weapon))
           return $GLOBALS['items'][0];
        if ($side == 1 && $this->equipment['lhand'] === $this->equipment['rhand'])
           return null;
        return $weapon;
        }

    function get_equipped_ammo($side)
        {
        $ammo = $this->get_equipped_item('ammo', $side);
        if (!is_null($ammo) && $this->equipment[find_slot('hand', $side)] === $this->equipment[find_slot('ammo', $side)])
           return null;
        return $ammo;
        }

    function get_ammo_count($side)
        {
        $fix_slot = find_slot('ammo', $side);
        $index = $this->equipment[$fix_slot];
        if (is_null($index))
           return null;
        return $this->get_item_qty($index);
        }

    function get_equipment_bonus($stat,$base,$all_equipment=false)
        {
        $command=(is_numeric($all_equipment)?$all_equipment:$this->command);
        $equipment=array();
        foreach($this->equipment as $slot=>$iindex)
            if(!is_null($iindex) && (
                $all_equipment===true
                ||!in_array($stat,array('Strength','Accuracy','Speed'))
                || ($command==0 && !in_array($slot,array('rammo','rhand')))
                || ($command==1 && !in_array($slot,array('lammo','lhand')))
                || ($command>1 && !in_array($slot,array('lammo','lhand','rammo','rhand')))))
                $equipment[$iindex]=$this->get_item($iindex);
        $percinc=0;
        foreach($equipment as $item)
            $percinc+=$item->statpercinc[$stat];
        $base*=($percinc/100.0+1.0);
        foreach($equipment as $item)
            $base+=$item->statinc[$stat];
        return (int)round($base);
        }

    function get_base($stat,$all_equipment=false)
        {
        $base=$this->base[$stat];
        if($all_equipment!==false || in_array($stat,array('HP','MP')))
            $base=$this->get_equipment_bonus($stat,$base,$all_equipment);
        return $base;
        }

    function get_current($stat,$all_equipment=false)
        {
        $base=$this->current[$stat];
        //$GLOBALS['output'].= "{$this->name} $stat: $base";
        if(!in_array($stat,array('HP','MP')))
            {
            $base=$this->get_equipment_bonus($stat,$base,$all_equipment);
            //$GLOBALS['output'].= "->$base";
            }
        //$GLOBALS['output'].= "<br>";
        return $base;
        }

    function reset_stats($all=false)
        {
        foreach($this->base as $stat=>$value)
            if($all===true || !in_array($stat,array('HP','MP')))
                $this->current[$stat]=$value;
        }

    function normalize_stats($all=false)
        {
        foreach($this->current as $stat=>$value)
            if($all===true || !in_array($stat,array('HP','MP')))
                {
                $diff=($value-$this->base[$stat])/10;
                if($diff>0)
                    $this->current[$stat]+=ceil($diff);
                else
                    $this->current[$stat]+=floor($diff);
                }
        }

    function find_target_range($weapon,$ammo)
        {

        if(is_null($weapon))
            return $GLOBALS['items'][0]->targets;
        if(is_null($ammo))
            return $GLOBALS['items'][$this->inventory[$weapon]['item']]->targets;
        return $GLOBALS['items'][$this->inventory[$ammo]['item']]->targets;
        }


    function to_js()
        {
        return php_data_to_js(get_object_vars($this));
        }


    function expend_ammo($pindex,$gindex,$cindex,$side,&$action_list)
        {
        //Return true means do attack, false means need ammo.
        //0 means left, 1 means right.
        if ($side===0)
            $side='l';
        else
            $side='r';
        $wpn=$this->equipment[$side."hand"];
        if(is_null($wpn))
            {
            //There is no weapon.
            return true;
            }
        $ammo=$this->equipment[$side."ammo"];
        $witem=$this->get_item($wpn);
        if($witem->ammo_type=='')
            {
            //Doesn't need ammo.  Check to see that the wpn is not the ammo.
            if(is_null($ammo))
                {
                return true;
                }
            }
        //This wpn is expendable or uses ammo.  If ammo is not present, then fail.
        if(is_null($ammo))
            {
            $GLOBALS['output'].= "Needs ammo.";
            return false;
            }
        //If the ammo qty is >0, then decrement and return true
        if($this->inventory[$ammo]['qty']>0)
            {
            $this->remove_item($ammo,1);
            //$actions[]=array(0,'consume_item',$pindex,$gindex,$cindex,$ammo,false);
            //$action_list[]=array('ExpendAmmo',array($ammo));
            return true;
            }
        //Otherwise we are out of ammo.
        return false;
        }


    function validate_action()
        {
        switch($this->command)
            {
            case 0: //Left hand attack
                //If there is something in the left hand and it's the
                //same as the right, then we can't attack with the right.
                if(!is_null($this->equipment['lhand']) &&
                    $this->equipment['rhand']==$this->equipment['lhand'])
                    return false;
            case 1: //Right hand attack
                break;
            case 2: //Use item
                if(!isset($this->inventory[$this->using]['item']))
                    return false;
                break;
            case 4: //Use skill
            case 5: //Cast spell
                if(!isset($this->abilities[$this->using]))
                    return false;
                break;
            case 8: //Change equipment and ammo
                if(!isset($this->inventory[$this->target[0]]['item']))
                    return false;
            case 3: //Change equipment
                if(!isset($this->inventory[$this->using]['item']))
                    return false;
                break;
            case 6: //Defend
            case 7: //Run
                break; //Do not do anything.
            }
        //Confirm action
        return true;
        }


    function validate_target(&$fight,$range,$effect)
        {
        switch($this->command)
            {
            case 3: //Change equipment
            case 8: //Change equipment
            case 7: //Run
            case 6: //Defend
                return false; //This action does not require a target, anything is good.
            }
        list($tpindex,$tgindex,$tcindex)=$this->target;
        //$GLOBALS['output'].="Original target: $tpindex,$tgindex,$tcindex with range $range<br>";
        if(!only_living_targets($effect))
            return false;
        switch($range)
            {
            //If it is all parties, then we can act.
            case -3:
                return false;
            //If it is party, verify our party is still alive.
            case -2:
                //NOTE: later we will detect party alignment and try
                //to recast on another party of the same alignment as the
                //intended target.  For now, don't cast if the
                //target party is dead.
                //$GLOBALS['output'].='Can the party be attacked?<br>';
                //Verify the party exists.
                if(array_key_exists($tpindex,$fight->parties)==false)
                    return true;
                return $fight->parties[$tpindex]->is_dead();
            //If it is group, verify that the group is still alive.
            case -1:
                //Verify the party exists.
                if(array_key_exists($tpindex,$fight->parties)==false)
                    return true;
                //Return false if the target is still alive.
                //$GLOBALS['output'].='Can the group be attacked?<br>';
                //Verify that the group exists and the group is alive.
                if (array_key_exists($tgindex,$fight->parties[$tpindex]->groups) &&
                    !$fight->parties[$tpindex]->groups[$tgindex]->is_dead())
                    return false;
                //$GLOBALS['output'].='No. Is the party dead?<br>';
                //OK, is the party dead?
                if ($fight->parties[$tpindex]->is_dead())
                    return true;
                //There is at least one group still alive.
                //Randomly pick one.
                $list=array();
                foreach(array_keys($fight->parties[$tpindex]->groups) as $index)
                    if(!$fight->parties[$tpindex]->groups[$index]->is_dead())
                        $list[]=$index;
                $tgindex=$list[array_rand($list)];
                $this->target=array($tpindex,$tgindex,$tcindex);
                //$GLOBALS['output'].="No. Chosing random group $tgindex.<br>";
                return false;
            //It is a ranged attack.
            default:
                //Verify the party exists.
                if(array_key_exists($tpindex,$fight->parties)==false)
                    return true;
                //Verify the group exists.
                if (array_key_exists($tgindex,$fight->parties[$tpindex]->groups)==false)
                    return true;
                //See if our range is dead.
                //$GLOBALS['output'].='Can the range be attacked?<br>';
                $dead=true;
                for($index=-$range;$index<=$range;$index++)
                    if (isset($fight->parties[$tpindex]->groups[$tgindex]->characters[$index+$tcindex]))
                        $dead=($dead&&$fight->parties[$tpindex]->groups[$tgindex]->characters[$index+$tcindex]->is_dead());
                if($dead==false)
                    return false;
                //OK, our range is dead.  Is there anyone else in the group?
                //If not, then we can't act.
                //$GLOBALS['output'].='No. Is the group dead?<br>';
                if($fight->parties[$tpindex]->groups[$tgindex]->is_dead())
                    return true;
                //Find a random target.
                $list=array();
                foreach(array_keys($fight->parties[$tpindex]->groups[$tgindex]->characters) as $index)
                    if(!$fight->parties[$tpindex]->groups[$tgindex]->characters[$index]->is_dead())
                        $list[]=$index;
                $tcindex=$list[array_rand($list)];
                $this->target=array($tpindex,$tgindex,$tcindex);
                //$GLOBALS['output'].="No. Chosing random character $tcindex.<br>";
                return false;
            }
        return true;
        }

    //This function causes a skill to be used by this character on the specified target
    function use_skill($ability,$tpindex,$tgindex,$tcindex,&$base_object,&$action_list)
        {
        //if($fighter->is_paralyzed())
        //  {
        //  $action_list[]=array('Paralyzed.',array());
        //  This action may not be performed.
        //  return;
        //  }
        $ability=$fighter->abilities[$fighter->using];
        $range=$GLOBALS['abilities'][$ability]->targets;
        $action_list[]=array('Skill',array($ability,$GLOBALS['abilities'][$ability]->name));
        if ($fighter->get_current('MP')<$GLOBALS['abilities'][$ability]->mp_used)
            {
            $may_affect=false;
            $action_list[]=array('NoMP',array($pindex,$gindex,$cindex));
            }
        else
            {
            //$GLOBALS['output'].= "MP reduced by {$GLOBALS['abilities'][$ability]->mp_used}.<br>";
            $action_list[]=array('AlterStat',array($pindex,$gindex,$cindex,'MP',-$GLOBALS['abilities'][$ability]->mp_used));
            $fighter->current['MP']-=$GLOBALS['abilities'][$ability]->mp_used;
            if (is_a($base_object,'FIGHT'))
                fight_do_affect('do_skill',$base_object,$range,$tpindex,$tgindex,$tcindex,$action_list);
            field_do_affect('do_skill',$base_object,$range,$tgindex,$tcindex,$action_list);
            }
        }

    //This function causes a spell to be used by this character on the specified target
    function use_spell($ability,$tpindex,$tgindex,$tcindex,&$base_object,&$action_list)
        {
        $ability=$fighter->abilities[$fighter->using];
        $range=$GLOBALS['abilities'][$ability]->targets;
        $action_list[]=array('Spell',array($ability,$GLOBALS['abilities'][$ability]->name));
        if ($fighter->get_current('MP')<$GLOBALS['abilities'][$ability]->mp_used)
            {
            $may_affect=false;
            $action_list[]=array('NoMP',array($pindex,$gindex,$cindex));
            }
        else
            {
            //$GLOBALS['output'].= "MP reduced by {$GLOBALS['abilities'][$ability]->mp_used}.<br>";
            $action_list[]=array('AlterStat',array($pindex,$gindex,$cindex,'MP',-$GLOBALS['abilities'][$ability]->mp_used));
            $fighter->current['MP']-=$GLOBALS['abilities'][$ability]->mp_used;
            //if($fighter->is_spell_blocked())
            //  {
            //  $action_list[]=array('Spell Contained.',array());
            //  This action may not be performed.
            //  return;
            //  }
            if (is_a($base_object,'FIGHT'))
                fight_do_affect('do_skill',$base_object,$range,$tpindex,$tgindex,$tcindex,$action_list);
            field_do_affect('do_skill',$base_object,$range,$tgindex,$tcindex,$action_list);
            }
        }

    //This function causes an item to be used by this character on the specified target
    function use_item($ability,$tpindex,$tgindex,$tcindex,&$base_object,&$action_list)
        {
        //if($fighter->is_paralyzed())
        //  {
        //  $action_list[]=array('Paralyzed.',array());
        //  This action may not be performed.
        //  return;
        //  }
        $item=$fighter->inventory[$fighter->using]['item'];
        $range=$GLOBALS['items'][$item]->use_targets;
        $fight_message=$GLOBALS['items'][$item]->name;
        $action_list[]=array('Item',array($item,$GLOBALS['items'][$item]->name));
        if (is_a($base_object,'FIGHT'))
            $this->fight_do_affect('do_skill',$base_object,$range,$tpindex,$tgindex,$tcindex,$action_list);
        $this->field_do_affect('do_skill',$base_object,$range,$tgindex,$tcindex,$action_list);
        if($GLOBALS['items'][$fighter->inventory[$fighter->using]['item']]->one_use)
            {
            $fighter->remove_item($fighter->using,1);
            $action_list[]=array('UseItem',array($fighter->using));
            }
        }

    function fight_do_affect($callback,&$fight,$range,$tpindex,$tgindex,$tcindex,&$action_list)
        {
        $key=$fight->get_character_key($this);
        if(!$key)
            $key="-1;-1;-1";
        list($pindex,$gindex,$cindex)=explode(';',$key);
        //First we handle party and group scenarios.
        switch($range)
            {
            //If it is all parties, then cycle through parties.
            case -3:
                //$GLOBALS['output'].= "Fight wide effect.<br>";
                $fight->affect_all_parties($callback,$this,$this->command,$pindex,$gindex,$cindex,$action_list);
                break;
            //If it is party, then cycle through groups.
            case -2:
                //$GLOBALS['output'].= "Party wide effect.<br>";
                $fight->affect_party($callback,$this,$this->command,$pindex,$gindex,$cindex,$tpindex,$action_list);
                break;
            //If it is group, then cycle through characters.
            case -1:
                //$GLOBALS['output'].= "Group wide effect.<br>";
                $fight->affect_group($callback,$this,$this->command,$pindex,$gindex,$cindex,$tpindex,$tgindex,$action_list);
                break;
            //It is a ranged attack.
            default:
                //$GLOBALS['output'].= "Range($range) effect.<br>";
                $fight->affect_group_range($callback,$this,$this->command,$pindex,$gindex,$cindex,$range,$tpindex,$tgindex,$tcindex,$action_list);
                break;
            }
        }

    function field_do_affect($callback,&$party,$range,$tgindex,$tcindex,&$action_list)
        {
        $key=$party->get_character_key($this);
        if(!$key)
            $key="-1;-1";
        list($gindex,$cindex)=explode(';',$key);
        //First we handle party and group scenarios.
        switch($range)
            {
            //If it is all parties, then cycle through parties.
            case -3:
            //If it is party, then cycle through groups.
            case -2:
                //$GLOBALS['output'].= "Party wide effect.<br>";
                $party->affect_party($callback,$this,$this->command,-1,$gindex,$cindex,$tpindex,$action_list);
                break;
            //If it is group, then cycle through characters.
            case -1:
                //$GLOBALS['output'].= "Group wide effect.<br>";
                $party->affect_group($callback,$this,$this->command,-1,$gindex,$cindex,-1,$tgindex,$action_list);
                break;
            //It is a ranged attack.
            default:
                //$GLOBALS['output'].= "Range($range) effect.<br>";
                $party->affect_group_range($callback,$this,$this->command,-1,$gindex,$cindex,$range,-1,$tgindex,$tcindex,$action_list);
                break;
            }
        }

    function affected_by(&$fighter,$pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$command,$intensity,$divisor,&$action_list)
        {
        //echo "affecting<br>";
        $impact_percentage=($divisor+1-$intensity)/($divisor+1.0);
        $item=-1;
        $show_anim=false;
        switch($command)
            {
            case 0: //Left hand attack
            case 1: //Right hand attack
                if($this->get_current('HP')==0)
                    break;
                //$GLOBALS['output'].= "{$fighter->name} attacks {$this->name}<br>";
                //See if we are hit.
                # Do not randomize- the roll is the randomizer
                $acc=$fighter->get_current('Accuracy',$command);
                $dod=$this->get_current('Dodge');
                $roll=mt_rand(1,$acc+$dod);
                //$GLOBALS['output'].= "A:$acc D:$dod R:$roll<br>";
                if($roll>$acc)
                    {
                    $action_list[]=array('Miss',array($tpindex,$tgindex,$tcindex));
                    break;
                    }

                //See how much we are hit for.
                # Do not randomize- the roll is the randomizer.
                $str=$fighter->get_current('Strength',$command);
                //If $roll is 1 then critical hit
                if($roll==1)
                    $str*=2;
                $blo=$this->get_current('Block');
                /* Scale dmg range by str vs blo */
                $base = atan2($blo, $str) * 8.0 / M_PI + 2;
                $base_dmg = floor($str / $base);
                /* Scale by ratio: barely hit is 50% dmg, dead-on is 100% dmg. */
                $dmg= $base_dmg * (1 + (($acc - $roll)/ ($acc - 1.0)));
                //If the target is defending then the damage is halved.
                if($this->command==6)
                    $dmg=floor($dmg/2);

                //Minimum damage is 1.
                if($dmg<1)
                    $dmg=1;
                //We would do attribute bonuses here.


                //Intensity penalty.
                $dmg=floor($dmg*$impact_percentage);

                //Inflict damage
                $this->inflict_damage($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$dmg,($roll==1?true:false),$action_list);
                break;
            case 2: //Use item
                $item=&$GLOBALS['items'][$fighter->inventory[$fighter->using]['item']];
                //$GLOBALS['output'].= "{$fighter->name} uses {$item->name} on {$this->name}<br>";
                $show_anim=do_effect(
                    $item->effect,
                    $item->base,
                    $item->added,
                    $pindex,$gindex,$cindex,
                    $this,$tpindex,$tgindex,$tcindex,$intensity,
                    $item->targets,
                    1,$action_list);
                break;
            case 4: //Use skill
                //Skills can be evaded.
                //$GLOBALS['output'].= "{$fighter->name} does {$GLOBALS['abilities'][$fighter->abilities[$fighter->using]]->name} on {$this->name}<br>";
                $ability=&$GLOBALS['abilities'][$fighter->abilities[$fighter->using]];
                if($ability->is_bad())
                    {
                    if($this->get_current('HP')==0)
                        break;
                    //See if we are hit.
                    $acc=$fighter->get_current('Accuracy');
                    $dod=$this->get_current('Dodge');
                    $roll=mt_rand(1,$acc+$dod);
                    if($roll>$acc)
                        {
                        $action_list[]=array('Miss',array($tpindex,$tgindex,$tcindex));
                        break;
                        }
                    }

                //We are hit or effect is good, do effect.
                $show_anim=do_effect(
                    $ability->effect,
                    $ability->base,
                    $ability->added,
                    $pindex,$gindex,$cindex,
                    $this,$tpindex,$tgindex,$tcindex,$intensity,
                    $ability->targets,
                    1,$action_list);
                break;
            case 5: //Cast spell
                //Effects are adjusted by resistance.
                //$GLOBALS['output'].= "{$fighter->name} casts {$GLOBALS['abilities'][$fighter->abilities[$fighter->using]]->name} on {$this->name}<br>";
                $ability=&$GLOBALS['abilities'][$fighter->abilities[$fighter->using]];
                $immunity=1;
                if($ability->is_bad())
                    {
                    if($this->get_current('HP')==0)
                        break;
                    //See if we make the saving throw.
                    $pow=$fighter->get_current('Power');
                    $res=$this->get_current('Resistance');
                    $roll=mt_rand(1,$pow+$res);
                    if($roll>$pow+$res/2)
                        {
                        $action_list[]=array('NoEffect',array($tpindex,$tgindex,$tcindex));
                        //$actions[]=array(0,'number_bounce',$tpindex,$tgindex,$tcindex,'No Effect','white');
                        break;
                        }
                    elseif($roll>$pow)
                        $immunuty=.5;
                    else
                        $immunity=1;
                    }

                $show_anim=do_effect(
                    $ability->effect,
                    $ability->base,
                    $ability->added,
                    $pindex,$gindex,$cindex,
                    $this,$tpindex,$tgindex,$tcindex,$intensity,
                    $ability->targets,
                    $immunity,$action_list);
                break;
            case 3: //Change equipment
            case 6: //Defend
            case 7: //Run
                break; //Do not do anything.
            }
        //echo "<br>End of do_affect<br>";
        //var_dump($action_list);
        }

    function inflict_damage($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$dmg,$critical_hit,&$action_list)
        {
        //Capture before for xp calculation
        $before=$this->current['HP'];

        //If this dmg will make HP go over max, then scale down dmg.
        if($this->get_current('HP')-$dmg>$this->get_base('HP'))
            $dmg=$this->get_current('HP')-$this->get_base('HP');

        //Display damage.
        if($dmg>=0)
            {
            //$action_list[]=array('AlterStat',array($tpindex,$tgindex,$tcindex,'HP',-$dmg));
            $action_list[]=array('Damage',array($tpindex,$tgindex,$tcindex,$dmg,$critical_hit));
            }
        else
            {
            //$action_list[]=array('AlterStat',array($tpindex,$tgindex,$tcindex,'HP',$dmg));
            $action_list[]=array('Restore',array($tpindex,$tgindex,$tcindex,'HP',-$dmg));
            }

        //Inflict damage on this target.
        $this->current['HP']-=$dmg;
        //If the target died, then display death.
        if($this->current['HP']<=0)
            {
            $action_list[]=array('Died',array($tpindex,$tgindex,$tcindex,$dmg));
            //Adjust dmg if it was excessive.
            $dmg+=$this->current['HP'];
            //Set hp to 0.
            $this->current['HP']=0;
            }
        //Add to the xp tree
        $this->add_debt_to_xp_tree($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$before,$this->current['HP'],$this->get_base('HP'));
        //var_dump($action_list);
        }

    function restore_HP($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$amt,&$action_list)
        {
        //Capture before for xp calculation
        $before=$this->current['HP'];

        //If this dmg will make HP go over max, then scale down dmg.
        if($this->get_current('HP')+$amt>$this->get_base('HP'))
            $amt=$this->get_base('HP')-$this->get_current('HP');

        //Display damage.
        if($amt<0)
            {
            //$action_list[]=array('AlterStat',array($tpindex,$tgindex,$tcindex,'HP',$amt));
            $action_list[]=array('Damage',array($tpindex,$tgindex,$tcindex,-$amt,false));
            }
        else
            {
            //$action_list[]=array('AlterStat',array($tpindex,$tgindex,$tcindex,'HP',$amt));
            $action_list[]=array('Restore',array($tpindex,$tgindex,$tcindex,'HP',$amt));
            }

        //Inflict damage on this target.
        $this->current['HP']+=$amt;
        //If the target died, then display death.
        if($this->current['HP']<=0)
            {
            $this->current['HP']=0;
            //Denote death of character.
            $action_list[]=array('Died',array($tpindex,$tgindex,$tcindex,-$amt));
            }
        //Add to the xp tree
        $this->add_debt_to_xp_tree($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$before,$this->current['HP'],$this->get_base('HP'));
        }

    function restore_MP($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$amt,&$action_list)
        {
        //Capture before for xp calculation
        $before=$this->current['MP'];

        //If this amt will make MP go over max, then scale down amt.
        if($this->get_current('MP')+$amt>$this->get_base('MP'))
            $amt=$this->get_base('MP')-$this->get_current('MP');

        //Display damage.
        //$GLOBALS['output'].= "Restored $amt MP.<br>";
        //$actions[]=array(0,'number_bounce',$tpindex,$tgindex,$tcindex,abs($amt),($amt>0?'purple':'white'));
        //$actions[]=array(0,'alter_stat',$tpindex,$tgindex,$tcindex,'MP',$amt);
        //$action_list[]=array('AlterStat',array($tpindex,$tgindex,$tcindex,'MP',$amt));
        $action_list[]=array('Restore',array($tpindex,$tgindex,$tcindex,'MP',$amt));

        //Inflict damage on this target.
        $this->current['MP']+=$amt;

        //Add to the xp tree
        $this->add_debt_to_xp_tree($pindex,$gindex,$cindex,$tpindex,$tgindex,$tcindex,$before,$this->current['MP'],$this->get_base('MP'));
        }

    function get_attack_animation($side)
        {
        $slot=($side==0?'lhand':'rhand');
        /*
        Based on what the weapon does, we should do one of the following:
        Swing
        Throw
        Fire
        */
        return $this->personality->attack_animation;
        }


    function is_dead()
        {
        $retval=($this->current['HP']<1);
        return $retval;
        }
        
    function get_perceived_stat(&$target,$statname)
        {
        $stat=$target->get_current($statname);
        $variance=$this->ai_experience;
        return $stat*100/mt_rand(10000-$variance*100,10000+$variance*100);
        }    

    function add_debt_to_xp_tree($party,$group,$character,$tparty,$tgroup,$tcharacter,$before,$after,$gague)
        {
        //log_error("XP tree addition to $tparty;$tgroup;$tcharacter for $party;$group;$character: $before,$after,$gague for {$this->pxp}XP");
        $this->xp_debts[]=array("$party;$group;$character",($before-$after)/$gague,$this->pxp);
        }
    }

function hero($name,$job,$level,$personality,$charid=null)
    {
    $character=new CHARACTER;
    $character->make_leveled_hero($name,$job,$level,$personality,$charid);
    return $character;
    }

function monster($name,$stats,$inventory,$equipment,$abilities,$personalityid,$gold,$ai_action,$ai_goal,$ai_target,$ai_experience)
    {
    $character=new CHARACTER;
    $character->make_monster($name,$stats,$stats,$abilities,$personalityid,$gold,$ai_action,$ai_goal,$ai_target,$ai_experience);
      foreach($inventory as $item)
         $character->add_item($item['item'],$item['qty']);
      foreach($equipment as $equip)
         $character->equip_item ($equip['slot'],$equip['side']);
    return $character;
    }
?>
