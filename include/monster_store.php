<?php
require_once INCLUDE_DIR.'monster.php';
require_once INCLUDE_DIR.'mysql.php';
require_once INCLUDE_DIR.'party.php';

class MONSTER_STORE
    {
    //This constructor (tries to) initialize the monster table.
    function MONSTER_STORE($reset=false)
        {

        $result=mysql_do_query("select count(*) from monsters",false);
        if($result===false || ($data=mysql_fetch_row($result))===false || $data[0]==0 || $reset===true)
            {
            //Delete tables
            mysql_do_query("DROP TABLE IF EXISTS monsters");
            mysql_do_query("DROP TABLE IF EXISTS monster_abilities");
            mysql_do_query("DROP TABLE IF EXISTS monster_items");
            mysql_do_query("DROP TABLE IF EXISTS monster_equip");
            //Recreate tables.
            mysql_do_query("
CREATE TABLE `monsters` (
  `monsterid` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `personalityid` int(11) unsigned NOT NULL default '0',
  `pxp` int(11) NOT NULL default '0',
  `gold` int(11) NOT NULL default '0',
  `HP` int(11) NOT NULL default '0',
  `MP` int(11) NOT NULL default '0',
  `accuracy` int(11) NOT NULL default '0',
  `strength` int(11) NOT NULL default '0',
  `dodge` int(11) NOT NULL default '0',
  `block` int(11) NOT NULL default '0',
  `speed` int(11) NOT NULL default '0',
  `power` int(11) NOT NULL default '0',
  `resistance` int(11) NOT NULL default '0',
  `focus` int(11) NOT NULL default '0',
  `ai_action` tinyint(4) NOT NULL default '0',
  `ai_goal` tinyint(4) NOT NULL default '0',
  `ai_target` tinyint(4) NOT NULL default '0',
  `ai_experience` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`monsterid`)
) ;");
            mysql_do_query("
CREATE TABLE `monster_abilities` (
  `monsterid` int(11) unsigned NOT NULL default '0',
  `abilityid` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`monsterid`,`abilityid`)
) ;");
            mysql_do_query("
CREATE TABLE `monster_items` (
  `monsterid` int(11) unsigned NOT NULL default '0',
  `slot` tinyint(4) unsigned NOT NULL default '0',
  `itemid` int(11) unsigned NOT NULL default '0',
  `qty` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`monsterid`,`slot`)
) ;");
            mysql_do_query("
CREATE TABLE `monster_equip` (
  `monsterid` int(11) unsigned NOT NULL default '0',
  `slot` tinyint(4) NOT NULL default '0',
  `side` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`monsterid`,`slot`)
) ;");
            }
        }

    function get_monster($index)
        {
        $query="select * from monsters where monsterid=$index";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);
        if($data===false)
            log_error("monster $index does not exist in the database.");

        //Get abilities
        $query="select abilityid from monster_abilities where monsterid=$index";
        $result=mysql_do_query($query);
        $abilities=array();
        while($data2=mysql_fetch_assoc($result))
            $abilities[]=$data2['abilityid'];

        //Get inventory
        $query="select slot, itemid, qty from monster_items where monsterid=$index";
        $result=mysql_do_query($query);
        $inventory=array();
        while($data2=mysql_fetch_assoc($result))
            $inventory[$data2['slot']]=array('item'=>$data2['itemid'],'qty'=>$data2['qty']);

        //Get equipment
        $query="select slot, side from monster_equip where monsterid=$index";
        $result=mysql_do_query($query);
        $equipment=array();
        while($data2=mysql_fetch_assoc($result))
            $equipment[]=array('slot'=>$data2['slot'],'side'=>$data2['side']);

        return new MONSTER(
            $data['name'],
            array($data['HP'],$data['MP'],$data['speed'],$data['accuracy'],$data['strength'],
                $data['dodge'],$data['block'],$data['power'],$data['resistance'],$data['focus']),
            $abilities,$inventory,$equipment,$data['gold'],$data['personalityid'],
            $data['ai_action'],$data['ai_goal'],$data['ai_target'],$data['ai_experience']);
        }

    function &get_all_monsters()
        {
        $monsters=array();
        $query="select monsterid from monsters";
        $result=mysql_do_query($query);
        while(($data=mysql_fetch_row($result))!==false)
            $monsters[$data[0]]=&$this->get_monster($data[0]);
        return $monsters;
        }

    function &get_monsters_in_range($target_pxp,$spread=0)
        {
        $min_pxp=floor($target_pxp*(1.0-($spread*2/3)));
        $max_pxp=floor($target_pxp*(1.0+($spread*1/3)));
        $monsters=array();
        $query="select monsterid from monsters where pxp between $min_pxp and $max_pxp";
        $result=mysql_do_query($query);
        while(($data=mysql_fetch_row($result))!==false)
            $monsters[]=$this->get_monster($data[0]);
        return $monsters;
        }

    function set_monster(&$monster,$index)
        {
        $name=mysql_real_escape_string($monster->name);
        $query="
        replace into monsters
            (monsterid,name,personalityid,pxp,gold,HP,MP,accuracy,strength,
            dodge,block,speed,power,resistance,focus,
            ai_action,ai_goal,ai_target,ai_experience)
        values
            ($index,'$name',{$monster->personalityid},{$monster->pxp},{$monster->gold},{$monster->stats["HP"]},{$monster->stats["MP"]},{$monster->stats["Accuracy"]},{$monster->stats["Strength"]},{$monster->stats["Dodge"]},
            {$monster->stats["Block"]},{$monster->stats["Speed"]},{$monster->stats["Power"]},{$monster->stats["Resistance"]},{$monster->stats["Focus"]},
            {$monster->ai_action},{$monster->ai_goal},{$monster->ai_target},{$monster->ai_experience})";
        mysql_do_query($query);
        if($index==0)
            $index=mysql_insert_id();

        //Fix abilities
        mysql_do_query("delete from monster_abilities where monsterid=$index");
        if(count($monster->abilities)>0)
            {
            $abilities=array();
            foreach($monster->abilities as $ability)
                $abilities[]="($index,$ability)";
            $query="
            replace into monster_abilities
                (monsterid,abilityid)
            values ".implode(',',$abilities);
            mysql_do_query($query);
            }

        //Fix inventory
        mysql_do_query("delete from monster_items where monsterid=$index");
        if(count($monster->items)>0)
            {
            $items=array();
            foreach($monster->items as $count=>$item)
                $items[]="($index,$count,$item[item],$item[qty])";
            $query="
            replace into monster_items
                (monsterid,slot,itemid,qty)
            values ".implode(',',$items);
            mysql_do_query($query);
            }
        //Fix equipment
        mysql_do_query("delete from monster_equip where monsterid=$index");
        if(count($monster->equipment)>0)
            {
            $equip=array();
            foreach($monster->equipment as $slot)
                $equip[]="($index,$slot[slot],$slot[side])";
            $query="
            replace into monster_equip
                (monsterid,slot,side)
            values ".implode(',',$equip);
            mysql_do_query($query);
            }
        return $index;
        }

    function delete_monster($index)
        {
        mysql_do_query("delete from monsters where monsterid=$index");
        mysql_do_query("delete from monster_abilities where monsterid=$index");
        mysql_do_query("delete from monster_items where monsterid=$index");
        mysql_do_query("delete from monster_equip where monsterid=$index");
        }

    function write_monsters_file()
        {
        $handle=fopen(INCLUDE_DIR.'monsters.php','w');
        if($handle)
            {
            $monsters=$this->get_all_monsters();
            $monster_ser=serialize($monsters);
            fwrite($handle, '<?php require_once INCLUDE_DIR."monster.php"; $GLOBALS["monsters"]=unserialize(<<<EOD
'.$monster_ser.'
EOD
); ?>');
            fclose($handle);
            }
        }
    }
?>
