function static_individual_impact_init()
    {
    this.static_individual_object=object_create_image(this.images[0],fight_screen,{},effect_images);
    var target=object_get_center(get_hero(this.tpindex,this.tgindex,this.tcindex).html.pic,this.static_individual_object);
    object_x(this.static_individual_object,target.x);
    object_y(this.static_individual_object,target.y);
    object_z(this.static_individual_object,player_party==this.tpindex?150:50);
    object_show(this.static_individual_object);
    start_event();
    play_sound(this.sounds[0]);
    this.setTimeout(1,this.times[0]);
    return this.times[1];
    }

function static_individual_damage(tpindex,tgindex,tcindex,range)
    {
    default_impact_processor(this.tpindex,this.tgindex,this.tcindex,this.range,this.events)
    this.setTimeout(2,this.times[1]-this.times[0]);
    }

function static_individual_cleanup()
    {
    object_delete(this.static_individual_object);
    this.static_individual_object=null;
    finish_event();
    }

function static_individual_impact()
    {
    this.static_individual_object=null;
    }

function static_individual_prep()
    {
    static_individual_impact.prototype=new animation();
    static_individual_impact.prototype.functions=[static_individual_impact_init,static_individual_damage,static_individual_cleanup];
    static_individual_impact.prototype.description=function ()
        {
        return {
            images:['static image to display on target'],
            imageloc:[effect_images],
            sounds:['sound image makes as it appears on screen'],
            times:['ms before damage is displayed',
                'ms before animation allows next sequence to begin']
            };
        };
    }
static_individual_impact.prototype.prep=static_individual_prep;

if (animations==null)
    {
    var animations={};
    }
try{
    if (animation!=null)
        {
        static_individual_prep();
        }
    }
catch (e) {}
animations['static_individual_impact']=static_individual_impact;
