function die_animation_init()
    {
    var hero=get_hero(this.pindex,this.gindex,this.cindex);
    var object=hero.html;
    var bottom=object_get_y(object.pic)+object_get_height(object.img);
    var image=object_get_cached_image(this.images[0],fighter_images);
    var left=hero.x_offset+(object_get_width(object.img)-image.width)/2;
    object_set_image(object.img,this.images[0],fighter_images);
    object_x(object.pic,left);
    object_y_base(object.pic,bottom);
    start_event();
    if(this.sounds[0])
        play_sound(this.sounds[0]);
    if(this.sounds[1])
        play_sound(this.sounds[1]);
    this.setTimeout(1,this.times[1]);
    return this.times[0];
    }

function die_fade()
    {
    this.fade-=this.times[2];
    if(this.fade<0) this.fade=0;
    var hero=get_hero(this.pindex,this.gindex,this.cindex);
    var object=hero.html.img;
    object_opacity(object,this.fade);
    if(this.fade==0)
        {
        var personality=personalities[hero.personalityid];
        invoke_animation('base_animation',this.subject,this.target,0,[],personality.base_data);
        finish_event();
        }
    else
        this.setTimeout(1,10);
    }

function die_animation()
    {
    this.fade=100;
    }

function die_animation_prep()
    {
    die_animation.prototype=new animation();
    die_animation.prototype.functions=[die_animation_init,die_fade];
    die_animation.prototype.description=function ()
        {
        return {
            images:['death image of main object'],
            imageloc:[fighter_images],
            sounds:['sound 1 played when image is shown',
                'sound 2 played when image is shown'],
            times:['ms before animation allows next sequence to begin',
            'ms before image fade',
            'speed of image fade']
            };
        };
    }
die_animation.prototype.prep=die_animation_prep;

if (animations==null)
    {
    var animations={};
    }
try{
    if (animation!=null)
        {
        die_animation_prep();
        }
    }
catch (e) {}
animations['die_animation']=die_animation;
