<?php
require_once INCLUDE_DIR.'fight.php';
require_once INCLUDE_DIR.'mysql.php';

class FIGHT_STORE
    {
    //This constructor (tries to) initialize the fight table.
    function FIGHT_STORE($reset=false)
        {

        $result=mysql_do_query("select count(*) from fights",false);
        if($result===false || ($data=mysql_fetch_row($result))===false || $data[0]==0 || $reset===true)
            {
            //Delete tables
            mysql_do_query("DROP TABLE IF EXISTS fights");
            mysql_do_query("DROP TABLE IF EXISTS fight_players");
            mysql_do_query("DROP TABLE IF EXISTS fight_actions");
            mysql_do_query("DROP TABLE IF EXISTS fight_saves");
            //Recreate tables.
            mysql_do_query("
CREATE TABLE `fights` (
  `fightid` int(11) unsigned NOT NULL,
  `fight_data` text NOT NULL,
  `sequence` mediumint(5) unsigned NOT NULL,
  `active` enum('yes','no','processing') NOT NULL,
  `timeout` datetime NOT NULL,
  PRIMARY KEY  (`fightid`)
) ");
            mysql_do_query("
CREATE TABLE `fight_players` (
  `fightid` int(11) unsigned NOT NULL,
  `playerid` int(11) unsigned NOT NULL,
  `teamid` int(11) unsigned NOT NULL,
  `player_party` tinyint(4) unsigned NOT NULL,
  `seen` enum('yes','no')  NOT NULL,
  `sequence` mediumint(5) unsigned NOT NULL,
  PRIMARY KEY  (`fightid`,`playerid`),
  KEY `playerid` (`playerid`)
) ");
            mysql_do_query("
CREATE TABLE `fight_actions` (
  `fightid` int(11) unsigned NOT NULL,
  `sequence` mediumint(5) unsigned NOT NULL,
  `prefight_data` text  NOT NULL,
  `action_data` text  NOT NULL,
  PRIMARY KEY  (`fightid`,`sequence`)
) ");
            mysql_do_query("
CREATE TABLE `fight_saves` (
  `fightid` int(11) unsigned NOT NULL,
  `playerid` int(11) unsigned NOT NULL,
  `type` enum('retain','user','underdog','overkill') NOT NULL,
  PRIMARY KEY  (`fightid`,`playerid`),
  KEY `playerid` (`playerid`,`type`)
) ");
            }
        }
        
    function lock_db($write=false)
        {
        if($write)
            $query="LOCK TABLES fights WRITE, fights AS f1 WRITE, fights AS f2 WRITE, fight_players WRITE, fight_actions WRITE, fight_saves WRITE";
        else
            $query="LOCK TABLES fights READ, fights AS f1 READ, fights AS f2 READ, fight_players READ, fight_actions READ, fight_saves READ";
        mysql_do_query($query);
        }

    function unlock_db()
        {
        $query="UNLOCK TABLES";
        mysql_do_query($query);
        }

    function get_fight_players($fightid)
        {
        $players=array();
        $query="
            SELECT
                playerid, teamid, player_party, seen
            FROM fight_players
            WHERE fightid=$fightid";
        $result=mysql_do_query($query);
        while($data=mysql_fetch_assoc($result))
            $players[$data['playerid']]=$data;

        return $players;
        }

    function get_fight_actions($fightid)
        {
        $actions=array();
        $query="
            SELECT
                sequence, prefight_data, action_data
            FROM fight_actions
            WHERE fightid=$fightid
            ORDER BY sequence";
        $result=mysql_do_query($query);
        while($data=mysql_fetch_assoc($result))
            {
            extract($data);
            $prefight=unserialize($prefight_data);
            $actions=unserialize($actions);
            $actions[$data['sequence']]=array('prefight'=>$prefight,'actions'=>$actions);
            }

        return $actions;
        }

    function get_fight_data()
        {
        //Lock the db
        $this->lock_db(true);
        $query="
            SELECT
                fightid, sequence,
                unix_timestamp(timeout)-unix_timestamp(now()) timeout,
                fight_data
            FROM fights
            WHERE fightid=$fightid";
        $result=mysql_do_query($query);
        if(($data=mysql_fetch_assoc($result))===false)
            return false;
        extract($data);
        //Collect player data
        $players=$this->get_fight_players($data['fightid']);
        //Collect fight history
        $actions=$this->get_fight_actions($data['fightid']);
        //Unlock the db
        $this->unlock_db();
        //Generate the fight data for reference
        $fight=unserialize($fight_data);
        //Add to retval
        return array('fightid'=>$fightid,'sequence'=>$sequence,
            'timeout'=>$timeout,'fight'=>$fight,'players'=>$players);
        }

    function get_all_fights()
        {
        //See what fights are active.
        $retval=array();
        $query="
            SELECT fightid
            FROM fights";
        $result=mysql_do_query($query);
        while($data=mysql_fetch_assoc($result))
            {
            $retval[$fightid]=$this->get_fight_data($data['fightid']);
            }
        return $retval;
        }

    function set_fight_data($fightid,$data)
        {
        //insert main fight data.
        extract($data);
        $fight_data=mysql_real_escape_string(serialize($fight));
        $query="INSERT INTO fights (fightid,fight_data,sequence,active,timeout)
            VALUES($fightid,'$fight_data',$sequence,'$active','$timeout')";
        $result=mysql_do_query($query);
        if($result)
            {
            //insert player data.
            foreach($players as $player)
                {
                extract($player);
                $query="INSERT INTO fight_players(fightid,playerid,teamid,player_party,seen,sequence)
                    VALUES($fightid,$playerid,$teamid,$player_party,'$seen',$sequence)";
                $result=mysql_do_query($query);
                }
            //insert action data.
            foreach($actions as $action)
                {
                extract($action);
                $prefight_data=mysql_real_escape_string(serialize($prefight));
                $action_data=mysql_real_escape_string(serialize($action_data));
                $query="INSERT INTO fight_actions(fightid,sequence,prefight_data,action_data)
                    VALUES($fightid,$sequence,'$prefight_data','$action_data')";
                $result=mysql_do_query($query);
                }
            }
        }

    function get_active_fights()
        {
        //Lock the db
        $this->lock_db(true);
        //See what fights are active.
        $retval=array();
        $query="
            SELECT
                fightid, sequence,
                unix_timestamp(timeout)-unix_timestamp(now()) timeout,
                fight_data
            FROM fights
            WHERE active<>'no'";
        $result=mysql_do_query($query);
        while($data=mysql_fetch_assoc($result))
            {
            extract($data);
            //Collect player data
            $players=$this->get_fight_players($data['fightid']);

            //Generate the fight data for reference
            $fight=unserialize($fight_data);
            //Add to retval
            $retval[$fightid]=array('fightid'=>$fightid,'sequence'=>$sequence,
                'timeout'=>$timeout,'fight'=>$fight,'players'=>$players);
            }

        //Unlock the db
        $this->unlock_db();

        return $retval;
        }

    function get_fight($playerid,$fightid=0)
        {
        //See if this is cached.
        if(isset($GLOBALS['__GET_FIGHT_DATA']))
            return $GLOBALS['__GET_FIGHT_DATA'];

        //Lock the db
        $this->lock_db(true);
        //See if this player has a fight.
        $query="
            SELECT
                fights.fightid, active, teamid, player_party, fight_data,
                fight_players.sequence, seen, unix_timestamp(timeout)-unix_timestamp(now()) timeout
            FROM fights
            LEFT JOIN fight_players
                ON fights.fightid=fight_players.fightid AND playerid=$playerid
            WHERE ";
        if($fightid)
            $query.="fights.fightid=$fightid";
        else
            $query.="active<>'no' and playerid=$playerid";

        $result=mysql_do_query($query);
        //If there is more than one active fight data, then bail.
        if(mysql_num_rows($result)>1)
            {
            log_error(mysql_num_rows($result)." active fights(?!?!)\n$query");
            $this->unlock_db();
            return false;
            }
        $data=mysql_fetch_assoc($result);
        //If there is no active fight data, then bail.
        if($data===false)
            {
            //log_error("No active fight\n$query");
            $this->unlock_db();
            return false;
            }
        extract($data);

        if($active!='no')
            {
            $query="UPDATE fight_players SET seen='yes' WHERE playerid=$playerid AND fightid=$fightid";
            mysql_do_query($query);
            }

        //If this player has a fight, highest sequence and prefight data.
        //If this data is empty, then there are no sequences to the fight.
        if($sequence==0)
            {
            $prefight_data=$fight_data;
            $action_data=serialize(array());
            }
        //Otherwise load the data
        else
            {
            extract($data);
            do {
                $query="select sequence, prefight_data, action_data from fight_actions where fightid=$fightid and sequence=$sequence";
                $result=mysql_do_query($query);
                $data=mysql_fetch_assoc($result);
                if($data==false)
                    {
                    log_error("There is no data for a fight sequence $fightid-$sequence");
                    }
                $sequence--;
                } while($sequence>=0 && $data==false);
            if($data==false)
                {
                //Unlock the db
                $this->unlock_db();
                //Bail with fail.
                return false;
                }
            extract($data);
            }

        //Get the players list
        $players=$this->get_fight_players($fightid);

        //Unlock the db
        $this->unlock_db();

        //For efficiency, cache the data in a global.
        $GLOBALS['__GET_FIGHT_DATA']=array(
            'teamid'=>$teamid,
            'player_party'=>$player_party,
            'sequence'=>$sequence,
            'prefight'=>unserialize($prefight_data),
            'action_list'=>unserialize($action_data),
            'fight'=>unserialize($fight_data),
            'seen'=>$seen,
            'timeout'=>$timeout,
            'players'=>$players,
            'fightid'=>$fightid,
            'active'=>$active);

        //Return the data as an array of humungous proportions.
        return $GLOBALS['__GET_FIGHT_DATA'];
        }

    function get_full_fight($fightid)
        {
        $data=$this->get_fight_data($fightid);
        $full_fight=array();
        foreach($data['actions'] as $action)
            $full_fight+=$action['actions'];

        return array('prefight'=>$data['actions'][0]['prefight'],'actions'=>$full_fight,'fight'=>$data['fight']);
        }

    function init_fight(&$fight,$players)
        {
        //Lock the db
        $this->lock_db(true);

        //Get the next available fight slot.
        $query="
            select f1.fightid+1 fightid
            from fights f1
            left join fights f2 on f1.fightid+1=f2.fightid
            where f2.fightid is null
            order by f1.fightid";
        $result=mysql_do_query($query);
        if($result==false)
            {
            $this->unlock_db();
            return false;
            }
        $data=mysql_fetch_assoc($result);
        if($data==false)
            $fightid=1;
        else
            extract($data);

        //Set the fight's fightid
        $fight->id=$fightid;
        //Find the initial timeout value, too.
        $timeout=$fight->count()+30;

        //Store the fight.
        $fight_data=mysql_real_escape_string(serialize($fight));
        $query="
            INSERT INTO fights (fightid,fight_data,active,timeout,sequence)
            VALUES ($fightid,'$fight_data','yes',adddate(now(),INTERVAL $timeout MINUTE),0)";
        $result=mysql_do_query($query);

        if($result)
            {
            //The fight was addedd successfully.  Now add the fight players data.
            $data=array();
            foreach($players as $player)
                $data[]="($fightid,{$player['playerid']},{$player['teamid']},{$player['player_party']},0,'yes')";
            $query="INSERT INTO fight_players (fightid,playerid,teamid,player_party,sequence,seen) VALUES ".implode(',',$data);
            $result=mysql_do_query($query);
            }
        //Unlock the db
        $this->unlock_db();

        if ($result==false) return false;

        return $fightid;
        }

    function update_player($playerid,$new_sequence)
        {
        //Get the current fight and sequence for this
        $fight_data=$this->get_fight($playerid);
        if($fight_data==false)
            return null;
        extract($fight_data);

        //log_error("$sequence!=$new_sequence",100);
        //If new_sequence is <> sequence+1, then ignore
        if($sequence!=$new_sequence)
            return null;

        //Increment the player sequence
        $new_sequence++;

        //Lock the db
        $this->lock_db(true);

        //Update the sequence for this player
        $fightid=$fight->id;
        $query="UPDATE fight_players SET sequence=$new_sequence WHERE fightid=$fightid and playerid=$playerid";
        mysql_do_query($query);

        //Now see if there are any other players waiting to get in on the action.
        $query="SELECT count(*) count from fight_players WHERE sequence=$sequence AND fightid=$fightid";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);

        //If count is 0, then set the fight to processing.
        //This thread will be responsible for the fight processing.
        if($data['count']==0)
            {
            $query="UPDATE fights SET active='processing' WHERE fightid=$fightid";
            mysql_do_query($query);
            }

        //Unlock the db
        $this->unlock_db();

        //Return true if count is 0 or false if not.
        return ($data['count']==0);
        }

    function has_timed_out($fightid,$sequence_number)
        {
        //Lock the db
        $this->lock_db(true);

        //Check the timeout and active fields
        $query="SELECT IF(NOW()>=timeout,1,0) process,unix_timestamp(timeout)-unix_timestamp(now()) timeout, active, sequence FROM fights WHERE fightid=$fightid";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);
        extract($data);

        //If sequences do not match, return null to indicate a fight reload.
        if($sequence!=$sequence_number)
            $retval=null;
        //The deal: if process==1 and active='yes' then we will process the fight.
        elseif($process==1 and $active=='yes')
            {
            $query="UPDATE fights SET active='processing' WHERE fightid=$fightid";
            mysql_do_query($query);
            $retval=true;
            }
        //Otherwise we won't
        else
            $retval=false;

        //Unlock the db
        $this->unlock_db();

        //Return the result.
        return array('expired'=>$retval,'timeout'=>$timeout);
        }

    function update_timeout(&$fight,$delay)  //Delay is # of minutes
        {
        $fightid=$fight->id;

        //Lock the db
        $this->lock_db(true);

        //Update the fight timeout to the larger of timeout and now()+delay
        $query="UPDATE fights SET timeout=LEAST(timeout,adddate(now(),INTERVAL $delay MINUTE)) WHERE fightid=$fightid";
        $result=mysql_do_query($query);
        if($result)
            $result=mysql_affected_rows();

        //Unlock the db
        $this->unlock_db();

        return $result;
        }

    function update_fight_action(&$fight,$current_sequence)
        {
        //Setup the data.
        $fightid=$fight->id;
        $fight_data=mysql_real_escape_string(serialize($fight));

        //Lock the db
        $this->lock_db(true);

        //Now verify the fight sequence data.
        $query="SELECT sequence from fights WHERE fightid=$fightid AND active='yes'";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);
        if($data['sequence']!=$current_sequence)
            {
            $this->unlock_db();
            return false;
            }

        //Update the sequence for this fight
        $query="UPDATE fights SET fight_data='$fight_data' WHERE fightid=$fightid";
        mysql_do_query($query);

        //Unlock the db
        $this->unlock_db();

        //Return true that the update completed.
        return true;
        }

    function update_fight(&$prefight,&$actions,&$fight,$new_sequence)
        {
        //Setup the data.
        $fightid=$fight->id;
        $prefight_data=mysql_real_escape_string(serialize($prefight));
        $action_data=mysql_real_escape_string(serialize($actions));
        $fight_data=mysql_real_escape_string(serialize($fight));
        //Find the initial timeout value, too.
        $timeout=$fight->count()*2+30;

        //PS if there are less thatn two live parties, then set active to false!!!
        $active=($fight->count_live_parties()<2)?'no':'yes';

        //Lock the db
        $this->lock_db(true);

        //Now verify the fight sequence data.
        $query="SELECT sequence from fights WHERE fightid=$fightid";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);
        if($data['sequence']+1!=$new_sequence)
            {
            $this->unlock_db();
            return array('result'=>false,'timeout'=>$timeout);
            }

        //Create an action entry/
        $query="
            INSERT INTO fight_actions (fightid,sequence,prefight_data,action_data)
            VALUES ($fightid,$new_sequence,'$prefight_data','$action_data')";
        $result=mysql_do_query($query);

        //Update the sequence for this fight
        $query="UPDATE fights SET sequence=$new_sequence, active='$active', timeout=adddate(now(),INTERVAL $timeout MINUTE), fight_data='$fight_data' WHERE fightid=$fightid";
        mysql_do_query($query);

        //Update the sequence for players in this fight
        $query="UPDATE fight_players SET sequence=$new_sequence, seen='no' WHERE fightid=$fightid";
        mysql_do_query($query);

        //Unlock the db
        $this->unlock_db();

        //Return true that the update completed.
        return array('result'=>true,'timeout'=>$timeout*60);
        }
    }
?>
