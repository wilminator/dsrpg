function ranged_arc_impact_init()
    {
    var initial_object=null;
    var count=0;
    //If an image is specified, then make it.  Otherwise we will skip the first part.
    if(this.images && this.images[0])
        initial_object=object_create_image(this.images[0],fight_screen,{},effect_images);
    var hero=object_get_center(get_hero(this.pindex,this.gindex,this.cindex).html.pic);
    var target=object_get_center(get_hero(this.tpindex,this.tgindex,this.tcindex).html.pic,initial_object);
    //Init a ranged_arc object.
    this.ranged_arcs=[];
    this.ranged_arcs[0]={
        object:initial_object,
        x:hero.x,y:hero.y,
        tx:target.x,ty:target.y,
        count:0
        };
    //Create a shortcut
    var ranged_arc=this.ranged_arcs[0];
    //Determine the distance of the ranged_arc's path.
    ranged_arc.length=Math.sqrt((ranged_arc.tx-ranged_arc.x)*(ranged_arc.tx-ranged_arc.x)+(ranged_arc.ty-ranged_arc.y)*(ranged_arc.ty-ranged_arc.y));
    //Dertermine the sine and cosine
    if(this.times && 0 in this.times)
        {
        ranged_arc.dx=(ranged_arc.tx-ranged_arc.x)*this.times[0]/ranged_arc.length;
        ranged_arc.dy=(ranged_arc.ty-ranged_arc.y)*this.times[0]/ranged_arc.length;
        }
    else
        {
        alert("No times.");
        }
    if(initial_object)
        {
        //Position and show the ranged_arc
        object_x(ranged_arc.object,ranged_arc.x);
        object_y(ranged_arc.object,ranged_arc.y);
        object_z(ranged_arc.object,find_z(ranged_arc.object)+1);
        object_show(ranged_arc.object);
        }
    else
        {
        //Set count to length, we are skipping this.
        ranged_arc.count=ranged_arc.length;
        }
    //Start the event
    start_event();
    //Play sound
    play_sound(this.sounds[0]);
    //Begin the animation loop.
    this.setTimeout(1,10);
    return this.times[1];
    }

//Possibly incorporate the event handler into two default functions-
//one for left to right and one for right to left.
function ranged_arc_move()
    {
    //Shortcut the ranged_arc
    var ranged_arc=this.ranged_arcs[0];
    //Adjust its position
    if(ranged_arc.object)
        {
        ranged_arc.x+=ranged_arc.dx;
        ranged_arc.y+=ranged_arc.dy;
        object_x(ranged_arc.object,ranged_arc.x);
        object_y(ranged_arc.object,ranged_arc.y);
        object_z(ranged_arc.object,find_z(ranged_arc.object)+1);
        //Adjust counter by the length we just moved.
        ranged_arc.count+=this.times[0];
        }
    //If we have traveled at least length, then we move to phase two- mini ranged_arcs and explosions.
    if(ranged_arc.count>=ranged_arc.length)
        {
        //Kill the ranged_arc pic
        if (ranged_arc.object)
            object_delete(ranged_arc.object);
        //Reinit the ranged_arcs data structure
        this.ranged_arcs=[];
        var index,data;
        var base=object_get_center(get_hero(this.tpindex,this.tgindex,this.tcindex).html.pic);
        for(index=-this.range;index<=this.range;index++)
            {
            if(index+this.tcindex>=0 && index+this.tcindex<get_group_length(this.tpindex,this.tgindex))
                {
                var wait=this.times[3]*Math.abs(index);
                if(index==0)
                    data=this.ranged_arc_create_explosion(this.tpindex,this.tgindex,this.tcindex+index);
                else
                    data=this.ranged_arc_create_small_ranged_arc(this.tpindex,this.tgindex,this.tcindex+index,base,wait);
                //Locate and show the object
                if (data.object)
                    {
                    object_x(data.object,data.x);
                    object_y(data.object,data.y);
                    object_z(data.object,find_z(data.object)+1);
                    }
                //Push onto our list of animmations
                this.ranged_arcs.push(data);
                //Start a new event
                start_event();
                }
            }
        finish_event();
        this.setTimeout(2,10);
        }
    else
        //Otherwise loop
        this.setTimeout(1,10);
    }

