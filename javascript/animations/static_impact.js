var default_static_object=null;
var default_static_events=null;

function static_impact_init()
    {
    this.static_object=object_create_image(this.images[0],fight_screen,{},effect_images);
    object_x(this.static_object,0);
    object_y(this.static_object,player_party==this.tpindex?400:200);
    object_z(this.static_object,player_party==this.tpindex?150:50);
    object_show(this.static_object);
    start_event();
    play_sound(this.sounds[0]);
    this.setTimeout(1,this.times[0]);
    return this.times[1];
    }

function static_damage(tpindex,tgindex,tcindex,range)
    {
    default_impact_processor(this.tpindex,this.tgindex,this.tcindex,this.range,this.events)
    this.setTimeout(2,this.times[1]-this.times[0]);
    }

function static_cleanup()
    {
    object_delete(this.static_object);
    this.static_object=null;
    finish_event();
    }

function static_impact()
    {
    this.static_object=null;
    }

function static_prep()
    {
    static_impact.prototype=new animation();
    static_impact.prototype.functions=[static_impact_init,static_damage,static_cleanup];
    static_impact.prototype.description=function ()
        {
        return {
            images:['static image to display on screen'],
            imageloc:[effect_images],
            sounds:['sound image makes as it appears on screen'],
            times:['ms before damage is displayed',
                'ms before animation allows next sequence to begin']
            };
        };
    }
static_impact.prototype.prep=static_prep;

if (animations==null)
    {
    var animations={};
    }
try{
    if (animation!=null)
        {
        static_prep();
        }
    }
catch (e) {}
animations['static_impact']=static_impact;
