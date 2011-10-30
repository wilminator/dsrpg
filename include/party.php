<?php
/**
 * party.php
 * party object
 * @version 0.1.0
 * @copyright 2003 Mike Wilmes
 **/

require_once INCLUDE_DIR.'group.php';
require_once INCLUDE_DIR.'character_store.php';
require_once INCLUDE_DIR.'team_store.php';

define('PARTY_MAX_COUNT',3);

class PARTY{
    var $groups;
    var $teams;
    var $name;
    var $allies;
    var $enemies;
    var $monster;
    var $location;

    function PARTY()
        {
        $this->groups=array();
        $this->teams=array();
        $this->name='';
        $this->allies=array();
        $this->enemies=array();
        $this->monster=false;
        }

    function add($group)
        {
        if(is_a($group,'GROUP'))
            {
            $this->groups[count($this->groups)]=&$group;
            if($group->name=='' && count($group->characters)>0)
                $group->name=$group->characters[0]->name."'s group";
            return false;
            }
        return true;
        }

    function remove($index)
        {
        if($index<$this->count())
            {
            array_splice($this->groups,$index,1);
            $this->characters=array_merge($this->groups);
            return false;
            }
        return true;
        }

    function count()
        {
        return count($this->groups);
        }

    function to_js()
        {
        foreach($this->groups as $index=>$group)
            $output[$index]=$group->to_js();
        $output="[".implode(",",$output)."]";
        return "{groups:$output}";
        }

    function affect_party($callback,&$fighter,$command,$pindex,$gindex,$cindex,$tpindex,&$action_list)
        {
        if ($this->is_dead())
            return;
        foreach(array_keys($this->groups) as $tgindex)
            {
            $group=&$this->groups[$tgindex];
            $group->affect_group($callback,$fighter,$command,$pindex,$gindex,$cindex,$tpindex,$tgindex,$action_list);
            }
        }

    function affect_group($callback,&$fighter,$command,$pindex,$gindex,$cindex,$tpindex,$tgindex,&$action_list)
        {
        if (array_key_exists($tgindex,$this->groups))
            $this->groups[$tgindex]->affect_group($callback,$fighter,$command,$pindex,$gindex,$cindex,$tpindex,$tgindex,$action_list);
        }

    function affect_group_range($callback,&$fighter,$command,$pindex,$gindex,$cindex,$range,$tpindex,$tgindex,$tcindex,&$action_list)
        {
        if (array_key_exists($tgindex,$this->groups))
            $this->groups[$tgindex]->affect_group_range($callback,$fighter,$command,$pindex,$gindex,$cindex,$range,$tpindex,$tgindex,$tcindex,$action_list);
        }

    function get_pxp()
        {
        $pxp=0;
        foreach(array_keys($this->groups) as $group)
            $pxp+=$this->groups[$group]->get_pxp();
        return $pxp;
        }

    function is_dead()
        {
        $sum=true;
        foreach($this->groups as $index=>$group)
            $sum=($sum&&$group->is_dead());
        return $sum;
        }

    //This function rebuilds the list of allies and enemies for this party.
    function find_party_alignment(&$fight)
        {
        //Reset the ally and enemy lists.
        $this->allies=array();
        $this->enemies=array();

        //This is the simple cut-down version.
        //If you are not this party, then you are an enemy. (Each for self)
        foreach(array_keys($fight->parites) as $index)
            if($fight->parites[$index]===$this)
                $this->allies[]=$index;
            else
                $this->enemies[]=$index;
        }

    function get_ally_parties(&$fight)
        {
        return $this->allies;
        }

    function get_enemy_parties(&$fight)
        {
        return $this->enemies;
        }

    function get_non_ally_parties(&$fight)
        {
        return array_diff(array_keys($fight->parties),$this->allies);
        }

    function get_non_enemy_parties(&$fight)
        {
        return array_diff(array_keys($fight->parties),$this->enemies);
        }

    function get_character_list($living)
        {
        $retval=array();
        foreach(array_keys($this->groups) as $index)
            {
            $result=$this->groups[$index]->get_character_list($living);
            foreach($result as $char)
                $retval[]="$index;$char";
            }
        return $retval;
        }

    function &get_character($key)
        {
        list($group,$sub_key)=explode(';',$key,2);
        while($group==-1)
            list($group,$sub_key)=explode(';',$sub_key,2);
        return $this->groups[$group]->get_character($sub_key);
        }

    function get_group_list($living)
        {
        $retval=array();
        foreach(array_keys($this->groups) as $index)
            {
            $result=!$this->groups[$index]->is_dead();
            if($living===$result || is_null($living))
                $retval="$index";
            }
        return $retval;
        }

