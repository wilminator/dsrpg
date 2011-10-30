<?php
require_once INCLUDE_DIR.'team.php';
require_once INCLUDE_DIR.'mysql.php';
require_once INCLUDE_DIR.'character_store.php';

class TEAM_STORE
    {
    //This constructor (tries to) initialize the team table.
    function TEAM_STORE($reset=false)
        {
        $result=mysql_do_query("select count(*) from teams",false);
        if($result===false ||($data=mysql_fetch_row($result))===false || $data[0]==0 || $reset===true)
            {
            if (!$reset)
                {
                log_error('YamIhere?');
                exit;
                }
            //Delete tables
            mysql_do_query("DROP TABLE IF EXISTS teams");
            mysql_do_query("DROP TABLE IF EXISTS team_characters");
            mysql_do_query("DROP TABLE IF EXISTS team_quests");
            mysql_do_query("DROP TABLE IF EXISTS team_relations");
            //Recreate tables.
            mysql_do_query("
CREATE TABLE `teams` (
  `teamid` int(11) unsigned NOT NULL auto_increment,
  `playerid` int(11) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `gold` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`teamid`)
) ;");
            mysql_do_query("
CREATE TABLE `team_characters` (
  `teamid` int(11) unsigned NOT NULL default '0',
  `characterid` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`teamid`,`characterid`)
) ;");
            mysql_do_query("
CREATE TABLE `team_quests` (
  `teamid` int(11) unsigned NOT NULL default '0',
  `quest` varchar(64) NOT NULL default '',
  `prestige` int(11) NOT NULL default '0',
  `mark` varchar(32) default NULL
) ;");
            mysql_do_query("
CREATE TABLE `team_relations` (
  `teamid` int(11) unsigned NOT NULL default '0',
  `otherteamid` int(4) unsigned NOT NULL default '0',
  `state` enum('allied','neutral','ememies') NOT NULL default 'neutral',
  PRIMARY KEY  (`teamid`,`otherteamid`)
) ;");
            }
        }

    function &get_team($index,$use_ids=false)
        {
        $query="select * from teams where teamid=$index";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);
        if($data===false)
            log_error("team $index does not exist in the database.");

        //Get characters
        $char_store=new CHARACTER_STORE();
        $query="select characterid from team_characters where teamid=$index";
        $result=mysql_do_query($query);
        $characters=array();
        while($data2=mysql_fetch_assoc($result))
            if($use_ids)
                $characters[]=$data2['characterid'];
            else
                $characters[]=$char_store->get_character($data2['characterid']);

        //Get quests
        $query="select quest,prestige,mark from team_quests where teamid=$index";
        $result=mysql_do_query($query);
        $quests=array();
        while($data2=mysql_fetch_assoc($result))
            $quests[]=$data2;

        //Get relations
        $query="select otherteamid teamid,state from team_relations where teamid=$index";
        $result=mysql_do_query($query);
        $relations=array();
        while($data2=mysql_fetch_assoc($result))
            $relations[$data2['teamid']]=$data2['state'];
        /*
        $query="select teamid,state from team_relations where otherteamid=$index";
        $result=mysql_do_query($query);
        while($data2=mysql_fetch_assoc($result))
            $relations[$data2['teamid']]=$data2['state'];
        */
        $team=new TEAM;
        $team->build_team(
            $data['teamid'],$data['playerid'],$data['name'],$data['gold'],
            $characters,$quests,$relations);
        return $team;
        }

    function &get_all_teams($use_ids=false)
        {
        $teams=array();
        $query="select teamid from teams";
        $result=mysql_do_query($query);
        while(($data=mysql_fetch_row($result))!==false)
            $teams[$data[0]]=&$this->get_team($data[0]);
        return $teams;
        }

    function &get_all_teams_by_playerid($playerid)
        {
        $teams=array();
        $query="select teamid from teams where playerid=$playerid";
        $result=mysql_do_query($query);
        while(($data=mysql_fetch_row($result))!==false)
            $teams[$data[0]]=&$this->get_team($data[0]);
        return $teams;
        }

    function set_team(&$team,$use_ids=false)
        {
        $index=(is_null($team->teamid)?0:$team->teamid);
        $name=mysql_real_escape_string($team->name);
        $query="
        replace into teams
            (teamid,playerid,name,gold)
        values
            ($index,{$team->playerid},'$name',{$team->gold})";
        mysql_do_query($query);
        if($index==0)
            {
            $index=mysql_insert_id();
            $team->teamid=$index;
            }

        //Fix characters
        mysql_do_query("delete from team_characters where teamid=$index");
        if(count($team->characters)>0)
            {
            $characters=array();
            if($use_ids)
                {
                foreach(array_keys($team->characters) as $charid)
                    {
                    $characters[]="($index,$charid)";
                    }
                }
            else
                {
                $char_store=new CHARACTER_STORE();
                foreach(array_keys($team->characters) as $cindex)
                    {
                    $char_store->set_character($team->characters[$cindex]);
                    $characters[]="($index,{$team->characters[$cindex]->charid})";
                    }
                }
            $query="
            replace into team_characters
                (teamid,characterid)
            values ".implode(',',$characters);
            mysql_do_query($query);
            }

        //Fix quests
        mysql_do_query("delete from team_quests where teamid=$index");
        if(count($team->quests)>0)
            {
            $quests=array();
            foreach($team->quests as $quest)
                $quests[]="($index,$quest[quest],$quest[prestige],$quest[mark]})";
            $query="
            replace into team_quests
                (teamid,quest,reputation,mark)
            values ".implode(',',$quests);
            mysql_do_query($query);
            }

        //Fix relations
        mysql_do_query("delete from team_relations where teamid=$index");
        if(count($team->relations)>0)
            {
            $relations=array();
            foreach($team->relations as $otherteam=>$status)
                $relations[]="($index,$otherteam,$status)";
            $query="
            replace into team_relations
                (teamid,otherteamid,state)
            values ".implode(',',$relations);
            mysql_do_query($query);
            }

        return $index;
        }

    function delete_team($index)
        {
        //Purge team characters
        $char_store=new CHARACTER_STORE();
        $query="SELECT characterid FROM team_characters WHERE teamid=$index";
        $result=mysql_do_query($query);
        while(($data=mysql_fetch_assoc($result))!==false)
            $char_store->delete_character($data['characterid']);

        //Purge team data
        mysql_do_query("delete from teams where teamid=$index");
        mysql_do_query("delete from team_characters where teamid=$index");
        mysql_do_query("delete from team_quests where teamid=$index");
        mysql_do_query("delete from team_relations where teamid=$index");
        }
    }
?>
