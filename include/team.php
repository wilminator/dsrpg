<?php
/**
 * team.php
 * defines teams and creates team functionality
 * @version 0.1.0
 * @copyright 2003 Mike Wilmes
 **/

require_once INCLUDE_DIR.'character.php';
require_once INCLUDE_DIR.'team_store.php';

require_once INCLUDE_DIR.'js_rip.php';

class TEAM
    {
    var $characters;
    var $name;
    var $teamid;
    var $quests;
    var $marks;
    var $gold;
    var $relations;
    var $playerid;

    function TEAM()
        {
        $this->characters=array();
        $this->name='';
        $this->teamid=null;
        $this->gold=0;
        $this->quests=array();
        $this->relations=array();
        $this->playerid=null;
        }

    function build_team($teamid,$playerid,$name,$gold,$members,$quests,$relations)
        {
        $this->teamid=$teamid;
        $this->playerid=$playerid;
        $this->name=$name;
        $this->gold=$gold;
        $this->characters=$members;
        $this->quests=$quests;
        $this->relations=$relations;
        foreach(array_keys($this->characters) as $index)
            $this->characters[$index]->teamid=$teamid;
        }

    function add($character)
        {
        if(!is_a($character,'CHARACTER'))
            {
            var_dump($character);
            log_error("Character is not a character object");
            }
        $character->teamid=$this->teamid;
        array_push($this->characters,$character);
        return false;
        }

    function &get_character($index)
        {
        if(!isset($this->characters[$index]))
            log_error("Invalid index");
        return $this->characters[$index];
        }

    function get_team_id()
        {
        return $this->id;
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

    function get_pxp()
        {
        $pxp=0;
        foreach(array_keys($this->characters) as $character)
            $pxp+=$this->characters[$character]->pxp;
        return $pxp;
        }

    function charid_in_team($charid)
        {
        foreach(array_keys($this->characters) as $index)
            if($this->characters[$index]->charid==$charid)
                return $index;
        return false;
        }

    function store_team()
        {
        $team_store=new TEAM_STORE;
        $team_store->set_team($this);
        }

    function refresh_team()
        {
        $char_store=new CHARACTER_STORE;
        foreach(array_keys($this->characters) as $index)
            $this->characters[$index]=&$char_store->get_character($this->characters[$index]->charid);
        }
    }
?>