    function add_team(&$team)
        {
        //Verify that there are not too many total people
        $count=0;
        foreach(array_keys($this->groups) as $index)
            $count+=$this->groups[$index]->count();
        if ($count+$team->count()>=GROUP_MAX_COUNT*PARTY_MAX_COUNT)
            return true;
        //Add teamid to teams array
        $this->teams[$team->teamid]=array();
        //Add characters to party
        #Find a group to add whole team to.
        if($this->count()<PARTY_MAX_COUNT)
            $group=$this->count();
        else
            $group=0;
        while($group<PARTY_MAX_COUNT
            && isset($this->groups[$group])
            && $this->groups[$group]->count()+$team->count()>GROUP_MAX_COUNT)
            $group++;
        #If no available group exists, then start from the beginning.
        if($group==PARTY_MAX_COUNT)
            $group=0;
        $char_store=new CHARACTER_STORE;
        foreach(array_keys($team->characters) as $index)
            {
            $this->teams[$team->teamid][]=$team->characters[$index]->charid;
            while(isset($this->groups[$group])
                && $this->groups[$group]->count()==GROUP_MAX_COUNT)
                $group++;
            $character=&$char_store->get_character($team->characters[$index]->charid);
            $character->teamid=$team->teamid;
            $this->insert_character($group,null,$character);
            }
        }

    function remove_team($teamid)
        {
        //Reconstitute the team object
        //Remove all characters in this team from the party
        //Remove the teamid from the teams array
        }

    function get_character_key(&$character)
        {
        foreach(array_keys($this->groups) as $index)
            {
            $result=$this->groups[$index]->get_character_key($character);
            if(!is_null($result))
                return "$index;$result";
            }
        return null;
        }

    function find_character($charid)
        {
        foreach(array_keys($this->groups) as $index)
            {
            $result=$this->groups[$index]->find_character($charid);
            if($result!==true)
                return "$index;$result";
            }
        return true;
        }

    function insert_character($group,$position,&$character)
        {
        //Check group index.
        if (!isset($this->groups[$group]) && $group>1 && !isset($this->groups[$group-1]))
            return true;
        //Make the group if needed.
        if (!isset($this->groups[$group]))
            $this->add(new GROUP());
        //Insert character.
        return $this->groups[$group]->add_hero($character,$position);
        }

    function remove_character($group,$position)
        {
        //Check group index.
        if (!isset($this->groups[$group]))
            return true;
        //Remove character.
        $result=$this->groups[$group]->remove_fighter($position);
        //If that was the last person in that group, then remove the group.
        if($this->groups[$group]->count()==0)
            $this->remove($group);
        return $result;
        }

    function move_character($old_group,$old_position,$new_group,$new_position)
        {
        echo "$old_group,$old_position,$new_group,$new_position<br>";
        //Check old_group index.
        if (!isset($this->groups[$old_group]))
            return true;
        //Check new_group size.
        if (isset($this->groups[$new_group])
            && $old_group!=$new_group
            && $this->groups[$new_group]->count()>=GROUP_MAX_COUNT)
            return true;
        //Save the character.
        $character=$this->get_character("$old_group;$old_position");
        if($character===true)
            return true;
        //Check if this is the last person in the group
        $group_count=$this->groups[$old_group]->count();
        //Remove character from old position
        #var_dump($this);
        $result=$this->remove_character($old_group,$old_position);
        if($result===true)
            return true;
        //Fix group number if needed.
        if($group_count==1 && $new_group>$old_group)
            $new_group--;
        //Insert character into new location
        //return
        #var_dump($this);
        $this->insert_character($new_group,$new_position,$character);
        #var_dump($this);
        }

    function teams_in_party()
        {
        $teams=array();
        $team_store=new TEAM_STORE;
        foreach($this->teams as $teamid=>$members)
            {
            $team=$team_store->get_team($teamid);
            foreach($members as $index=>$charid)
                {
                  $loc=$this->find_character($charid);
                  //echo "$loc<br>";
                $team->characters[$index]=&$this->get_character($loc);
                }
            $teams[$teamid]=$team;
            }
        return $teams;
        }

    function store_party()
        {
        $char_store=new CHARACTER_STORE;
        foreach(array_keys($this->groups) as $gindex)
            foreach(array_keys($this->groups[$gindex]->characters) as $cindex)
                $char_store->set_character($this->groups[$gindex]->characters[$cindex]);
        }

    //Unused
    function refresh_party()
        {
        $char_store=new CHARACTER_STORE;
        $char_list=array();
        foreach(array_keys($this->groups) as $gindex)
            foreach(array_keys($this->groups[$gindex]->characters) as $cindex)
                $char_list[$this->groups[$gindex]->characters[$cindex]->charid]=array($gindex,$cindex);
        foreach($char_list as $charid=>$location)
            {
            list($gindex,$cindex)=$location;
            $this->groups[$gindex]->characters[$cindex]=&$char_store->get_character($charid);
            }
        }

    function distribute_gold($gold)
        {
        $this->store_party();
        $teams=$this->teams_in_party();
        $team_store=new TEAM_STORE;
        $take=floor($gold/count($teams));
        foreach(array_keys($teams) as $index)
            {
            $teams[$index]->gold+=$take;
            $team_store->set_team($teams[$index]);
            }
        return $take;
        }


