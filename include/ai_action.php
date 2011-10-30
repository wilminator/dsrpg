<?php
require_once INCLUDE_DIR.'js_rip.php';

function ai_action_select(&$fight,$pindex,$gindex,$cindex,&$target_list)
    {
    arsort($target_list);
    ai_target($fight,$pindex,$gindex,$cindex,$target_list);
    //Log the target list.
    //log_error('Selecting from target list:'.php_data_to_js($target_list));
    $target_value=reset($target_list);
    $target_list=array_keys($target_list,$target_value);
    $choice=array_rand($target_list);
    //Log the selected choice.
    //log_error('Target:'.php_data_to_js($target_list[$choice]));
    return $target_list[$choice];
    }

function ai_action_stupid(&$fight,$pindex,$gindex,$cindex)
    {
    //echo "ai_stupid";
    $fighter=&$fight->get_character("$pindex;$gindex;$cindex");
    #Pick a command and a sub command, if needed
    //Add attack options
    $command_options=array(array(1,0));
    if(is_null($fighter->equipment['lhand']) || $fighter->equipment['lhand']!=$fighter->equipment['rhand'])
        $command_options[]=array(0,0);
    //Add ability options
    foreach($fighter->abilities as $index=>$ability)
        $command_options[]=array($GLOBALS['abilities'][$ability]->type==0?5:4,$index);
    $choice=array_rand($command_options);
    list($command,$subcommand)=$command_options[$choice];
    #Collect the values.
    $values=ai_process_command_on_target($fight,$fighter,$pindex,$command,$subcommand);
    #Now convert the values list to a target list.
    //$range=ai_get_range($fighter,$command,$subcommand);
    //var_dump($values);
    $target_list=ai_combine_on_range($command,$subcommand,0,$values);
    $retval=ai_action_select($fight,$pindex,$gindex,$cindex,$target_list);
    //var_dump($retval);
    return $retval;
    }

function ai_find_party_weak_links(&$fight,&$fighter,$pindex,$min_ratio)
    {
    $weak_links=array();
    foreach(array_keys($fight->parties[$pindex]->groups) as $tgindex)
        foreach(array_keys($fight->parties[$pindex]->groups[$tgindex]->characters) as $tcindex)
            {
            $character=&$fight->parties[$pindex]->groups[$tgindex]->characters[$tcindex];
            $ratio=$character->get_current('HP')*100/$character->get_base('HP');
            if($ratio>0 && $ratio<=$min_ratio)
                $weak_links[]="$pindex;$tgindex;$tcindex";
            }
    return $weak_links;
    }

function ai_find_healing_commands(&$fighter)
    {
    $healing_commands=array();
    #Look thru items
    foreach($fighter->inventory as $index=>$item)
        if($item['qty']>0 && $GLOBALS['items'][$item['item']]->effect==1)
            $healing_commands[]=array(2,$index);
    #Look thru abilities, checking for MP
    foreach($fighter->abilities as $index=>$ability)
        {
        if($GLOBALS['abilities'][$ability]->effect==1)
            if($GLOBALS['abilities'][$ability]->mp_used<=$fighter->get_current('MP'))
                $healing_commands[]=array($GLOBALS['abilities'][$ability]->type==0?5:4,$index);
        }
    return $healing_commands;
    }

function ai_find_damaging_commands(&$fighter)
    {
    $damaging_commands=array();
    #Look thru abilities, checking for MP
    foreach($fighter->abilities as $index=>$ability)
        {
        if($GLOBALS['abilities'][$ability]->effect==2)
            if($GLOBALS['abilities'][$ability]->mp_used<=$fighter->get_current('MP'))
                $damaging_commands[]=array($GLOBALS['abilities'][$ability]->type==0?5:4,$index);
        }
    #Look thru items
    foreach($fighter->inventory as $index=>$item)
        if($item['qty']>0 && $GLOBALS['items'][$item['item']]->effect==2)
            $damaging_commands[]=array(2,$index);
    return $damaging_commands;
    }

