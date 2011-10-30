<?php
/*
MAP_COUNTERS holds all the data that affects all maps of an instance of the 
world.  This class does all DB interfacing, and must be thread safe, at least
for the map the player this object is created for is currently located in.

There are two categories of counters: personal and global.
Personal counters only affect the current player.
Global counters affect ALL players in the world instance.  Only global 
counters must be thread safe.

This class operates on the principal that if a counter is changed (either type) 
for an object in the current map in the current world instance, then that 
object's script MUST fire to calculate potential changes.

Of note, only the thread making the change causes the object's script to
execute; as the script executes, queued messages are created that affect the
currently present players.
*/
class MAP_COUNTERS
    {
    var $worldid;
    var $counters;
    var $global_counters;

    function MAP_COUNTERS($worldid)
        {
        }
}
?>
