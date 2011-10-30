var message_delay=2000;

function show_fight_message(x,y)
    {
    var object=object_get('fight_message');
    object_y(object,y);
    object_x(object,x);
    object_show(object);
    setTimeout('hide_fight_message();',message_delay);
    start_event();
    return 0;
    }

function set_fight_message(msg)
    {
    var object=object_get('fight_message').firstChild;
    object.replaceData(0,object.length,msg);
    return 0;
    }

function hide_fight_message()
    {
    var object=object_get('fight_message');
    object_hide(object);
    finish_event();
    return 0;
    }
