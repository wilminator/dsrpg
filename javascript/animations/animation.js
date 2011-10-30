function invoke_animation(name,subject,target,range,events,data)
    {
    if(!(name in animations))
        return 0;
    var animation_constructor=animations[name];
    //var animation_instance=new animation_constructor();
    var animation_instance=new (animations[name])();
    return animation_instance.start(subject,target,range,events,data);
    }

function animation_start(subject,target,range,events,data)
    {
    this.subject=subject;
    this.pindex=subject.party;
    this.gindex=subject.group;
    this.cindex=subject.character;
    this.target=target;
    this.tpindex=target.party;
    this.tgindex=target.group;
    this.tcindex=target.character;
    this.data=data;
    this.range=range;
    this.events=events;
    this.images=data.images;
    this.sounds=data.sounds;
    this.times=data.times;
    return this.invoke(0);
    }

function animation_timeout(functionIndex,delay)
    {
    var that=this;
    timeoutFunction=function(){that.invoke(functionIndex);};
    setTimeout(timeoutFunction,delay);
    }

function animation_invoke(functionIndex)
    {
    this.invokeFunction=this.functions[functionIndex];
    return this.invokeFunction();
    }

//Abstract class animation.
function animation() {this.toString=function(){return "animation";};}

animation.prototype.start=animation_start;
animation.prototype.setTimeout=animation_timeout;
animation.prototype.invoke=animation_invoke;
//Functions must be defined for use with start and setTimeout.
animation.prototype.functions=[];
/*
Description must be set for admin interface to know what is needed.
slide_right_impact.prototype.description=function ()
    {
    return {
        images:['description of array of available images'],
        sounds:['description of array of available sounds'],
        times:['description of array of timing values']
        };
    }
*/

if (animations==null)
    var animations={};

for (var index in animations)
    animations[index].prototype.prep();
