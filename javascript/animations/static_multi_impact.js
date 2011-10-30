function static_multi_impact_init()
    {
    var targetids={};
    //First identify the targets
    for(var event in this.events)
        {
        targetids[this.events[event][1].slice(0,3).join(',')]=this.events[event][1];
        }
    //Next create an object for each target
    for (var targetid in targetids)
        {
        var data=targetids[targetid];
        var tpindex=data[0];
        var tgindex=data[1];
        var tcindex=data[2];
        var initial_object=null;
        //If an image is specified, then make it.  Otherwise we will skip the first part.
        if(this.images && this.images[0])
            initial_object=object_create_image(this.images[0],fight_screen,{},effect_images);
        var target=object_get_center(get_hero(tpindex,tgindex,tcindex).html.pic,initial_object);
        //Determine random offset if asked for
        var rand=0;
        if(this.times && 3 in this.times && this.times[3])
            rand=Math.floor(Math.random()*-50);
        this.static_multi_impact_objects[targetid]={
            object:initial_object,
            x:target.x,y:target.y,count:rand,
            tpindex:tpindex,tgindex:tgindex,tcindex:tcindex
            };
        //Start an event
        start_event();
        }
    //Begin the animation loop.
    this.setTimeout(1,10);
    return this.times[1];
    }

function static_multi_impact_wait()
    {
    var index,data,cont=false;
    for(index in this.static_multi_impact_objects)
        {
        data=this.static_multi_impact_objects[index];
        //If distance<length, then use routine to move animation
        if(data.count<=this.times[2])
            {
            //When count gets to 0, then show the object, if it exists
            if(data.count==0)
                {
                if(data.object)
                    {
                    object_x(data.object,data.x);
                    object_y(data.object,data.y);
                    object_z(data.object,find_z(data.object)+1);
                    object_show(data.object);
                    //Play sound
                    play_sound(this.sounds[0]);
                    }
                else
                    {
                    //Set count to times[0], we are skipping this.
                    data.count=this.times[0];
                    }
                }
            //If count is past 0, then move the object
            if(data.count==this.times[0] || (data.count==this.times[2] && this.times[0]>this.times[2]))
                {
                //do the damage display.
                var tgtevents=filter_events(this.events,data.tpindex,data.tgindex,data.tcindex);
                for(index in tgtevents)
                    default_impact_process_event(tgtevents[index]);
                }
            data.count++;
            cont=true;
            }
        else
            {
            //Kill the initial pic
            if (data.object)
                object_delete(data.object);
            data.object=null;
            finish_event();
            delete this.static_multi_impact_objects[index];
            }
        }
    //If we need to continue, then loop
    if(cont)
        this.setTimeout(1,10);
    }

function static_multi_impact()
    {
    this.static_multi_impact_objects={};
    }

function static_multi_impact_prep()
    {
    static_multi_impact.prototype=new animation();
    static_multi_impact.prototype.functions=[static_multi_impact_init,static_multi_impact_wait];
    static_multi_impact.prototype.description=function ()
        {
        return {
            images:['static image to display on targets'],
            imageloc:[effect_images],
            sounds:['sound image makes as it appears on screen'],
            times:['frames before damage is displayed',
                'ms before animation allows next sequence to begin',
                'frames before animations are removed (150)',
                'if not 0 or empty, causes images to appear at random']
            };
        };
    static_multi_impact.prototype.static_multi_impact_wait=static_multi_impact_wait;
    }
static_multi_impact.prototype.prep=static_multi_impact_prep;

if (animations==null)
    {
    var animations={};
    }
try{
    if (animation!=null)
        {
        static_multi_impact_prep();
        }
    }
catch (e) {}
animations['static_multi_impact']=static_multi_impact;
