<?php
require_once INCLUDE_DIR.'location.php';
require_once INCLUDE_DIR.'party.php';

require_once INCLUDE_DIR.'js_rip.php';

/**
 * fight.php
 * fight object
 * @version 0.1.0
 * @copyright 2003 Mike Wilmes
 **/

class FIGHT{
    var $id;
    var $parties;
    var $location;
    var $counter;
    var $exp_tree;
    var $exp_debts;
    var $background;
    var $music;

    function FIGHT($background,$music)
        {
        $this->parties=array();
        $this->location=array();
        $this->counter=0;
        $this->exp_tree=array();
        $this->id=0;
        $this->background=$background;
        $this->music=$music;
        }

     function add($party)
        {
        if(is_a($party,'PARTY'))
            {
            $this->parties[count($this->parties)]=&$party;
            if($party->name=='')
                $party->name=$party->groups[0]->characters[0]->name."'s party";
            //Clear our xp debts.
            foreach(array_keys($party->groups) as $group)
                foreach(array_keys($party->groups[$group]->characters) as $character)
                    {
                    $party->groups[$group]->characters[$character]->xp_debts=array();
                    $party->groups[$group]->characters[$character]->old_exp=$party->groups[$group]->characters[$character]->exp;
                    }
            return false;
            }
        return true;
        }
        
    function count()
        {
        return count($this->parties);
        }

    function to_js()
        {
        return php_data_to_js($this);
        }

    function set_NPC_actions()
        {
        }

    function set_PC_actions($player_party,$player_team,$actions)
        {
        /*
        This is where the input hardening needs to take place.
        First check to see that all characters controlled belong to
        the team controlled by the player.

        Next ensure that all commands passed in are valid.
        Kick back if not.
        */
        //log_error("Setting many PC actions, player_party=$player_party, player_team=$player_team);
        $commands=explode(';',$actions);
        foreach($this->parties[$player_party]->groups as $grpnumber=>$group)
            foreach($group->characters as $chrnumber=>$hero)
                {
                $hero=&$fight->parties[0]->groups[$grpnumber]->characters[$chrnumber];
                $mycmd=array_shift($commands);
                list($cmd,$use,$pty,$grp,$chr)=explode(',',$mycmd);
                $hero->command=$cmd;
                $hero->using=$use;
                $hero->target=array($pty,$grp,$chr);
                }
        return true;
        }

    function set_PC_action($player_party,$player_group,$player_character,$player_team,$action)
        {
        /*
        This is where the input hardening needs to take place.
        First check to see that all characters controlled belong to
        the team controlled by the player.
        */
        //This check is if there are normal PCs in the party.
        //log_error("Setting PC action, player_party=$player_party, player_team=$player_team SESSION=".var_export($_SESSION,true)." fight=".var_export($this,true));
        if($player_team>0)
            {
            $fighter=&$this->parties[$player_party]->groups[$player_group]->characters[$player_character];
            //If this character ID is not in the teams[teamid] list, then bail.
            if($fighter->teamid!=$player_team)
                return false;
            }
        //This check is if a monster party is controlled by a player
        else
            {
            if($this->parties[$player_party]->monster==false)
                return false;
            $fighter=&$this->parties[$player_party]->groups[$player_group]->characters[$player_character];
            }
        /*
        Next ensure that all commands passed in are valid.
        Kick back if not.
        */
        list($fighter->command,$fighter->target,$fighter->using)=$action;

        return true;
        }


    /*
    OK, the meat and potatoes of this- combat simulation!
    What we are doing here-
    1. Generate a list of participants (characters with more than 0 HP,) ordered
        most to least by their speed+/-15% less focus/MP if a spell or skill
        is being used.
    2. Loop through the list, giving each character an opportunity to perform
        their action.
    3. For each action, record the outcomes so the client can play them back.
    4. If any character dies, remove them from the list.
    5. Return the outcomes to the caller.
    */

