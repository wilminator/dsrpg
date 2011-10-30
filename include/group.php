<?php
/**
 * group.php
 * defines groups and creates group functionality
 * @version 0.1.0
 * @copyright 2003 Mike Wilmes
 **/

require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'character.php';

require_once INCLUDE_DIR.'js_rip.php';

class GROUP
    {
    var $characters=array();
    var $name='';
    
    function add_hero($character,$position=null)
        {
        if($this->count()<GROUP_MAX_COUNT)
            {
            if(is_null($position))
                array_push($this->characters,$character);
            else
                array_splice($this->characters,$position,0,array($character));
            #Check to see if a whole team is here lead by a member(?)
            #If so, rename the group to the team name.  add "& Co." if
            #more than just team in group.
            $this->name=$this->characters[0]->name."'s group";;
            return false;
            }
        return true;
        }

    function add_monster($character)
        {
        array_push($this->characters,$character);
        return false;
        }

    function add_monsters($count,$name,$level,$base,$current,$inventory,$equipment,$abilities,$personality_js)
        {
        $this->name=$name;
        for($index=0;$index<$count;$index++)
            {
            $monster=monster($name,$level,$base,$current,$inventory,$equipment,$abilities,$personality_js);
            if($count>1)
                $monster->name=$name.'-'.chr(65+$index);
            array_push($this->characters,$monster);
            }
        }

    function remove_fighter($index)
        {
        if($index<$this->count())
            {
            array_splice($this->characters,$index,1);
            $this->characters=array_merge($this->characters);
            return false;
            }
        return true;
        }

    function count()
        {
        return count($this->characters);
        }
        
    function reset()
        {
        return reset($this->characters);
        }
        
    function each()
        {
        return each($this->characters);
        }
        
    function next()
        {
        return next($this->characters);
        }
        
    function current()
        {
        return current($this->characters);
        }

    function key()
        {
        return key($this->characters);
        }

    function to_js()
        {
        //foreach($this->characters as $index=>$character)
        //    $output[$index]=$character->to_js();
        //$output="[\n".implode(",\n",$output)."]\n";
        return php_data_to_js(get_object_vars($this));
        }

    function affect_group($callback,&$fighter,$command,$pindex,$gindex,$cindex,$tpindex,$tgindex,&$action_list)
        {
        if ($this->is_dead())
            return;
        //Present this group
        //$actions[]=array(0,'present_group',$tpindex,$tgindex);
        //Wait for animation.
        //$actions[]=array(1);

        //Do the effect animation, triggering all the above actions.
        //$fighter->animate_impact($actions,$tpindex,$tgindex,-1,-1);
        //Wait for animation.
        //$actions[]=array(1);

        foreach($this->characters as $index=>$character)
            $callback($fighter,$pindex,$gindex,$cindex,$this->characters[$index],$tpindex,$tgindex,$index,$command,1,1,$action_list);
            //$this->characters[$index]->affected_by($fighter,$tpindex,$tgindex,$index,1,1,$actions,$action_list);

        //Wait for animation.
        //$actions[]=array(1);
        }

    function affect_group_range($callback,&$fighter,$command,$pindex,$gindex,$cindex,$range,$tpindex,$tgindex,$tcindex,&$action_list)
        {
        if ($this->is_dead())
            return;
        //Present this group
        //$actions[]=array(0,'present_group',$tpindex,$tgindex);
        //Wait for animation.
        //$actions[]=array(1);

        //If range is >0, then cycle through main target then
        //    pair outward until limit is reached.
        //If range is 0, then effect character only.
        //var_dump($action_list);
        for($index=0;$index<=$range;$index++)
            {
            //Do the effect animation.
            //$fighter->animate_impact($actions,$tpindex,$tgindex,$tcindex,$index);

            if($index>0 && $tcindex-$index>=0)
                $callback($fighter,$pindex,$gindex,$cindex,$this->characters[$tcindex-$index],$tpindex,$tgindex,$tcindex-$index,$command,$index,$range,$action_list);
                //$this->characters[$tcindex-$index]->affected_by($fighter,$tpindex,$tgindex,$tcindex-$index,$index,$range,$actions,$action_list);
            if($tcindex+$index<count($this->characters))
                $callback($fighter,$pindex,$gindex,$cindex,$this->characters[$tcindex+$index],$tpindex,$tgindex,$tcindex+$index,$command,$index,$range,$action_list);
                //$this->characters[$tcindex+$index]->affected_by($fighter,$tpindex,$tgindex,$tcindex+$index,$index,$range,$actions,$action_list);
            }
        //Wait for animation.
        //$actions[]=array(1);
        //var_dump($action_list);
        //exit;
        }

    function get_pxp()
        {
        $pxp=0;
        foreach(array_keys($this->characters) as $character)
            $pxp+=$this->characters[$character]->pxp;
        return $pxp;
        }

    function is_dead()
        {
        $sum=true;
        foreach($this->characters as $index=>$character)
            $sum=($sum&&$character->is_dead());
        return $sum;
        }

    function count_living()
        {
        $sum=0;
        foreach($this->characters as $index=>$character)
            if($character->current['HP']>0)
                $sum++;
        return $sum;
        }

    function get_character_list($living)
        {
        $retval=array();
        foreach(array_keys($this->characters) as $index)
            {
            $result=!$this->characters[$index]->is_dead();
            if($living===$result || is_null($living))
                {
                $retval[]="$index";
                }
            }
        return $retval;
        }

    function get_character_key(&$character)
        {
        foreach(array_keys($this->characters) as $index)
            if($this->characters[$index]===$character)
                return "$index";
        return null;
        }

    function &get_character($key)
        {
        if(!isset($this->characters[$key]))
            return true;
        return $this->characters[$key];
        }

    function find_character($charid)
        {
        foreach(array_keys($this->characters) as $index)
            if($this->characters[$index]->charid==$charid)
                return $index;
        return true;
        }
    }
?>
