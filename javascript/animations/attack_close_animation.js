function attack_close_animation_init()
    {
    //Get the object ot manipulate and data about where to go and from.
    var hero=get_hero(this.pindex,this.gindex,this.cindex);
    this.hero=hero;
    var object=hero.html;
    this.object=object;

    //Get the hero's location
    var bottom=object_get_y(object.pic)+object_get_height(object.img);
    this.bottom=bottom;
    var top=object_get_y(object.pic);
    var left=object_get_x(object.pic);
    this.left=left;
    var width=get_picture_x(object.pic);
    this.width=width;
    var height=get_picture_y(object.pic);
    var center=left+width/2;

    //OK, if the hero and target are BOTH not a player party then skip the motion
    if(this.pindex!=player_party && this.tpindex!=player_party)
        {
        this.invoke(3);
        return this.times[1];
        }

    //Get the target object and its location
    var tobject=fight.parties[this.tpindex].groups[this.tgindex].characters[this.tcindex].html.pic;
    var ttop=object_get_y(tobject);
    var tleft=object_get_x(tobject);
    var twidth=get_picture_x(tobject);
    var theight=get_picture_y(tobject);
    var tcenter=tleft+twidth/2;

    //Figure out the distance to travel.
    this.deltaX=tcenter-center;
    if(this.pindex==player_party)
        this.deltaY=(ttop+theight)-top-50;
    else
        this.deltaY=ttop-(top+height)+40;

    object_set_image(object.img,this.images[0],fighter_images);
    //Get the target destination.
    this.x_offset=(this.width-get_picture_x(object.pic))/2;

    object_x(object.pic,left+this.x_offset);
    object_y_base(object.pic,bottom);
    if(this.sounds[0])
        play_sound(this.sounds[0]);
    this.setTimeout(1,20);
    return this.times[0]*20+this.times[1];
    }

function attack_close_animation_closer()
    {
    var object=this.object;
    this.count++;
    //If we have made our count then advance to the attack.
    if(this.count>=this.times[0])
        {
        return this.invoke(2);
        }
    object_x(object.pic,this.left+this.x_offset+this.deltaX*this.count/this.times[0]);
    object_y_base(object.pic,this.bottom+this.deltaY*this.count/this.times[0]);
    this.setTimeout(1,20);
    }

function attack_close_animation_assult()
    {
    var object=this.object;
    object_set_image(object.img,this.images[1],fighter_images);
    this.x_offset=(this.width-get_picture_x(object.pic))/2;
    object_x(object.pic,this.hero.x_offset+this.x_offset+this.deltaX);
    object_y_base(object.pic,this.bottom+this.deltaY);

    if(this.sounds[1])
        play_sound(this.sounds[1]);

    //OK, if the hero and target are BOTH not a player party then skip the motion
    if(this.pindex!=player_party && this.tpindex!=player_party)
        {
        this.setTimeout(5,this.times[2]);
        }
    //Start the retreat on the next execution.
    this.setTimeout(3,this.times[2]);
    }

function attack_close_animation_retreat_init()
    {
    var object=this.object;
    object_set_image(object.img,this.images[2],fighter_images);
    this.x_offset=(this.width-get_picture_x(object.pic))/2;
    this.count=this.times[0]-1;
    //If we have made our count then advance to the cleanup.
    if(this.count<=0)
        {
        return this.invoke(5);
        }
    object_x(object.pic,this.left+this.x_offset+this.deltaX*this.count/this.times[0]);
    object_y_base(object.pic,this.bottom+this.deltaY*this.count/this.times[0]);
    this.setTimeout(4,20);
    }

function attack_close_animation_retreat()
    {
    var object=this.object;
    this.count--;
    //If we have made our count then advance to the attack.
    if(this.count<=0)
        {
        return this.invoke(5);
        }
    object_x(object.pic,this.left+this.x_offset+this.deltaX*this.count/this.times[0]);
    object_y_base(object.pic,this.bottom+this.deltaY*this.count/this.times[0]);
    object_z(object.pic,find_z(object.pic));
    this.setTimeout(4,20);
    }

function attack_close_to_base()
    {
    var personality=personalities[this.hero.personalityid];
    invoke_animation('base_animation',this.subject,this.target,0,[],personality.base_data);
    }

function attack_close_animation()
    {
    this.child_animation=null;
    this.count=0;
    this.x_offset=0;
    }

function attack_close_animation_prep()
    {
    attack_close_animation.prototype=new animation();
    attack_close_animation.prototype.functions=[
        attack_close_animation_init,
        attack_close_animation_closer,
        attack_close_animation_assult,
        attack_close_animation_retreat_init,
        attack_close_animation_retreat,
        attack_close_to_base];
    attack_close_animation.prototype.description=function ()
        {
        return {
            images:['advancing image',
                'attacking image',
                'retreating image'],
            imageloc:[fighter_images,
                fighter_images,
                fighter_images],
            sounds:['sound played when image advances',
                'sound played when image attacks',
                'sound played when image retreats'],
            times:['number of frames to advance and retreat',
                'ms into attack image for next sequence to begin',
                'ms to wait while attack image is displayed']
            };
        };
    }
attack_close_animation.prototype.prep=attack_close_animation_prep;

if (animations==null)
    {
    var animations={};
    }
try{
    if (animation!=null)
        {
        attack_close_animation_prep();
        }
    }
catch (e) {}
animations['attack_close_animation']=attack_close_animation;