function ranged_arc_create_explosion(tpindex,tgindex,tcindex)
    {
    var index;
    var explosion_object=null;
    if (this.images[1])
        explosion_object=object_create_image(this.images[1],fight_screen,{},effect_images);
    var target=object_get_center(get_hero(tpindex,tgindex,tcindex).html.pic,explosion_object);
    var tgtevents=filter_events(this.events,tpindex,tgindex,tcindex);
    for(index in tgtevents)
        default_impact_process_event(tgtevents[index]);
    var data={
        object:explosion_object,explode:true,
        x:target.x,y:target.y,dx:0,dy:0,dyy:0,
        count:this.times[4],cindex:tcindex};
    if (data.object)
        object_show(data.object);
    //Play sound
    play_sound(this.sounds[1]);
    return data;
    }

function ranged_arc_create_small_ranged_arc(tpindex,tgindex,tcindex,base,wait)
    {
    var acc=this.times[2];
    var fireball_object=null;
    if (this.images[2])
        fireball_object=object_create_image(this.images[2],fight_screen,{},effect_images);
    var target=object_get_center(get_hero(tpindex,tgindex,tcindex).html.pic,fireball_object);
    var data={
        object:fireball_object,
        explode:false,x:base.x,y:base.y,
        dx:(target.x-base.x)/wait,
        dy:(target.y-base.y)/wait-acc*wait/2,
        dyy:acc,count:wait,cindex:tcindex};
    if (data.object)
        object_show(data.object);
    return data;
    }

function ranged_arc_explode()
    {
    var index,data,cont=false;
    for(index in this.ranged_arcs)
        {
        data=this.ranged_arcs[index];
        if(data.count>0)
            {
            data.count--;
            data.x+=data.dx;
            data.y+=data.dy;
            data.dy+=data.dyy;
            if(data.count==0)
                {
                //Kill the ranged_arc pic
                if (data.object)
                    object_delete(data.object);
                data.object=null;
                //If this was not an explosion, then create one.
                if(data.explode==false)
                    {
                    //center on this target
                    data=this.ranged_arc_create_explosion(this.tpindex,this.tgindex,data.cindex);
                    //Store data
                    this.ranged_arcs[index]=data;
                    }
                }
            //Locate and show the object
            if (data.count>0)
                {
                if (data.object)
                    {
                    object_x(data.object,data.x);
                    object_y(data.object,data.y);
                    object_z(data.object,find_z(data.object)+1);
                    cont=true;
                    }
                }
            else
                {
                finish_event();
                }
            }
        }
    //If we need to continue, then loop
    if(cont)
        this.setTimeout(2,10);
    }

function ranged_arc_impact()
    {
    this.ranged_arc_object=[];
    }

function ranged_arc_prep()
    {
    ranged_arc_impact.prototype=new animation();
    ranged_arc_impact.prototype.functions=[ranged_arc_impact_init,ranged_arc_move,ranged_arc_explode];
    ranged_arc_impact.prototype.description=function ()
        {
        return {
            images:['initial projectile image to display on screen',
                'imapct image when projectile hits initial target',
                'projectile image of sub-projectiles'],
            imageloc:[effect_images,
                effect_images,
                effect_images],
            sounds:['sound image makes as it appears on screen',
                'sound made as each fireball hits a target'],
            times:['speed of initial projectile in pixels per frame',
                'ms before animation allows next sequence to begin',
                'vertical acceleration affecting sub-projectiles (0.25)',
                'frames to reach successive targets (30)',
                'frames before explosions are removed (150)']
            };
        };
    ranged_arc_impact.prototype.ranged_arc_create_explosion=ranged_arc_create_explosion;
    ranged_arc_impact.prototype.ranged_arc_create_small_ranged_arc=ranged_arc_create_small_ranged_arc;
    ranged_arc_impact.prototype.ranged_arc_explode=ranged_arc_explode;
    }
ranged_arc_impact.prototype.prep=ranged_arc_prep;

if (animations==null)
    {
    var animations={};
    }
try{
    if (animation!=null)
        {
        ranged_arc_prep();
        }
    }
catch (e) {}
animations['ranged_arc_impact']=ranged_arc_impact;