function ai_find_spell_commands(&$fighter)
    {
    $commands=array();
    #Look thru abilities, checking for MP
    foreach($fighter->abilities as $index=>$ability)
        {
        if($GLOBALS['abilities'][$ability]->type==0)
            if($GLOBALS['abilities'][$ability]->mp_used<=$fighter->get_current('MP'))
                $commands[]=array(5,$index);
        }
    return $commands;
    }

function ai_find_skill_commands(&$fighter)
    {
    $commands=array();
    #Look thru abilities, checking for MP
    foreach($fighter->abilities as $index=>$ability)
        {
        if($GLOBALS['abilities'][$ability]->type==1)
            if($GLOBALS['abilities'][$ability]->mp_used<=$fighter->get_current('MP'))
                $commands[]=array(4,$index);
        }
    return $commands;
    }

function ai_find_item_commands(&$fighter)
    {
    $commands=array();
    #Look thru items
    foreach($fighter->inventory as $index=>$item)
        if($item['qty']>0 && $GLOBALS['items'][$item['item']]->effect!=0)
            $commands[]=array(2,$index);
    return $commands;
    }

function ai_action_normal(&$fight,$pindex,$gindex,$cindex)
    {
    //echo "ai_normal";
    $fighter=&$fight->get_character("$pindex;$gindex;$cindex");

    #Pick a command and a sub command, if needed
    //For Normal, check to see if any party members are at 10% or less life.
    $weak_links=ai_find_party_weak_links($fight,$fighter,$pindex,10);
    //log_error('Weak Links:'.php_data_to_js($weak_links));
    //Also look for healing commands.
    $healing_commands=ai_find_healing_commands($fighter);
    //log_error('Healing Commands:'.php_data_to_js($healing_commands));
    //If we have weakened party members and healing ability, then heal
    if(count($weak_links)>0 && count($healing_commands)>0)
        $command_options=$healing_commands;
    else
        {
        //Add attack options
        $command_options=array(array(1,0));
        if(is_null($fighter->equipment['lhand']) || $fighter->equipment['lhand']!=$fighter->equipment['rhand'])
            $command_options[]=array(0,0);
        //Add ability options
        foreach($fighter->abilities as $index=>$ability)
            if($GLOBALS['abilities'][$ability]->mp_used<=$fighter->get_current('MP'))
                $command_options[]=array($GLOBALS['abilities'][$ability]->type==0?5:4,$index);
        }

    #Pick a command
    $choice=array_rand($command_options);
    list($command,$subcommand)=$command_options[$choice];

    #Get the command's range
    $range=ai_get_range($fighter,$command,$subcommand);

    #Collect the values.
    if(count($weak_links)>0 && count($healing_commands)>0)
        $values=ai_process_command_on_selected_targets($fight,$fighter,$pindex,$command,$subcommand,$range,$weak_links);
    else
        $values=ai_process_command_on_target($fight,$fighter,$pindex,$command,$subcommand);

    #Now convert the values list to a target list.
    //log_error('Values dump:'.print_r($values, true));
    //log_error('Range dump:'.print_r($range, true));
    $target_list=ai_combine_on_range($command,$subcommand,$range,$values);
    $retval=ai_action_select($fight,$pindex,$gindex,$cindex,$target_list);
    //log_error('Retval dump:'.print_r($retval, true));
    return $retval;
    }

function ai_action_healer(&$fight,$pindex,$gindex,$cindex)
    {
    //echo "ai_healer";
    $fighter=&$fight->get_character("$pindex;$gindex;$cindex");

    #Pick a command and a sub command, if needed
    //Look for healing commands.
    $healing_commands=ai_find_healing_commands($fighter);
    //If there are no healing commands, then return ai_normal
    if(count($healing_commands)==0)
        return ai_action_normal($fight,$pindex,$gindex,$cindex);

    //For Healer, check to see if any party members are at 50% or less life.
    $weak_links=ai_find_party_weak_links($fight,$fighter,$pindex,50);

    //While we are at it, look for non-party allies that are <10%
    foreach($fight->parties[$pindex]->allies as $index)
        $weak_links=array_merge($weak_links,ai_find_party_weak_links($fight,$fighter,$index,10));

    //If there are no weak links then return ai_normal
    if(count($weak_links)==0)
        return ai_action_normal($fight,$pindex,$gindex,$cindex);

    #Pick a command
    $choice=array_rand($healing_commands);
    list($command,$subcommand)=$command_options[$choice];

    #Get the command's range
    $range=ai_get_range($fighter,$command,$subcommand);

    #Collect the values.
    $values=ai_process_command_on_selected_targets($fight,$fighter,$pindex,$command,$subcommand,$range,$weak_links);

    #Now convert the values list to a target list.
    //var_dump($range);
    $target_list=ai_combine_on_range($command,$subcommand,$range,$values);
    $retval=ai_action_select($fight,$pindex,$gindex,$cindex,$target_list);
    //var_dump($retval);
    return $retval;
    }

