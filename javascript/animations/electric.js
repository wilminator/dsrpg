var electric_object=null;
var electric_events=null;

function electric_impact(pindex,gindex,cindex,tpindex,tgindex,tcindex,range,events)
    {
    electric_object=new static_impact();
    return electric_object.start(pindex,gindex,cindex,tpindex,tgindex,tcindex,range,events,['lightening_storm.gif'],['electrocute3.ogg'],[1500,2500]);
    electric_events=events;
    electric_object=object_create_image('lightening_storm.gif',fight_screen,{},effect_images);
    object_x(electric_object,0);
    object_y(electric_object,pindex==tpindex?400:200);
    object_z(electric_object,pindex==tpindex?150:50);
    object_show(electric_object);
    start_event();
    setTimeout('electric_damage('+[tpindex,tgindex,tcindex,range].join(',')+');',1500);
    play_sound('electrocute3.ogg');
    return 2500;
    }

function electric_damage(tpindex,tgindex,tcindex,range)
    {
    default_impact_processor(tpindex,tgindex,tcindex,range,electric_events)
    electric_events=null;
    setTimeout('electric_cleanup();',1000);
    }

function electric_cleanup()
    {
    object_delete(electric_object);
    electric_object=null;
    finish_event();
    }

