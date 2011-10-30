var active_events=0;
var wait_for_completion=false;
var playback_queue=[];
var event_playback_callback=null;
var paused_counter=0;

function set_playback_queue(events,callback)
    {
    playback_queue=events;
    event_playback_callback=callback;
    event_playback();
    }

function event_playback()
    {
    if(wait_for_completion && active_events>0 && ++paused_counter<200)
        {
        setTimeout('event_playback();',50);
        return;
        }
    wait_for_completion=false;
    paused_counter=0;
    var command,delay;
    while(playback_queue.length>0)
        {
        command=playback_queue.shift();
        switch(command[0])
            {
            case 0:
                delay=call_function(command[1],command.slice(2));
                if (delay>0)
                    {
                    setTimeout('event_playback();',delay);
                    return;
                    }
                break;
            case 1:
                if(active_events>0)
                    {
                    wait_for_completion=true;
                    setTimeout('event_playback();',50);
                    return;
                    }
            }
        }
    if (event_playback_callback)
        event_playback_callback();
    }

function call_function(func_name,args)
    {
    var index,cmdline,argline=[];
    if(func_name=="") return 0;
    for (index in args)
        argline.push("args["+index+"]");
    cmdline='(!'+func_name+')';
    funct=eval(cmdline);
    if (!funct)
        {
        cmdline='if('+func_name+') '+func_name+"("+argline.join(',')+")";
        cmdline=eval(cmdline);
        }
    else
        alert("Unknown function "+func_name+"!");
    return cmdline;
    }

function start_event()
    {
    active_events+=1;
    //set_fight_message(active_events.toString());
    //show_fight_message(0,0);
    }

function finish_event()
    {
    active_events-=1;
    //set_fight_message(active_events.toString());
    //show_fight_message(0,0);
    }