function ai_action_pummeler(&$fight,$pindex,$gindex,$cindex)
    {
    //echo "ai_pummeler";
    $fighter=&$fight->get_character("$pindex;$gindex;$cindex");

    #Pick a command and a sub command, if needed
    //Look for damaging commands.
    $damaging_commands=ai_find_damaging_commands($fighter);

    if(count($damaging_commands)>0)
        $command_options=$damaging_commands;
    else
        {
        //Add attack options
        $command_options=array(array(1,0));
        if(is_null($fighter->equipment['lhand']) || $fighter->equipment['lhand']!=$fighter->equipment['rhand'])
            $command_options[]=array(0,0);
        //Add ability options
        foreach($fighter->abilities as $index=>$ability)
            if($GLOBALS['abilities'][$ability]->mp_used<=$fighter->get_current('MP'))
                $command_options[]=array($GLOBALS['abilities'][$ability]->type==0?5:4,$index);
        }

    #Pick a command
    $choice=array_rand($command_options);
    list($command,$subcommand)=$command_options[$choice];

    #Get the command's range
    $range=ai_get_range($fighter,$command,$subcommand);

    #Collect the values.
    $values=ai_process_command_on_target($fight,$fighter,$pindex,$command,$subcommand);

    #Now convert the values list to a target list.
    //var_dump($range);
    $target_list=ai_combine_on_range($command,$subcommand,$range,$values);
    $retval=ai_action_select($fight,$pindex,$gindex,$cindex,$target_list);
    //var_dump($retval);
    return $retval;
    }

function ai_action_fighter(&$fight,$pindex,$gindex,$cindex)
    {
    //echo "ai_fighter";
    $fighter=&$fight->get_character("$pindex;$gindex;$cindex");

    #Pick a command and a sub command, if needed
    //For Normal, check to see if any party members are at 10% or less life.
    $weak_links=ai_find_party_weak_links($fight,$fighter,$pindex,10);
    //Also look for healing commands.
    $healing_commands=ai_find_healing_commands($fighter);

    if(count($weak_links)>0 && count($healing_commands)>0)
        $command_options=$healing_commands;
    else
        {
        //Add attack options
        $command_options=array(array(1,0));
        if(is_null($fighter->equipment['lhand']) || $fighter->equipment['lhand']!=$fighter->equipment['rhand'])
            $command_options[]=array(0,0);
        //Add ability options
        foreach($fighter->abilities as $index=>$ability)
            if($GLOBALS['abilities'][$ability]->mp_used<=$fighter->get_current('MP'))
                $command_options[]=array($GLOBALS['abilities'][$ability]->type==0?5:4,$index);
        }

    #Pick a command
    $choice=array_rand($command_options);
    list($command,$subcommand)=$command_options[$choice];

    #Get the command's range
    $range=ai_get_range($fighter,$command,$subcommand);

    #Collect the values.
    if(count($weak_links)>0 && count($healing_commands)>0)
        $values=ai_process_command_on_selected_targets($fight,$fighter,$pindex,$command,$subcommand,$range,$weak_links);
    else
        $values=ai_process_command_on_target($fight,$fighter,$pindex,$command,$subcommand);

    #Now convert the values list to a target list.
    //var_dump($range);
    $target_list=ai_combine_on_range($command,$subcommand,$range,$values);
    //Cut the target list down by 50%
    cut_target_list($target_list,50);
    $retval=ai_action_select($fight,$pindex,$gindex,$cindex,$target_list);
    //var_dump($retval);
    return $retval;
    }

