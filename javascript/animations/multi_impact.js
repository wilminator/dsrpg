function multi_impact_init()
    {
    var targetids={};
    var event,targetid,data,tpindex,tgindex,tcindex,initial_object;
    var target,dx,dy,length;
    //First identify the targets
    for(event in this.events)
        {
        targetids[this.events[event][1].slice(0,3).join(',')]=this.events[event][1];
        }
    //Get the hero coordinates
    var hero=object_get_center(get_hero(this.pindex,this.gindex,this.cindex).html.pic);
    //Next create an object for each target
    for (targetid in targetids)
        {
        data=targetids[targetid];
        tpindex=data[0];
        tgindex=data[1];
        tcindex=data[2];
        initial_object=null;
        //If an image is specified, then make it.  Otherwise we will skip the first part.
        if(this.images && this.images[0])
            initial_object=object_create_image(this.images[0],fight_screen,{},effect_images);
        target=object_get_center(get_hero(tpindex,tgindex,tcindex).html.pic,initial_object);
        dx=target.x-hero.x;
        dy=target.y-hero.y;
        //Determine the distance of the data's path.
        length=Math.sqrt(dx*dx+dy*dy);
        //Dertermine the sine and cosine
        if(this.times && 0 in this.times)
            {
            dx=dx*this.times[0]/length;
            dy=dy*this.times[0]/length;
            }
        else
            {
            alert("No times.");
            }
        //Determine random offset if asked for
        var rand=0;
        if(this.times && 3 in this.times && this.times[3])
            rand=Math.floor(Math.random()*-50);
        this.multi_impact_objects[targetid]={
            object:initial_object,
            x:hero.x,y:hero.y,
            tx:target.x,ty:target.y,
            dx:dx,dy:dy,explode:false,
            count:rand,length:length,distance:0,
            tpindex:tpindex,tgindex:tgindex,tcindex:tcindex
            };
        //Start an event
        start_event();
        }
    //Begin the animation loop.
    this.setTimeout(1,10);
    return this.times[1];
    }

function multi_impact_create_explosion(tpindex,tgindex,tcindex)
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
        count:this.times[2],cindex:tcindex};
    if (data.object)
        {
        object_x(data.object,data.x);
        object_y(data.object,data.y);
        object_z(data.object,find_z(data.object)+1);
        object_show(data.object);
        }
    //Play sound
    play_sound(this.sounds[1]);
    return data;
    }

function multi_impact_move()
    {
    var index,data,cont=false;
    for(index in this.multi_impact_objects)
        {
        data=this.multi_impact_objects[index];
        //If distance<length, then use routine to move animation
        if(data.explode==false)
            {
            if(data.distance<data.length)
                {
                //If count is past 0, then move the object
                if(data.count>0)
                    {
                    data.distance+=this.times[0];
                    data.x+=data.dx;
                    data.y+=data.dy;
                    }
                //If count is at or past 0 then move the object
                if(data.object && data.count>=0)
                    {
                    //Position and show the data
                    object_x(data.object,data.x);
                    object_y(data.object,data.y);
                    object_z(data.object,find_z(data.object)+1);
                    }
                //When count gets to 0, then show the object, if it exists
                if(data.count==0)
                    {
                    if(data.object)
                        {
                        object_show(data.object);
                        //Play sound
                        play_sound(this.sounds[0]);
                        }
                    else
                        {
                        //Set count to length, we are skipping this.
                        data.distance=data.length;
                        }
                    }
                data.count++;
                }
            else
                {
                //Kill the initial pic
                if (data.object)
                    object_delete(data.object);
                data.object=null;
                this.multi_impact_objects[index]=this.multi_impact_create_explosion(data.tpindex,data.tgindex,data.tcindex);
                }
            cont=true;
            }
        else if(data.count>0)
            {
            data.count--;
            if(data.count==0)
                {
                //Kill the explosion pic
                if (data.object)
                    object_delete(data.object);
                data.object=null;
                finish_event();
                }
            else
                cont=true;
            }
        }
    //If we need to continue, then loop
    if(cont)
        this.setTimeout(1,10);
    }

function multi_impact()
    {
    this.multi_impact_objects={};
    }

function multi_impact_prep()
    {
    multi_impact.prototype=new animation();
    multi_impact.prototype.functions=[multi_impact_init,multi_impact_move];
    multi_impact.prototype.description=function ()
        {
        return {
            images:['initial projectile image to display on screen',
                'imapct image when projectile hits initial target',
                'projectile image of sub-projectiles'],
            imageloc:[effect_images,
                effect_images,
                effect_images],
            sounds:['sound image makes as it appears on screen',
                'sound made as each image hits its target'],
            times:['speed of initial projectile in pixels per frame',
                'ms before animation allows next sequence to begin',
                'frames before explosions are removed (150)',
                'if not 0 or empty, causes images to appear at random']
            };
        };
    multi_impact.prototype.multi_impact_create_explosion=multi_impact_create_explosion;
    multi_impact.prototype.multi_impact_move=multi_impact_move;
    }
multi_impact.prototype.prep=multi_impact_prep;

if (animations==null)
    {
    var animations={};
    }
try{
    if (animation!=null)
        {
        multi_impact_prep();
        }
    }
catch (e) {}
animations['multi_impact']=multi_impact;
