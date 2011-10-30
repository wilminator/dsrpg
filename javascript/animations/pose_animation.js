function pose_animation_init()
    {
    var hero=get_hero(this.pindex,this.gindex,this.cindex);
    var object=hero.html;
    var bottom=object_get_y(object.pic)+object_get_height(object.img);
    var image=object_get_cached_image(this.images[0],fighter_images);
    var left=hero.x_offset+(object_get_width(object.img)-image.width)/2;
    object_set_image(object.img,this.images[0],fighter_images);
    object_x(object.pic,left);
    object_y_base(object.pic,bottom);
    if(this.sounds[0])
        play_sound(this.sounds[0]);
    this.setTimeout(1,this.times[1]);
    return this.times[0];
    }

function pose_to_base()
    {
    var hero=get_hero(this.pindex,this.gindex,this.cindex);
    var personality=personalities[hero.personalityid];
    invoke_animation('base_animation',this.subject,this.target,0,[],personality.base_data);
    }

function pose_animation()
    {
    this.child_animation=null;
    }

function pose_animation_prep()
    {
    pose_animation.prototype=new animation();
    pose_animation.prototype.functions=[pose_animation_init,pose_to_base];
    pose_animation.prototype.description=function ()
        {
        return {
            images:['new image of main object'],
            imageloc:[fighter_images],
            sounds:['sound played when image is shown'],
            times:['ms before animation allows next sequence to begin',
            'ms before base animation is redisplayed']
            };
        };
    }
pose_animation.prototype.prep=pose_animation_prep;

if (animations==null)
    {
    var animations={};
    }
try{
    if (animation!=null)
        {
        pose_animation_prep();
        }
    }
catch (e) {}
animations['pose_animation']=pose_animation;