function ai_action_caster(&$fight,$pindex,$gindex,$cindex)
    {
    //echo "ai_caster";
    $fighter=&$fight->get_character("$pindex;$gindex;$cindex");

    #Pick a command and a sub command, if needed
    //Look for spell commands.
    $commands=ai_find_spell_commands($fighter);
    //If there were no spells, look for skills.
    if (count($commands)==0)
        $commands=ai_find_skill_commands($fighter);
    //If there were no skills, look for items.
    if (count($commands)==0)
        $commands=ai_find_item_commands($fighter);
    //If there are command options, then use them.  Otherwise attack.
    if(count($commands)>0)
        $command_options=$commands;
    else
        {
        //Add attack options
        $command_options=array(array(1,0));
        if(is_null($fighter->equipment['lhand']) || $fighter->equipment['lhand']!=$fighter->equipment['rhand'])
            $command_options[]=array(0,0);
        //Add ability options
        foreach($fighter->abilities as $index=>$ability)
            if($GLOBALS['abilities'][$ability]->mp_used<=$fighter->get_current('MP'))
                $command_options[]=array($GLOBALS['abilities'][$ability]->type==0?5:4,$index);
        }

    #Pick a command
    $choice=array_rand($command_options);
    list($command,$subcommand)=$command_options[$choice];

    #Get the command's range
    $range=ai_get_range($fighter,$command,$subcommand);

    #Collect the values.
    $values=ai_process_command_on_target($fight,$fighter,$pindex,$command,$subcommand);

    #Now convert the values list to a target list.
    //var_dump($range);
    $target_list=ai_combine_on_range($command,$subcommand,$range,$values);
    $retval=ai_action_select($fight,$pindex,$gindex,$cindex,$target_list);
    //var_dump($retval);
    return $retval;
    }

function ai_action_mage(&$fight,$pindex,$gindex,$cindex)
    {
    //echo "ai_mage";
    $fighter=&$fight->get_character("$pindex;$gindex;$cindex");

    #Pick a command and a sub command, if needed
    //Look for spell commands.
    $commands=ai_find_spell_commands($fighter);
    //If there were no spells, look for skills.
    if (count($commands)==0)
        $commands=ai_find_skill_commands($fighter);
    //If there were no skills, look for items.
    if (count($commands)==0)
        $commands=ai_find_item_commands($fighter);
    //If there are command options, then use them.  Otherwise attack.
    if(count($commands)>0)
        $command_options=$healing_commands;
    else
        {
        //Add attack options
        $command_options=array(array(1,0));
        if(is_null($fighter->equipment['lhand']) || $fighter->equipment['lhand']!=$fighter->equipment['rhand'])
            $command_options[]=array(0,0);
        //Add ability options
        foreach($fighter->abilities as $index=>$ability)
            $command_options[]=array($GLOBALS['abilities'][$ability]->type==0?5:4,$index);
        }

    #Pick a command
    $choice=array_rand($command_options);
    list($command,$subcommand)=$command_options[$choice];

    #Get the command's range
    $range=ai_get_range($fighter,$command,$subcommand);

    #Collect the values.
    $values=ai_process_command_on_target($fight,$fighter,$pindex,$command,$subcommand);

    #Now convert the values list to a target list.
    //var_dump($range);
    $target_list=ai_combine_on_range($command,$subcommand,$range,$values);
    //Cut the target list down by 50%
    cut_target_list($target_list,50);
    $retval=ai_action_select($fight,$pindex,$gindex,$cindex,$target_list);
    //var_dump($retval);
    return $retval;
    }

function ai_action_sharp(&$fight,$pindex,$gindex,$cindex)
    {
    //echo "ai_sharp";
    if(mt_rand(1,100)<=50)
        return ai_action_fighter($fight,$pindex,$gindex,$cindex);
    return ai_action_mage($fight,$pindex,$gindex,$cindex);
    }

?>