    function do_combat()
        {
        //Make the fight order list.
        $participants=$this->generate_participant_list();

        //Prep the playback action list!
        $action_list=array();

        //We are ready to rumble.
        //While someone has a turn let them go!
        //Stop early if the party is only one left.
        //log_error("Live parties:".$this->count_live_parties(),100);
        while(count($participants)>0
            && $this->count_live_parties()>1)
            {
            //Get the lucky person!
            list($pindex,$gindex,$cindex)=explode('.',array_shift($participants));
            $party=&$this->parties[$pindex];
            $group=&$party->groups[$gindex];
            $fighter=&$group->characters[$cindex];

            //If the person is dead, then skip.
            if ($fighter->get_current('HP')==0)
                continue;

            //Indicate that this is the beginning of the fighter's turn.
            $action_list[]=array('Turn',array($pindex,$gindex,$cindex));

            //Validate that the fighter can perform these actions
            if($fighter->validate_action()==false)
                continue;

            //Step one.  Figure out what our fight is doing!
            //While we are at it, find the range of attack, if applicable.
            //And check to see if the fighter MAY affect the target.
            $may_affect=true;
            $fight_message='';
            switch($fighter->command)
                {
                case 0: //Left hand attack
                    //if($fighter->is_paralyzed())
                    //  This action may not be performed.
                    //  continue 2;
                    $range=$fighter->find_target_range($fighter->equipment['lhand'],$fighter->equipment['lammo']);
                    //Validate that the target is valid.
                    if($fighter->validate_target($this,$range,2))//2 is damage effect
                        continue 2;
                    //Get the item id.
                    if(!is_null($fighter->equipment['lhand']))
                        $item=$fighter->inventory[$fighter->equipment['lhand']]['item'];
                    else
                        $item=0;
                    //log_error("Attacking with LEFT item $item range $range".var_export($fighter,true));
                    //if ($range>0 && $item==0) exit;
                    $may_affect=$fighter->expend_ammo($pindex,$gindex,$cindex,0,$action_list);
                    if($may_affect)
                        {
                        $action_list[]=array('Attack',array($item,'left',$range));
                        }
                    break;
                case 1: //Right hand attack
                    //if($fighter->is_paralyzed())
                    //  This action may not be performed.
                    //  continue 2;
                    $range=$fighter->find_target_range($fighter->equipment['rhand'],$fighter->equipment['rammo']);
                    //Validate that the target is valid.
                    if($fighter->validate_target($this,$range,2)) //2 is damage effect
                        continue 2;
                    //Get the item id.
                    if(!is_null($fighter->equipment['rhand']))
                        $item=$fighter->inventory[$fighter->equipment['rhand']]['item'];
                    else
                        $item=0;
                    //log_error("Attacking with RIGHT item $item range $range".var_export($fighter,true));
                    //if ($range>0 && $item==0) exit;
                    $may_affect=$fighter->expend_ammo($pindex,$gindex,$cindex,1,$action_list);
                    if($may_affect)
                        {
                        $action_list[]=array('Attack',array($item,'right',$range));
                        }
                    break;
                case 2: //Item use
                    //if($fighter->is_paralyzed())
                    //  This action may not be performed.
                    //  continue 2;
                    $item=$fighter->inventory[$fighter->using]['item'];
                    $range=$GLOBALS['items'][$item]->use_targets;
                    $effect=$GLOBALS['items'][$item]->effect;
                    //Validate that the target is valid.
                    if($fighter->validate_target($this,$range,$effect))
                        continue 2;
                    $fight_message=$GLOBALS['items'][$item]->name;
                    $action_list[]=array('Item',$item);
                    break;
                case 3: //Equip weapon
                case 8: //Equip weapon and ammo
                    //if($fighter->is_paralyzed())
                    //  This action may not be performed.
                    //  continue 2;
                    $may_effect=false;
                    list($location,$ammo,$tcindex)=$fighter->target;
                    if($fighter->using!=-1)
                        {
                        //Equip new item.
                        //Unequip old items preventing us from equipping the new item.
                        do {
                               $item=$fighter->equip_item($fighter->using,$location);
                               if($item===false)
                                   {
                                   log_error("There was a severe error: Could not equip item in slot {$fighter->using} to location $location.");
                                   continue 3;
                                   }
                               if(!is_array($item))
                                   {
                                   $old_item=$item;
                                   $item=$fighter->unequip_item($item);
                                   if ($item===false)
                                       {
                                       log_error("There was a severe error: Could not unequip item item in slot $old_item.");
                                       continue 3;
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
                        if($fighter->command==8)
                            {
                            if(!in_array('hand',$GLOBALS['items'][$fighter->inventory[$fighter->using]['item']]->equip_type))
                                //Equip right hand weapon with ammo in two handed weapon
                                $location=0;
                            $item=$fighter->equip_item($ammo,$location);
                               if($item===false)
                                   {
                                   log_error("There was a severe error: Could not equip item in slot $ammo to location $location.");
                                   continue 2;
                                   }
                            foreach($item as $slot)
                                {
                                $action_list[]=array('EquipSlot',array($ammo,$slot));
                                }
                            $item=$fighter->inventory[$ammo]['item'];
                            $fight_message.='and '.$GLOBALS['items'][$ammo]->name;
                            }
                        if($fighter->command==8)
                            $action_list[]=array('Equip',array($eitem,$ammo));
                        else
                            $action_list[]=array('Equip',array($eitem));
                           continue 2;
                        }
                    else
                        {
                        $eitem=$fighter->inventory[$location]['item'];
                        $item=$fighter->unequip_item($location);
                           if($item===true)
                               {
                               echo "There was a severe error: Tried to unequip non-existant inventory item $location.";
                               continue 2;
                               }
                           if($item===false)
                               {
                               echo "There was a severe error: Tried to unequip non-equipped item $location.";
                               continue 2;
                               }
                        $action_list[]=array('Unequip',array($GLOBALS['items'][$eitem]->name));
                        foreach($item as $slot)
                            {
                            $action_list[]=array('UnequipSlot',array($slot));
                            }
                           continue 2;
                        }
                    break;
                case 4: //Skill use
                    //if($fighter->is_paralyzed())
                    //  This action may not be performed.
                    //  continue 2;
                    $ability=$fighter->abilities[$fighter->using];
                    $range=$GLOBALS['abilities'][$ability]->targets;
                    $effect=$GLOBALS['abilities'][$ability]->effect;
                    //Validate that the target is valid.
                    if($fighter->validate_target($this,$range,$effect))
                        continue 2;
                    $action_list[]=array('Skill',$ability);
                    break;
                case 5: //Spell use
                    $ability=$fighter->abilities[$fighter->using];
                    $range=$GLOBALS['abilities'][$ability]->targets;
                    $effect=$GLOBALS['abilities'][$ability]->effect;
                    //Validate that the target is valid.
                    if($fighter->validate_target($this,$range,$effect))
                        continue 2;
                    $action_list[]=array('Spell',$ability);
                    //$may_affect=!$fighter->is_spell_blocked();
                    break;
                case 6: //Defend (should not see)
                    $action_list[]=array('Defend',array());
                    continue 2;
                case 7: //Run (scared!)
                    //if($fighter->is_paralyzed())
                    //  This action may not be performed.
                    //  continue 2;
                    //Try and run
                    //If successful, remove from combat.
                    //Otherwise, do nothing.
                    $action_list[]=array('Run',array());
                    continue 2;
                }

            //Validate that the fighter can perform on the desired target.
            //if it returns true, then continue; there is no possible target for this action.
            if($may_affect==false)
                continue;

            //Set the target.
            list($tpindex,$tgindex,$tcindex)=$fighter->target;

            //Indicate the target.
            $action_list[]=array('Target',array($tpindex,$tgindex,$tcindex,$range));

            //Step two.
            //If this is a spell or skill, do MP check and consumption
            if($fighter->command==4||$fighter->command==5)
                {
                if ($fighter->get_current('MP')<$GLOBALS['abilities'][$ability]->mp_used)
                    {
                    $may_affect=false;
                    $action_list[]=array('NoMP',array($pindex,$gindex,$cindex));
                    }
                else
                    {
                    $action_list[]=array('AlterStat',array($pindex,$gindex,$cindex,'MP',-$GLOBALS['abilities'][$ability]->mp_used));
                    $fighter->current['MP']-=$GLOBALS['abilities'][$ability]->mp_used;
                    }
                }

            //Step three. If the fighter MAY affect the target(s), then do so.
            if($may_affect)
                {
                $fighter->fight_do_affect('perform_action',$this,$range,$tpindex,$tgindex,$tcindex,$action_list);
                //If this was an item use, consume if it it is not unlimited use
                if($fighter->command==2)
                    {
                    if($GLOBALS['items'][$fighter->inventory[$fighter->using]['item']]->one_use)
                        {
                        $fighter->remove_item($fighter->using,1);
                        $action_list[]=array('UseItem',array($fighter->using));
                        }
                    }
                }
            }
        return $action_list;
        }

    function generate_participant_list()
        {
        //Make the pool.  Exclude defenders and the dead.
        $participant_pool=array();
        foreach(array_keys($this->parties) as $pindex)
            {
            $party=&$this->parties[$pindex];
            foreach(array_keys($party->groups) as $gindex)
                {
                $group=&$party->groups[$gindex];
                foreach(array_keys($group->characters) as $cindex)
                    {
                    $character=&$group->characters[$cindex];
                    if($character->current['HP']>0 && $character->command!=6)
                        {
                        $speed=randomize($character->get_current('Speed'));
                        if($character->command==4 || $character->command==5) //Using magic
                            {
                            $focus=randomize($character->get_current('Focus'));
                            if($focus<1) $focus=1;
                            $mp_summoned=sqrt($focus);
                            $mp_used=$GLOBALS['abilities'][$character->abilities[$character->using]]->mp_used;
                            $time_needed_to_cast=$mp_used/$mp_summoned;
                            if($time_needed_to_cast>1) $time_needed_to_cast=1;
                            $speed-=$time_needed_to_cast;
                            }
                        //If a participant uses a weapon that allows for multiple attacks,
                        //that participant will have more than one appearence.
                        if($character->command==0 && !is_null($character->equipment['lhand'])) //Right hand attack.
                            $times=$GLOBALS['items'][$character->inventory[$character->equipment['lhand']]['item']]->attack_count;
                        elseif($character->command==1 && !is_null($character->equipment['rhand'])) //Left hand attack.
                            $times=$GLOBALS['items'][$character->inventory[$character->equipment['rhand']]['item']]->attack_count;
                        else
                            $times=1;
                        for($count=1;$count<=$times;$count++)
                            $participant_pool[floor($speed*$count/$times)][]="$pindex.$gindex.$cindex";
                        }
                    }
                }
            }
        krsort($participant_pool);

        //The pool is now sorted from fastest to slowest.
        //Make the participants list.
        $participants=array();
        foreach($participant_pool as $speed=>$rung)
            {
            //A rung has characters that had the same speed rating.  Randomly
            //insert them into the list.
            shuffle($rung);
            foreach($rung as $index)
                $participants[]=$index;
            }

        return $participants;
        }

    function dump()
        {
        return;
        foreach(array_keys($this->parties) as $pindex)
            {
            $party=&$this->parties[$pindex];
            $GLOBALS['output'].= "{$party->name}<br>";
            foreach(array_keys($party->groups) as $gindex)
                {
                $group=&$party->groups[$gindex];
                $GLOBALS['output'].= "--->{$group->name}<br>";
                foreach(array_keys($group->characters) as $cindex)
                   {
                   $character=&$group->characters[$cindex];
                   $GLOBALS['output'].= "------->{$character->name} ({$character->current['HP']}/{$character->base['HP']}) ";
                   if($character->current['HP']>0 && $character->command!=6)
                       $GLOBALS['output'].= " alive<br>";
                   else
                       $GLOBALS['output'].= " dead<br>";
                   }
                }
            }
        $GLOBALS['output'].= "<br>";
        }

    function test_own_party_dead($party)
        {
        return $this->parties[$party]->is_dead();
        }

    function test_other_parties_dead($party)
        {
        foreach($this->parties as $index=>$aparty)
            if ($index!=$party && !$aparty->is_dead())
                return false;
        return true;
        }

    function count_live_parties()
        {
        $sum=0;
        foreach($this->parties as $index=>$aparty)
            if(!$aparty->is_dead())
                $sum++;
        return $sum;
        }

    function affect_all_parties($callback,&$fighter,$command,$pindex,$gindex,$cindex,&$action_list)
        {
        foreach(array_keys($this->parties) as $tpindex)
            {
            $party=&$this->parties[$tpindex];
            $party->affect_party($callback,$fighter,$command,$pindex,$gindex,$cindex,$tpindex,$action_list);
            }
        }

    function affect_party($callback,&$fighter,$command,$pindex,$gindex,$cindex,$tpindex,&$action_list)
        {
        if (array_key_exists($tpindex,$this->parties))
            $this->parties[$tpindex]->affect_party($callback,$fighter,$command,$pindex,$gindex,$cindex,$tpindex,$action_list);
        }

    function affect_group($callback,&$fighter,$command,$pindex,$gindex,$cindex,$tpindex,$tgindex,&$action_list)
        {
        if (array_key_exists($tpindex,$this->parties))
            $this->parties[$tpindex]->affect_group($callback,$fighter,$command,$pindex,$gindex,$cindex,$tpindex,$tgindex,$action_list);
        }

    function affect_group_range($callback,&$fighter,$command,$pindex,$gindex,$cindex,$range,$tpindex,$tgindex,$tcindex,&$action_list)
        {
        if (array_key_exists($tpindex,$this->parties))
            $this->parties[$tpindex]->affect_group_range($callback,$fighter,$command,$pindex,$gindex,$cindex,$range,$tpindex,$tgindex,$tcindex,$action_list);
        }

    function get_character_list($living,$parties)
        {
        $retval=array();
        foreach(array_keys($parties) as $index)
            {
            $result=$this->parties[$index]->get_character_list($living);
            foreach($result as $char)
                $retval[]="$index;$result";
            }
        return $retval;
        }

    function get_character_key(&$character)
        {
        foreach(array_keys($this->parties) as $index)
            {
            $result=$this->parties[$index]->get_character_key($character);
            if(!is_null($result))
                return "$index;$result";
            }
        return null;
        }

    function &get_character($key)
        {
        if(!strpos($key,';'))
            log_error("Bogus key $key");
        list($party,$sub_key)=explode(';',$key,2);
        return $this->parties[$party]->get_character($sub_key);
        }

    function get_group_list($living,$parties)
        {
        $retval=array();
        foreach($parties as $index)
            {
            $result=$this->parites[$index]->get_group_list($living);
            foreach($result as $char)
                $retval[]="$index;$result";
            }
        return $retval;
        }
    /*
    function add_event_to_exp_tree($fighter,$target,$type,$before,$after,$whole,$pxp)
        {
        list($party,$group,$character)=explode(';',$fighter);
        list($tparty,$tgroup,$tcharacter)=explode(';',$target);
        $good_thing=$after>$before;
        $bad_thing=$before>$after;
        switch($type)
            {
            case 'direct': //Damage inflicting, healing, MP restoring
                if($good_thing&&in_array($tparty,get_non_enemy_parties($this)
                    ||$bad_thing&&in_array($tparty,get_non_ally_parties($this)))
                    $this->exp_tree[$tparty][$tgroup][$tcharacter][]=array($fighter,abs($before-$after)/$whole);
                break;
            case 'indirect': //Altering stats such as Strength, causing status ailments like sleep
                if($good_thing&&in_array($tparty,get_non_enemy_parties($this)
                    ||$bad_thing&&in_array($tparty,get_non_ally_parties($this)))
                    $this->exp_tree[$tparty][$tgroup][$tcharacter][]=array($fighter,abs($before-$after)/$before,$pxp);
                break;
            }
        }
    */

    function distribute_party_xp($exiting_party, $parties_already_exited, &$exp_debts, &$exp_credits)
        {
        foreach(array_keys($this->parties) as $party)
            if (!in_array($party, $parties_already_exited))
                foreach(array_keys($this->parties[$party]->groups) as $group)
                    {
                    $non_allies=$this->parties[$party]->get_non_ally_parties($this);
                    $non_enemies=$this->parties[$party]->get_non_enemy_parties($this);
                    foreach(array_keys($this->parties[$party]->groups[$group]->characters) as $character)
                        foreach($this->parties[$party]->groups[$group]->characters[$character]->xp_debts as $debt)
                            {
                            list($tparty,$tgroup,$tcharacter)=explode(';',$debt[0]);
                            //Only give credit for good actions
                            #Attacking non allies
                            #Healing non enemies
                            if(!in_array($tparty, $parties_already_exited)
                                && ($tparty == $exiting_party || $party == $exiting_party)
                                && (($debt[1]>0 && in_array($tparty,$non_allies))
                                || ($debt[1]<0 && in_array($tparty,$non_enemies))))
                                {
                                $xp=round(abs($debt[1])*$debt[2]*$exp_debts[$party][$group][$character][1]);
                                $exp_credits[$tparty]['total']+=$xp;
                                $exp_credits[$tparty][$tgroup][$tcharacter]+=$xp;
                                #$target=&$this->get_character("$party;$group;$character");
                                #$fighter=&$this->get_character("$tparty;$tgroup;$tcharacter");
                                #echo "{$fighter->name} earned $xp of credit from {$target->name} ($debt[1]/$debt[2]).<br>";
                                }
                            }
                    }
        }

    function process_exp_tree()
        {
        //This function does a few things:
        //When a party is completely destroyed, all experience is distributed
        //that this party gave and took.
        //That party is then removed from this fight.
        //The fight is over when there is only one party left.

        //Step one- calculate the exp debt and credit trees
        //Initialize the debts tree
        $exp_debts=array();
        foreach(array_keys($this->parties) as $party)
            foreach(array_keys($this->parties[$party]->groups) as $group)
                foreach(array_keys($this->parties[$party]->groups[$group]->characters) as $character)
                    {
                    $exp_debts[$party][$group][$character]=array(0,1);
                    //Calculate the debts
                    foreach($this->parties[$party]->groups[$group]->characters[$character]->xp_debts as $debt)
                        $exp_debts[$party][$group][$character][0]+=abs($debt[1]);
                    //Now if there is an overage [0]>1 then set [1]=1/[0]
                    if($exp_debts[$party][$group][$character][0]>1)
                        $exp_debts[$party][$group][$character][1]=1.0/$exp_debts[$party][$group][$character][0];
                    }
        #log_error(print_r($exp_debts, true));

        //Initialize the credits tree
        $exp_credits=array();
        foreach(array_keys($this->parties) as $party)
            {
            $exp_credits[$party]['total']=0;
            $exp_credits[$party]['count']=0;
            foreach(array_keys($this->parties[$party]->groups) as $group)
                foreach(array_keys($this->parties[$party]->groups[$group]->characters) as $character)
                    {
                    $exp_credits[$party][$group][$character]=0;
                    $exp_credits[$party]['count']++;
                    }
            }

        //Step two: look for dead parties
        $winners = array_keys($this->parties);
        $losers=array();
        foreach(array_keys($this->parties) as $party)
            if($this->test_own_party_dead($party))
                {
                //This party is dead.  Process and distribute all exp to and from this party.
                $this->distribute_party_xp($party, $losers, $exp_debts, $exp_credits);
                //This party is a loser, and will not have their exp pulled from future passes.
                $losers[]=$party;
                //Remove it from the winner's circle.
                $winners = array_filter($winners, create_function('$a',"return \$a!=$party;"));
                }
        #log_error(print_r($exp_credits, true));
        #log_error('winners:'.print_r($winners, true));
        #log_error('losers:'.print_r($losers, true));

        //Step three: If all paries alive are not enemies, then end this fight.
        $all_friends = true;
        foreach($winners as $party)
            {
            $allies = $this->parties[$party]->get_ally_parties($this);
            $allies[] = $party;
            if (count(array_intersect($winners, $allies)) != count($winners))
                $all_friends = false;
            }
        if ($all_friends)
            foreach($winners as $party)
                {
                //This party is dead.  Process and distribute all exp to and from this party.
                $this->distribute_party_xp($party, $losers, $exp_debts, $exp_credits);
                //Treat party is a loser, and will not have their exp pulled from future passes.
                $losers[]=$party;
                }

        //Step four: award the experience points
        foreach(array_keys($this->parties) as $party)
            {
            $team_count=count($this->parties[$party]->teams);
            //Only award xp if there was a team involved.
            if($team_count>0)
                {
                //XP for being a part of the party.
                $party_shared_xp=round($exp_credits[$party]['total']*.2/$exp_credits[$party]['count']);
                //XP each team splits between members
                $team_shared_xp=array();
                foreach($this->parties[$party]->teams as $teamid=>$team)
                    $team_count_shared_xp[$teamid]=round($exp_credits[$party]['total']*.3/count($team));
                foreach(array_keys($this->parties[$party]->groups) as $group)
                    foreach(array_keys($this->parties[$party]->groups[$group]->characters) as $character)
                        {
                        //Get this character
                        $fighter=&$this->get_character("$party;$group;$character");
                        //Find the team this character belongs to.
                        $this_teamid=false;
                        foreach($this->parties[$party]->teams as $teamid=>$team)
                            if(in_array($fighter->charid,$team))
                                $this->teamid=$teamid;
                        //The character gets 50% of his xp+ the party shared xp
                        $shared_xp_earned=$party_shared_xp;
                        //If a member of the team, bonus with team xp
                        if($this_teamid!==false)
                            $shared_xp_earned+=round($exp_credits[$party][$group][$character]/2)+
                                $team_shared_xp[$this_teamid];
                        //The character gets 50% of his xp+ the party shared xp
                        $xp_earned=round($exp_credits[$party][$group][$character]/2)+$shared_xp_earned;
                        //Add the XP.
                        $fighter->exp+=$xp_earned;
                        #echo "{$fighter->name} earned {$xp_earned} XP ({$shared_xp_earned} shared).<br>";
                        }
                }
            }

        //Step five: discharge the dead parties
        foreach(array_keys($this->parties) as $party)
            if($this->test_own_party_dead($party))
                unset($this->parties[$party]);

        //return true if we have one (or none) live parties left.
        return ($all_friends || count($winners)<2);
        }
    }
?>
