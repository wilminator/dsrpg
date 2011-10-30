var fireball_events=null;
var fireballs=[];
var fireball_object=null;

function fireball_impact(pindex,gindex,cindex,tpindex,tgindex,tcindex,range,events)
    {
    fireball_object=new ranged_arc_impact();
    return fireball_object.start(pindex,gindex,cindex,tpindex,tgindex,tcindex,range,events,['fireball.png','explosion.gif','small_fireball.png'],['Flame4.ogg','Flame1.ogg'],[15,1000,0.25,30,15]);
    fireball_events=events;
    var fireball=object_create_image('fireball.png',fight_screen,{},effect_images);
    var hero=object_get_center(get_hero(pindex,gindex,cindex).html.pic,fireball);
    var target=object_get_center(get_hero(tpindex,tgindex,tcindex).html.pic,fireball);
    //Init a fireball object.
    fireballs=[];
    fireballs[0]={
        object:fireball,
        x:hero.x,y:hero.y,
        tx:target.x,ty:target.y,
        count:0
        };
    //Create a shortcut
    var fireball=fireballs[0];
    //Determine the distance of the fireball's path.
    fireball.length=Math.sqrt((fireball.tx-fireball.x)*(fireball.tx-fireball.x)+(fireball.ty-fireball.y)*(fireball.ty-fireball.y));
    //Dertermine the sine and cosine
    fireball.dx=(fireball.tx-fireball.x)*15/fireball.length;
    fireball.dy=(fireball.ty-fireball.y)*15/fireball.length;
    //Position and show the fireball
    object_x(fireball.object,fireball.x);
    object_y(fireball.object,fireball.y);
    object_z(fireball.object,find_z(fireball.object)+1);
    object_show(fireball.object);
    //Start the event
    start_event();
    //Begin the animation loop.
    setTimeout('fireball_move('+[tpindex,tgindex,tcindex,range].join(',')+');',10);
    play_sound('Flame4.ogg');
    return 1000;
    }

//Possibly incorporate the event handler into two default functions-
//one for left to right and one for right to left.
function fireball_move(tpindex,tgindex,tcindex,range)
    {
    //Shortcut the fireball
    var fireball=fireballs[0];
    //Adjust its position
    fireball.x+=fireball.dx;
    fireball.y+=fireball.dy;
    object_x(fireball.object,fireball.x);
    object_y(fireball.object,fireball.y);
    object_z(fireball.object,find_z(fireball.object)+1);
    //Adjust counter by the length we just moved.
    fireball.count+=15;
    //If we have traveled at least length, then we move to phase two- mini fireballs and explosions.
    if(fireball.count>=fireball.length)
        {
        //Kill the fireball pic
        object_delete(fireball.object);
        //Reinit the fireballs data structure
        fireballs=[];
        var index,data,acc=-1;
        var base=object_get_center(get_hero(tpindex,tgindex,tcindex).html.pic);
        for(index=-range;index<=range;index++)
            {
            if(index+tcindex>=0 && index+tcindex<get_group_length(tpindex,tgindex))
                {
                var wait=30*Math.abs(index);
                if(index==0)
                    data=fireball_create_explosion(tpindex,tgindex,tcindex+index);
                else
                    data=fireball_create_small_fireball(tpindex,tgindex,tcindex+index,base,wait);
                //Locate and show the object
                object_x(data.object,data.x);
                object_y(data.object,data.y);
                object_z(data.object,find_z(data.object)+1);
                //Push onto our list of animmations
                fireballs.push(data);
                //Start a new event
                start_event();
                }
            }
        finish_event();
        setTimeout('fireball_explode('+[tpindex,tgindex,tcindex,range].join(',')+');',10);
        }
    //Otherwise loop
    else
        setTimeout('fireball_move('+[tpindex,tgindex,tcindex,range].join(',')+');',10);
    }

function fireball_create_explosion(tpindex,tgindex,tcindex)
    {
    var index;
    var explosion=object_create_image('explosion.gif',fight_screen,{},effect_images);
    var hero=get_hero(tpindex,tgindex,tcindex);
    var target=object_get_center(hero.html.pic,explosion);
    var tgtevents=filter_events(fireball_events,tpindex,tgindex,tcindex);
    for(index in tgtevents)
        default_impact_process_event(tgtevents[index]);
    var data={
        object:explosion,
        explode:true,x:target.x,y:target.y,dx:0,dy:0,dyy:0,count:150,cindex:tcindex};
    object_show(data.object);
    play_sound('Flame1.ogg');
    return data;
    }

function fireball_create_small_fireball(tpindex,tgindex,tcindex,base,wait)
    {
    var acc=0.25;
    var fireball=object_create_image('small_fireball.png',fight_screen,{},effect_images);
    var target=object_get_center(get_hero(tpindex,tgindex,tcindex).html.pic);
    var data={
        object:fireball,
        explode:false,x:base.x,y:base.y,
        dx:(target.x-base.x)/wait,
        dy:(target.y-base.y)/wait-acc*wait/2,
        dyy:acc,count:wait,cindex:tcindex};
    object_show(data.object);
    return data;
    }

function fireball_explode(tpindex,tgindex,tcindex,range)
    {
    var index,data,cont=false;
    for(index in fireballs)
        {
        data=fireballs[index];
        if(data.count>0)
            {
            data.count--;
            data.x+=data.dx;
            data.y+=data.dy;
            data.dy+=data.dyy;
            if(data.count==0)
                {
                //Kill the fireball pic
                object_delete(data.object);
                //If this was not an explosion, then create one.
                if(data.explode==false)
                    {
                    //center on this target
                    data=fireball_create_explosion(tpindex,tgindex,data.cindex);
                    object_show(data.object);
                    //Store data
                    fireballs[index]=data;
                    }
                }
            //Locate and show the object
            if(data.count>0)
                {
                object_x(data.object,data.x);
                object_y(data.object,data.y);
                object_z(data.object,find_z(data.object)+1);
                cont=true;
                }
            else
                finish_event();
            }
        }
    //If we need to continue, then loop
    if(cont)
        setTimeout('fireball_explode('+[tpindex,tgindex,tcindex,range].join(',')+');',10);
    else
        fireball_events=null;
    }
