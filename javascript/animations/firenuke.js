var firenuke_object=null;
var firenuke_events=null;

function firenuke_impact(pindex,gindex,cindex,tpindex,tgindex,tcindex,range,events)
    {
    firenuke_object=new slide_right_impact();
    return firenuke_object.start(pindex,gindex,cindex,tpindex,tgindex,tcindex,range,events,['huge_fireball.png'],['Flame7.ogg'],[2000]);
    firenuke_events=events;
    firenuke_object=object_create_image('huge_fireball.png',fight_screen,{alt:'Huge fireball!!!'},effect_images);
    object_x(firenuke_object,-200);
    object_y(firenuke_object,tpindex==player_party?400:200);
    object_z(firenuke_object,tpindex==player_party?150:50);
    object_show(firenuke_object);
    start_event();
    setTimeout('firenuke_move('+[tpindex,tgindex,tcindex,range].join(',')+');',10);
    play_sound('Flame7.ogg');
    return 2000;
    }

//Possibly incorporate the event handler into two default functions-
//one for left to right and one for right to left.
function firenuke_move(tpindex,tgindex,tcindex,range)
    {
    var new_x=object_get_x(firenuke_object)+25;
    object_x(firenuke_object,new_x);
    if(firenuke_events.length>0)
        {
        var target=firenuke_events[0];
        var data=target[1];
        var pty=data[0];
        var grp=data[1];
        var chr=data[2];
        var xpos=object_get_x(firenuke_object);
        xpos-=object_get_width(firenuke_object)/2;
        var targetpos=object_get_x(get_hero(pty,grp,chr).html.pic);
        if(xpos>targetpos)
            {
            while (true)
                {
                target=firenuke_events.shift();
                default_impact_process_event(target);
                if(firenuke_events.length<1)
                    break;
                target=firenuke_events[0];
                data=target[1];
                var pty2=data[0];
                var grp2=data[1];
                var chr2=data[2];
                if(pty!=pty2 || grp!=grp2 || chr!=chr2)
                    break;
                }
            }
        }
    if(object_get_x(firenuke_object)>800)
        {
        object_delete(firenuke_object);
        firenuke_object=null;
        finish_event();
        }
    else
        setTimeout('firenuke_move('+[tpindex,tgindex,tcindex,range].join(',')+');',10);
    }

