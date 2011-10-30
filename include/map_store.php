<?php
require INCLUDE_DIR.'map.php';

class MAP_STORE
    {
    function MAP_STORE($reset=false)
        {

        $result=mysql_do_query("select count(*) from maps",false);
        if($result===false || ($data=mysql_fetch_row($result))===false || $data[0]==0 || $reset===true)
            {
            //Delete tables
            mysql_do_query("DROP TABLE IF EXISTS maps");
            mysql_do_query("DROP TABLE IF EXISTS map_tiles");
            mysql_do_query("DROP TABLE IF EXISTS map_events");
            mysql_do_query("DROP TABLE IF EXISTS map_npcs");
            mysql_do_query("DROP TABLE IF EXISTS map_monsters");
            mysql_do_query("DROP TABLE IF EXISTS map_zones");
            mysql_do_query("DROP TABLE IF EXISTS map_cache");
            //Recreate tables.
            mysql_do_query("
CREATE TABLE `maps` (
  `mapid` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `width` int(11) unsigned NOT NULL default 1,
  `height` int(11) unsigned NOT NULL default 1,
  `data` MEDIUMBLOB(16777215) NOT NULL default '\0',
  `zones` MEDIUMBLOB(16777215) NOT NULL default '\0',
  PRIMARY KEY  (`mapid`)
) ;");
            mysql_do_query("
CREATE TABLE `map_tiles` (
  `mapid` int(11) unsigned NOT NULL,
  `tile` byte(4) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `poison` int(11) unsigned NOT NULL,
  `pass` byte(4) unsigned NOT NULL,
  `encounter` byte(4) unsigned NOT NULL
  PRIMARY KEY  (`mapid`,`tile`)
) ;");
            mysql_do_query("
CREATE TABLE `map_events` (
  `mapeventid` int(11) unsigned NOT NULL,
  `mapid` int(11) unsigned NOT NULL,
  `name` varchar(32) NOT NULL default'',
  `x` int(11) unsigned NOT NULL,
  `y` int(11) unsigned NOT NULL,
  `data` BLOB(65535) NOT NULL
  PRIMARY KEY  (`mapeventid`)
) ;");
            mysql_do_query("
CREATE TABLE `map_npcs` (
  `mapnpcid` int(11) unsigned NOT NULL,
  `mapid` int(11) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `data` BLOB(65535) NOT NULL default '',
  PRIMARY KEY  (`mapnpcid`)
) ;");
            mysql_do_query("
CREATE TABLE `map_monsters` (
  `mapmonsterid` int(11) unsigned NOT NULL auto_increment,
  `party` BLOB(1024) NOT NULL default '',
  PRIMARY KEY  (`mapmonsterid`)
) ;");
            mysql_do_query("
CREATE TABLE `map_zones` (
  `mapid` int(11) unsigned NOT NULL,
  `zone` byte(4) unsigned NOT NULL,
  `mapmonsterid` int(11) NOT NULL,
  PRIMARY KEY  (`mapid`,`zone`,`mapmonsterid`)
) ;");
            mysql_do_query("
CREATE TABLE `map_cache` (
  `mapid` int(11) unsigned NOT NULL,
  `last_used` datetime unsigned NOT NULL,
  `data` LONGBLOB(50000000) NOT NULL,
  PRIMARY KEY  (`mapid`),
  INDEX (`last_used`)
) ;");
            }
        }

    function get_map($index)
        {
        //Check cache
        $query="select * from map_cache where mapid=$index";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);
        if($data===false)
            {
            //The cache does not have this map.  Shred all outdated maps then recreate the data.
            $query="DELETE FROM map_cache WHERE last_used<DATE_SUB(NOW(),INTERVAL 15 MINUTE)";
            $result=mysql_do_query($query);

            $query="select * from maps where mapid=$index";
            $result=mysql_do_query($query);
            $data=mysql_fetch_assoc($result);
            if($data===false)
                {
                log_error("map $index does not exist in the database.");
                return null;
                }

            //Get tileset
            $query="select tile, filename, poison, pass, encounter from map_tiles where mapid=$index";
            $result=mysql_do_query($query);
            $tileset=array();
            while($data2=mysql_fetch_assoc($result))
                $tileset[$data2['tile']]=$data2;

            //Get events
            $query="select mapeventid, name, x, y, data from map_events where mapid=$index";
            $result=mysql_do_query($query);
            $events=array();
            while($data2=mysql_fetch_assoc($result))
                $events[$data2['x'].','.$data2['y']]=$data2;

            //Get npcs
            $query="select mapnpcid, name, data from map_npcs where mapid=$index";
            $result=mysql_do_query($query);
            $npcs=array();
            while($data2=mysql_fetch_assoc($result))
                $npcs[$data2['mapnpc']]=$data2;

            //Make the map object
            $map=new MAP($width,$height,$data['data'],$data['zone'],$tileset,$events,$npcs,$monsters);

            //Populate the cache with the map object.
            $map_data=mysql_real_escape_string(serialize($map));
            $query="INSERT INTO map_cache(mapid,last_used,data) VALUES ($mapid,NOW(),'$map_data')";
            $result=mysql_do_query($query);
            }
        else
            {
            //Recreate map data
            $map=unserialize($data['data']);

            //Update last use
            $query="UPDATE map_cache SET last_used=NOW() WHERE mapid=$mapid";
            $result=mysql_do_query($query);
            }
        return $map
        }

    function set_map(&$map,$index)
        {
        //Update the main map data
        $safe_data=mysql_real_escape_string($map->data);
        $safe_zones=mysql_real_escape_string($map->zones);
        $query="REPLACE INTO maps(mapid,width,height,data,zones) VALUES ($index,{$map->width},{$map->height},'$safe_data','$safe_zones')";
        $result=mysql_do_query($query);

        //Update tileset
        $query="DELETE FROM map_tiles WHERE mapid=$index";
        $result=mysql_do_query($query);
        $tileset=array();
        foreach($map->tileset as $tile_index=>$tile_data)
            $tileset[]="($index,$tile_index,'$tile_data[filename]',$tile_data[poison],$tile_data[pass],$tile_data[encounter])";

        $query="INSERT INTO map_tiles (mapid, tile, filename, poison, pass, encounter) VALUES ".implode(',',$tileset);
        $result=mysql_do_query($query);

        //Update events
        $query="DELETE FROM map_events WHERE mapid=$index";
        $result=mysql_do_query($query);
        $events=array();
        foreach($map->events as $event_data)
            {
            $event_index=count($events);
            $safe_event_data=mysql_real_escape_string($event_data['data']);
            $events[]="($index,$event_index,'$event_data[name]',$tile_data[x],$tile_data[y],'$safe_event_data')";
            }
        $query="INSERT INTO map_events (mapid, mapeventid, name, x, y, data) VALUES ".implode(',',$tileset);
        $result=mysql_do_query($query);

        //Update npcs
        $query="DELETE FROM map_npcs WHERE mapid=$index";
        $result=mysql_do_query($query);
        $npcs=array();
        foreach($map->npcs as $npc_data)
            {
            $npc_index=count($npcs);
            $safe_npc_data=mysql_real_escape_string($npc_data['data']);
            $events[]="($index,$npc_index,'$npc_data[name]','$safe_npc_data')";
            }
        $query="INSERT INTO map_events (mapid, mapnpcid, name, data) VALUES ".implode(',',$tileset);
        $result=mysql_do_query($query);

        //Destroy any cache object.
        $query="DELETE FROM map_cache where mapid=$index";
        $result=mysql_do_query($query);
        }
    }
?>
