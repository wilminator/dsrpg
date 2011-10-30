<?php
/**
 * monster.php
 * monsters/classes object
 * @version 0.1.0
 * @copyright 2003 Mike Wilmes
 **/

require_once INCLUDE_DIR.'character.php';
require_once INCLUDE_DIR.'group.php';

class MONSTER
    {
    var $name;
    var $pxp;
    var $stats;
    var $abilities;
    var $items;
    var $equipment;
    var $gold;
    var $personalityid;
    var $ai_action;
    var $ai_goal;
    var $ai_target;
    var $ai_experience;

    function MONSTER($name,$stats,$abilities,$items,$equipment,$gold,$personalityid,$ai_action,$ai_goal,$ai_target,$ai_experience)
        {
        $this->name=$name;
        $this->stats=create_array($GLOBALS['character_stats'],$stats,0);
        $this->abilities=$abilities;
        $this->items=$items;
        $this->equipment=$equipment;
        $this->gold=$gold;
        $this->ai_action=$ai_action;
        $this->ai_goal=$ai_goal;
        $this->ai_target=$ai_target;
        $this->ai_experience=$ai_experience;
        $this->personalityid=$personalityid;
        $character=$this->make_monster();
        $this->pxp=$character->calculate_pxp();
        }

    function make_monster($suffix='',$HPratio=1,$MPratio=1)
        {
        $character=new CHARACTER;
        $current=$this->stats;
        $current['HP']=ceil($current['HP']*$HPratio);
        $current['MP']=ceil($current['MP']*$MPratio);
        $character->make_monster($this->name.$suffix,$this->stats,$current,$this->abilities,$this->personalityid,$this->gold,$this->ai_action,$this->ai_goal,$this->ai_target,$this->ai_experience);
        foreach($this->items as $item)
           $character->add_item($item['item'],$item['qty']);
        foreach($this->equipment as $equipment)
           $character->equip_item ($equipment['slot'],$equipment['side']);
        $character->gold=$this->gold;
        return $character;
        }


    function describe_stats()
        {
        $response=array("<b>PXP {$this->pxp}</b>","Gold {$this->gold}");
        foreach($this->stats as $index=>$stat)
            if($stat!=0)
                $response[]="$index $stat";
        return implode(' ',$response);
        }

    }

function &get_monsters_in_range($target_pxp,$spread=0)
    {
    $min_pxp=floor($target_pxp*(1.0-($spread*2/3)));
    $max_pxp=floor($target_pxp*(1.0+($spread*1/3)));
    $monsters=array_filter($GLOBALS['monsters'],create_function('$a','return $a->pxp>='.$min_pxp.' && $a->pxp<='.$max_pxp.';'));
    return $monsters;
    }

function &pick_monsters($target_pxp,$qty)
    {
    $spread=.05;
    do {
        $monsters=get_monsters_in_range($target_pxp,$spread);
        $spread+=.05;
        } while(count($monsters)<$qty+2);
    $retval=array();
    $array=array_rand($monsters,min($qty,count($monsters)));
    if(!is_array($array))
        $array=array($array);
    foreach($array as $key)
        $retval[]=&$monsters[$key];
    return $retval;
    }

function &create_monster_party($player_pxp,$player_qty,$monster_qty)
    {
    //Get xp per monster and group count
    if ($monster_qty > $player_qty)
        {
        $ratio = $monster_qty / $player_qty;
        $offset = 1 - ($ratio - 1)/(2 * $ratio);
        $pxp_per_monster=$player_pxp / $player_qty * $offset;
        }
    else
        {
        $ratio = $player_qty / $monster_qty;
        $offset = 1 + ($ratio - 1)/(2 * $ratio);
        $pxp_per_monster=$player_pxp / $player_qty * $offset;
        }
    $target_pxp = $pxp_per_monster * $monster_qty;
    $groups_from_qty=array(
         1=>array(1),
         2=>array(1),
         3=>array(1,2),
         4=>array(1,2),
         5=>array(1,2),
         6=>array(1,2),
         7=>array(2,3),
         8=>array(2,3),
         9=>array(2,3),
        10=>array(2,3,4),
        11=>array(2,3,4),
        12=>array(2,3,4),
        13=>array(2,3,4),
        14=>array(2,3,4),
        15=>array(2,3,4),
        16=>array(3,4),
        17=>array(3,4),
        18=>array(3,4),
        19=>array(3,4),
        20=>array(3,4),
        21=>array(3,4),
        22=>array(3,4),
        23=>array(3,4),
        24=>array(3,4),
        25=>array(3,4),
        26=>array(3,4),
        27=>array(3,4)
        );
    if(array_key_exists((int)$monster_qty,$groups_from_qty))
        $options=$groups_from_qty[$monster_qty];
    else
        $options=array(4);
    $groups=$options[array_rand($options)];
    //$GLOBALS['output'].= "Starting groups: $groups<br>";

    //Get the monsters for this party.
    $monsters=pick_monsters($pxp_per_monster,$groups);
    $groups=count($monsters);
    //$GLOBALS['output'].= "Ending groups: $groups<br>";

    //Setup the monster party and HP ratio.
    $party=new PARTY;
    $party->monster=true;
    $ratios=array();
    $total_pxp=0;
    for($count=0;$count<$groups;$count++)
        {
        $party->add(new GROUP);
        $party->groups[$count]->name="{$monsters[$count]->name} group";
        $ratios[$count]=array(min(mt_rand(0,250),100));
        $total_pxp+=$monsters[$count]->pxp;
        }

    //Loop until we are within 80-110% of target pxp
    $odds=1;
    while($monster_qty > count($party->get_character_list(null)))
        {
        $odds=0;
        // Add a monster type only if it will not put us over the cap.
        for($count=0;$count<$groups;$count++)
            if ($total_pxp + $monsters[$count]->pxp <= $target_pxp)
                {
                $odd=81-(count($ratios[$count])*count($ratios[$count]));
                //$GLOBALS['output'].= "$odd:";
                $odds+=$odd;
                }
        if($odds==0)
            break;
        $chance=mt_rand(1,$odds);
        //$GLOBALS['output'].= "($chance)";
        for($count=0;$count<$groups;$count++)
            if ($total_pxp + $monsters[$count]->pxp <= $target_pxp)
                {
                if ($chance<=(81 - count($ratios[$count])*count($ratios[$count])))
                    {
                    $ratios[$count][]=min(mt_rand(0,250),100);
                    $total_pxp+=$monsters[$count]->pxp;
                    break;
                    }
                else
                    {
                    $chance -= 81 - (count($ratios[$count])*count($ratios[$count]));
                    //$GLOBALS['output'].= "-($count-$chance)";
                    }
                }
        }
    log_error("Player PXP:$player_pxp, Player Qty:$player_qty, Monster Qty:$monster_qty,Offset: $offset,PXP Per Monster:$pxp_per_monster,Target Party PXP:$target_pxp");
    //Fill the party
    for($count=0;$count<$groups;$count++)
        foreach($ratios[$count] as $index=>$ratio)
            $party->groups[$count]->add_monster($monsters[$count]->make_monster((count($ratios[$count])==1?'':'-'.chr(ord('A')+$index)),$ratio/100.0,min(mt_rand(0,150),100)/100.0));
    //Return the party
    return $party;
    }
?>