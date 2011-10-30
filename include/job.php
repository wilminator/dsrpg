<?php
/**
 * job.php
 * jobs/classes object
 * @version 0.1.0
 * @copyright 2003 Mike Wilmes
 **/

require_once INCLUDE_DIR.'array.php';
require_once INCLUDE_DIR.'character.php';

require_once INCLUDE_DIR.'js_rip.php';

function jobs_to_js()
    {
    global $jobs;
    return php_data_to_js($GLOBALS['jobs']);
    }

class JOB
    {
    var $name;
    var $need;
    var $upgrade;
    var $abilities;

    function JOB($name,$need,$upgrade,$abilities)
        {
        global $character_stats;
        $this->name=$name;
        $this->need=$need;
        $this->upgrade=create_array($character_stats,$upgrade,0);
        $this->abilities=$abilities;
        }

    function don(&$character)
        {
        if($character->level==0)
            {
            foreach($character->base as $index=>$val)
                {
                $character->base[$index]=round($this->upgrade[$index]*(90+rand(0,55-$this->upgrade[$index]))/100.0);
                $character->current[$index]=$character->base[$index];
                }
            }
        else
            {
            $perc=$character->level/100.0;
            foreach($character->base as $index=>$val)
                {
                $character->base[$index]=round($character->base[$index]*$perc);
                $character->current[$index]=round($character->current[$index]*$perc);
                }
            }
        $character->level=1;
        $character->exp=0;
        $character->need=$this->need;
        $this->grant_abilities($character);
        $character->reset_stats(true);
        }

    function level(&$character)
        {
        foreach($character->base as $index=>$val)
            $character->base[$index]+=floor($this->upgrade[$index]*(75+rand(0,25))/100.0);
        $character->level++;
        $retval=$this->grant_abilities($character);
        $character->reset_stats();
        
        return $retval;
        }

    function grant_abilities(&$character)
        {
        $retval=array();
        //Add earned abilities
        foreach($this->abilities as $data)
            if($character->level>=$data['level'])
                if($character->add_ability($data['ability']))
                    {
                    $retval[]="{$character->name} has learned {$GLOBALS['abilities'][$data['ability']]->name}.";
                    }
        uasort($character->abilities,'ability_sort');
        return $retval;
        }

    function describe_stats()
        {
        $response=array();
        foreach($this->upgrade as $index=>$stat)
            if($stat!=0)
                $response[]="$index $stat";
        return implode(' ',$response);
        }
    }
?>
