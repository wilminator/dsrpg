<?php
/*
<script type="x" {x="x"} {y="x"} {name="x"}>...</script>
    Script XML enclosing tags.
    type- Either npc for an NPC event or tile for a tile event
    x- must be defined if type="tile".  X coordinate of the tile.
    y- must be defined if type="tile".  Y coordinate of the tile.
    name- must be defined if type="npc".  The name of this npc for script 
        coodrination.
<event id="x" {item="x"} {counter="x"}>...</event>
    Defines code that runs when a specific event is triggered by the game.
    id- the event that this code is triggered by
        init: Called when the map is initialized upon entry.  Called every
            time the map has been completely vacated by PCs and a PC party
            enters the map.
        enter: Called when a character enters the designated tile. Valid only
            for tiles.
        use: Called when a character uses an item on this tile, facing this
            tile, or facing this NPC.  Must use item attribute in the event
            tag with this value for id.
        talk: Called when a PC talks to this NPC.  Valid only for NPCs.
        search: Called when a tile is searched. Valid only for tiles.
        fight: Called only when a PC starts a fight with the NPC.  Valid only
            for NPCs.
        change: Called when the counter specified by the counter attribute 
            changes.  Requires the counter attribute.
    item- Required if the event id is use.  Specifies the item id that must be 
        used to trigger thte event.
    counter- Required if the event id is counter.  Specifies the counter that 
        is monitored for changes.  This block of code is executed if there is 
        a change to the specified counter.
<switch counter="">...</switch>
    Begins a code switch statement like VB's select case statement based on
        the specified counter.  Uses case tags to designate the switching
        values of the counter.
    counter- Then name of the counter being used to switch code.
<case range="x,y,z">...</case>
    Defines a block of code ran when the case range evaluates true against the
        immediately enclosing switch tag's defined value.
    range- a comma delimited set of arguments that are evaluated againt the
        enclosing switch's defined value.  Each set may be one of the
        following: a value preceded by the less-than operator (<), a value
        preceded by the greater-than operator,two values hyphenated (x-y), or
        a single value.  Values may be strings or numbers.  If the less-than
        or greater than operators are present, the value of the switch
        statement is tested to see if it is less than or greater than the
        value specified in that range.  If only a single value is present, a
        direct equality test is performed.  If two values are hyphenated, then
        the switch value is check to see if if falls inclusively in that
        range.  If any one set is true, then the case is evaluated and all
        other case statements in that switch statement are ignored.  If no
        case statement is true, then no code is executed.  If range is not
        defined, then the case is automatically true.
<label id="x" />
    Defines a label within a section of code.  Labels may only be seen within
        the current scope of code, and may not be seen within child switch or
        fight tags.
    id- The name of this label.  The first label in the current scope of code
        is used if invoked by a goto tag.
<goto id="x">
    Instructs the code to move to the first label with the same id within the 
        current scope of code.  Goto will not direct execution into a child or 
        out of the current scope.
    id- The id of the label to goto.
<sub id="x">...</sub>
    A subroutine of code.  This allows for code reuse.  Must be declared as an 
        immediate child of script or it will be ignored.
<call id="x" />
    Invokes a declared subroutine.  No variables are passed, but counters may
        be set for pigeon-hole variable passing.
<npc>...</npc>
    Defines that the following code sets or replaces any definition of the NPC
        associated with this script in the game.  Valid only for NPC scripts.
<location x="x" y="x" />
    Specifies an instantaneous location for the NPC.  This instruction may not
        be repeated.
    x- the new x coordinate of the NPC.
    y- the new y coordinate of the NPC.
<personality id="x" />
    Changes the personality sprite associated with the NPC.
    id- the new personality of the npc.
<move dir="x" {frames="x"} {steps="x"} />
    Queues movement an NPC is supposed to make.  If the NPC cannot make that
        move, it waits (forever) until it can continue where it left off.
    dir- the direction the NPC will move.  Must be up, down, left, right, or
        random.
    frames- the number of frames it takes for the NPC to move one tile.  One
        frame is 1/20 of a second.  If not defined, frames defaults to 15.
    steps- the number of steps to take in the specified direction.  If not
        defined, steps defaults to 1.
<wait frames="x" />
    Queues a number of frames to wait before continuing movement.
    frames- the number of frames to wait.  One frame is 1/20 of a second.
<repeat />
    Instructs the NPC to continually repeat the previously queued
        move and wait instructions.  Further instructions are ignored.
<tile terrain="x" />
    Changes the terrain to the value of terrain.  Valid only for tile scripts.
    terrain- the name of a tile in the tileset to turn this tile into.
<pause frames="" />
    Causes the script to suspend execution for a specified number of frames
        then to continue execution.  This is a guaranteed minimum, not 
        maximum.
<dialog>...</dialog>
    Defines dialog that is to occur.  Text nodes are treated as game text.
        Other tags define scripted effects.
<break /> or <br>
    Defines a break in the dialog.  The user is expected to click on the GUI
        to continue.
<question value="x">...</question>
    Before the dialog continues, the user is prompted with the value supplied
        in the question attribute, and must pick one of the answers.  The
        available answers are defined as child tags in this element.
    value- The question the user is prompted with while the system waits
        for an answer.
<answer value="x">...</answer>
    Specifies an answer to the question and a resulting block of code to
        execute if this answer is chosen.
    value- The text displayed for this answer.
<locateat name="x" x="x" y="x" />
    Causes the NPC named by the name attribute to be instantly relocated at
        the specified x and y coordinates.  The reserved name of "{player}"
        affects the player sprite.
<personalityof name="x" id="x" />
    Causes the NPC named by the name attribute to have the personality 
        specified by id.
<moveto name="x" dir="x" {frames="x"} {steps="x"} />
    Causes the NPC named by the name attribute to begin moving in the 
        direction specified by dir.  Acts like the move tag, but with 
        immediacy.
<waitfor {name=""} />
    Causes further execution of this dialog to wait for the specified NPC (or 
        player) to finish all movement.  If name is omitted, then all NPCs and 
        players instructed to move using moveto are waited for.
<scrollto x="x" y="x" frames="x" />
    Causes the screen viewport to begin scrolling to center itself on the 
        specified x and y coordinates within the specified number of frames.
<centeron name="x" frames="x"/>
    Causes the screen to scroll until centered on the named npc (or player of 
        name is "{player}") and remain centered on that sprite.
<sound id="x" />
    Causes the specified sound to be played.
<music {id=""} />
    Causes the specified music to be played.  If id is omitted, stops all 
        music.
*/
?>