<?php
require_once INCLUDE_DIR.'character.php';
require_once INCLUDE_DIR.'mysql.php';

$GLOBAL['__CHARACTERS']=array();

class CHARACTER_STORE
    {
    //This constructor (tries to) initialize the character table.
    function CHARACTER_STORE($reset=false)
        {

        $result=mysql_do_query("select count(*) from characters",false);
        if($result===false || ($data=mysql_fetch_row($result))===false || $data[0]==0 || $reset===true)
            {
            //Delete tables
            mysql_do_query("DROP TABLE IF EXISTS characters");
            mysql_do_query("DROP TABLE IF EXISTS character_abilities");
            mysql_do_query("DROP TABLE IF EXISTS character_items");
            mysql_do_query("DROP TABLE IF EXISTS character_equip");
            //Recreate tables.
            mysql_do_query("
CREATE TABLE `characters` (
  `characterid` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `personalityid` int(11) unsigned NOT NULL default '0',
  `jobid` int(11) NOT NULL default '0',
  `level` int(11) NOT NULL default '0',
  `experience` int(11) NOT NULL default '0',
  `need` int(11) NOT NULL default '0',
  `bHP` int(11) NOT NULL default '0',
  `bMP` int(11) NOT NULL default '0',
  `bAccuracy` int(11) NOT NULL default '0',
  `bStrength` int(11) NOT NULL default '0',
  `bDodge` int(11) NOT NULL default '0',
  `bBlock` int(11) NOT NULL default '0',
  `bSpeed` int(11) NOT NULL default '0',
  `bPower` int(11) NOT NULL default '0',
  `bResistance` int(11) NOT NULL default '0',
  `bFocus` int(11) NOT NULL default '0',
  `cHP` int(11) NOT NULL default '0',
  `cMP` int(11) NOT NULL default '0',
  `cAccuracy` int(11) NOT NULL default '0',
  `cStrength` int(11) NOT NULL default '0',
  `cDodge` int(11) NOT NULL default '0',
  `cBlock` int(11) NOT NULL default '0',
  `cSpeed` int(11) NOT NULL default '0',
  `cPower` int(11) NOT NULL default '0',
  `cResistance` int(11) NOT NULL default '0',
  `cFocus` int(11) NOT NULL default '0',
  PRIMARY KEY  (`characterid`)
) ;");
            mysql_do_query("
CREATE TABLE `character_abilities` (
  `characterid` int(11) unsigned NOT NULL default '0',
  `abilityid` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`characterid`,`abilityid`)
) ;");
            mysql_do_query("
CREATE TABLE `character_items` (
  `characterid` int(11) unsigned NOT NULL default '0',
  `slot` tinyint(4) unsigned NOT NULL default '0',
  `itemid` int(11) unsigned NOT NULL default '0',
  `qty` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`characterid`,`slot`)
) ;");
            mysql_do_query("
CREATE TABLE `character_equip` (
  `characterid` int(11) unsigned NOT NULL default '0',
  `slot` varchar(16) NOT NULL default '',
  `item` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`characterid`,`slot`)
) ;");
            }
        }

    function &get_character($index)
        {
        //If the character is not in queue, then cache
        if(!isset($GLOBAL['__CHARACTERS'][$index]))
            {
            //Get base stats
            $query="select * from characters where characterid=$index";
            $result=mysql_do_query($query);
            $data=mysql_fetch_assoc($result);
            if($data===false)
                log_error("character $index does not exist in the database.");

            //Get abilities
            $query="select abilityid from character_abilities where characterid=$index";
            $result=mysql_do_query($query);
            $abilities=array();
            while($data2=mysql_fetch_assoc($result))
                $abilities[]=$data2['abilityid'];

            //Get inventory
            $query="select slot, itemid, qty from character_items where characterid=$index";
            $result=mysql_do_query($query);
            $inventory=array();
            while($data2=mysql_fetch_assoc($result))
                $inventory[$data2['slot']]=array('item'=>$data2['itemid'],'qty'=>$data2['qty']);

            //Get equipment
            $query="select slot, item from character_equip where characterid=$index";
            $result=mysql_do_query($query);
            $equipment=array();
            while($data2=mysql_fetch_assoc($result))
                $equipment[$data2['slot']]=(int)$data2['item'];

            $character=new CHARACTER;
            $character->make_hero(
                $data['characterid'],$data['name'],$data['jobid'],$data['level'],$data['experience'],$data['need'],
                array($data['bHP'],$data['bMP'],$data['bAccuracy'],$data['bStrength'],$data['bDodge'],
                    $data['bBlock'],$data['bSpeed'],$data['bPower'],$data['bResistance'],$data['bFocus']),
                array($data['cHP'],$data['cMP'],$data['cAccuracy'],$data['cStrength'],$data['cDodge'],
                    $data['cBlock'],$data['cSpeed'],$data['cPower'],$data['cResistance'],$data['cFocus']),
                $abilities,$inventory,$equipment,$data['personalityid']);

            $GLOBAL['__CHARACTERS'][$index]=&$character;
            }

        return $GLOBAL['__CHARACTERS'][$index];;
        }

    function &get_all_characters()
        {
        $characters=array();
        $query="select characterid from characters";
        $result=mysql_do_query($query);
        while(($data=mysql_fetch_row($result))!==false)
            $characters[$data[0]]=&$this->get_character($data[0]);
        return $characters;
        }

    function set_character(&$character)
        {
        if(!is_a($character,'CHARACTER'))
            {
            var_dump($character);
            log_error("Character being set is not a CHARACTER object.");
            exit;
            }
        $index=(is_null($character->charid)?0:$character->charid);
        $name=mysql_real_escape_string($character->name);
        $query="
        replace into characters
            (characterid,name,personalityid,jobid,level,experience,need,
            bHP,bMP,bAccuracy,bStrength,bDodge,bBlock,bSpeed,bPower,bResistance,bFocus,
            cHP,cMP,cAccuracy,cStrength,cDodge,cBlock,cSpeed,cPower,cResistance,cFocus
            )
        values
            ($index,'$name',{$character->personalityid},{$character->jobid},{$character->level},{$character->exp},{$character->need},
            {$character->base["HP"]},{$character->base["MP"]},{$character->base["Accuracy"]},{$character->base["Strength"]},{$character->base["Dodge"]},{$character->base["Block"]},{$character->base["Speed"]},{$character->base["Power"]},{$character->base["Resistance"]},{$character->base["Focus"]},
            {$character->current["HP"]},{$character->current["MP"]},{$character->current["Accuracy"]},{$character->current["Strength"]},{$character->current["Dodge"]},{$character->current["Block"]},{$character->current["Speed"]},{$character->current["Power"]},{$character->current["Resistance"]},{$character->current["Focus"]}
            )";
        mysql_do_query($query);
        if($index==0)
            {
            $index=mysql_insert_id();
            $character->charid=$index;
            }

        //Fix abilities
        mysql_do_query("delete from character_abilities where characterid=$index");
        if(count($character->abilities)>0)
            {
            $abilities=array();
            foreach($character->abilities as $ability)
                $abilities[]="($index,$ability)";
            $query="
            replace into character_abilities
                (characterid,abilityid)
            values ".implode(',',$abilities);
            mysql_do_query($query);
            }

        //Fix inventory
        mysql_do_query("delete from character_items where characterid=$index");
        if(count($character->inventory)>0)
            {
            $items=array();
            foreach($character->inventory as $count=>$item)
                $items[]="($index,$count,$item[item],$item[qty])";
            $query="
            replace into character_items
                (characterid,slot,itemid,qty)
            values ".implode(',',$items);
            mysql_do_query($query);
            }
        //Fix equipment
        #List equipped items
        $equipped=array_unique($character->equipment);
        mysql_do_query("delete from character_equip where characterid=$index");
        if(count($character->equipment)>0)
            {
            $equip=array();
            foreach($character->equipment as $slot=>$item)
                if(!is_null($item))
                    $equip[]="($index,'$slot',$item)";
            $query="
            replace into character_equip
                (characterid,slot,item)
            values ".implode(',',$equip);
            if(count($equip)>0)
                mysql_do_query($query);
            }
        }

    function delete_character($index)
        {
        mysql_do_query("delete from characters where characterid=$index");
        mysql_do_query("delete from character_abilities where characterid=$index");
        mysql_do_query("delete from character_items where characterid=$index");
        mysql_do_query("delete from character_equip where characterid=$index");
        }
    }
?>