    function do_field_action($command,$using,$gindex,$cindex,$tgindex,$tcindex)
        {
        $fighter=$this->get_character("$gindex;$cindex");
        $fighter->command=$command;
        $fighter->using=$using;
        $may_affect=true;
        $action_list=array();

        //Indicate that this is the beginning of the fighter's turn.
        $action_list[]=array('Turn',array(-1,$gindex,$cindex));

        switch($fighter->command)
            {
            case 2: //Item use
                //if($fighter->is_paralyzed())
                //  This action may not be performed.
                //  continue 2;
                $item=$fighter->inventory[$fighter->using]['item'];
                $range=$GLOBALS['items'][$item]->use_targets;
                $action_list[]=array('Item',$item);
                break;
            case 3: //Equip weapon
                //if($fighter->is_paralyzed())
                //  This action may not be performed.
                //  continue 2;
                $may_effect=false;
                list($location,$ammo,$tcindex)=$fighter->target;
                //Equip new item.
                //Unequip old items preventing us from equipping the new item.
                do {
                       $item=$fighter->equip_item($fighter->using,$location);
                       if($item===false)
                           {
                           log_error("There was a severe error: Could not equip item in slot {$fighter->using} to location $location.");
                           return $action_list;
                           }
                       if(!is_array($item))
                           {
                           $item=$fighter->unequip_item($item);
                           if ($item===false)
                               {
                               log_error("There was a severe error: Could not unequip item item in slot $old_item.");
                                 return $action_list;
                               }
                            foreach($item as $slot)
                                {
                                $action_list[]=array('UnequipSlot',array($slot));
                                }
                           }
                    } while(!is_array($item));
                foreach($item as $slot)
                    {
                    $action_list[]=array('EquipSlot',array($fighter->using,$slot));
                    }
                $eitem=$fighter->inventory[$fighter->using]['item'];
                //$fight_message='Equipping '.$GLOBALS['items'][$item]->name;
                $action_list[]=array('Equip',array($eitem));
                break;
            case 4: //Skill use
                //if($fighter->is_paralyzed())
                //  This action may not be performed.
                //  continue 2;
                $ability=$fighter->abilities[$fighter->using];
                $range=$GLOBALS['abilities'][$ability]->targets;
                //$fight_message=$GLOBALS['abilities'][$ability]->name;
                $action_list[]=array('Skill',$ability);
                break;
            case 5: //Spell use
                $ability=$fighter->abilities[$fighter->using];
                $range=$GLOBALS['abilities'][$ability]->targets;
                $action_list[]=array('Spell',$ability);
                //$fight_message=$GLOBALS['abilities'][$ability]->name;
                //$may_affect=!$fighter->is_spell_blocked();
                break;
            case 9: //Give item
                $eitem=$fighter->inventory[$fighter->using]['item'];
                //$range=$GLOBALS['items'][$item]->use_targets;
                $action_list[]=array('Give',$eitem);
                //$fight_message=$GLOBALS['items'][$item]->name;
                break;
            }

        //Indicate the target.
        $action_list[]=array('Target',array(-1,$tgindex,$tcindex,$range));

        //Step four.
        //If this is a spell or skill, do MP check and consumption
        if($fighter->command==4||$fighter->command==5)
            {
            if ($fighter->get_current('MP')<$GLOBALS['abilities'][$ability]->mp_used)
                {
                $may_affect=false;
                $action_list[]=array('NoMP',array(-1,$gindex,$cindex));
                //$fight_message='Not enough MP.';
                }
            else
                {
                $GLOBALS['output'].= "MP reduced by {$GLOBALS['abilities'][$ability]->mp_used}.<br>";
                $action_list[]=array('AlterStat',array(-1,$gindex,$cindex,'MP',-$GLOBALS['abilities'][$ability]->mp_used));
                ////$playback_actions[]=array(0,'alter_stat',-1,$gindex,$cindex,'MP',-$GLOBALS['abilities'][$ability]->mp_used);
                $fighter->current['MP']-=$GLOBALS['abilities'][$ability]->mp_used;
                }
            }

        //Step three. Have the fighter do an action.
        //if($fight_message)
        //    {
        //    //$playback_actions[]=array(0,'set_fight_message',$fight_message);
        //    //$playback_actions[]=array(0,'show_fight_message_by_hero',-1,$gindex,$cindex);
        //    }
        //$fighter->animate_action($playback_actions,-1,$gindex,$cindex,-1,$tgindex,$tcindex);

        //Step five. If the fighter MAY affect the target(s), then do so.
        if($may_affect)
            {
            $fighter->field_do_affect('perform_action',$this,$range,$tgindex,$tcindex,$action_list);
            //var_dump($action_list);
            //If this was an item use, consume if it it is not unlimited use
            if($fighter->command==2)
                {
                if($GLOBALS['items'][$fighter->inventory[$fighter->using]['item']]->one_use)
                    {
                    $fighter->remove_item($fighter->using,1);
                    $action_list[]=array('UseItem',array($fighter->using));
                    //$playback_actions[]=array(0,'consume_item',-1,$gindex,$cindex,$fighter->using,true);
                    }
                }
            }
        $this->store_party();
        return $action_list;
        }

    }
?>