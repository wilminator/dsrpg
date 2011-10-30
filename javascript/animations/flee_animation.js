function flee_animation_init()
    {
    var hero=get_hero(this.pindex,this.gindex,this.cindex);
    var object=hero.html;
    object_set_image(object.img,this.images[0],fighter_images);
    object_x(object.pic,hero.x_offset);
    object_y_base(object.pic,hero.y_offset);
    if(hero.current.HP==0)
        hide_cause_is_dead(this.pindex,this.gindex,this.cindex);
    play_sound(this.sounds[0]);
    return this.times[0];
    }

function flee_animation()
    {
    this.slide_object=null;
    }

function flee_animation_prep()
    {
    flee_animation.prototype=new animation();
    flee_animation.prototype.functions=[flee_animation_init];
    flee_animation.prototype.description=function ()
        {
        return {
            images:['flee image of main object'],
            imageloc:[fighter_images],
            sounds:['sound played when flee image is shown'],
            times:['ms before animation allows next sequence to begin']
            };
        };
    }
flee_animation.prototype.prep=flee_animation_prep;

if (animations==null)
    {
    var animations={};
    }
try{
    if (animation!=null)
        {
        flee_animation_prep();
        }
    }
catch (e) {}
animations['flee_animation']=flee_animation;
