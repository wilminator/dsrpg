//Sprite data
var sprites=[];
//Genral format:
//{id:0, x:0, y:0, z:0, dx:0, dy:0, duration:0, queue:[], image:src, callback:function ,timer:0, html:object}
//id is an identifier used to track the sprite.  Defined by use
//x,y,z are coordinates linked to top,left, and z-order
//dx and dy are velocities, steps are number of frames left to move
//queue is an array of future movements to take containing arrays of triplets: [dx,dy,steps]
//image is the sprite graphic
//callabck gets called everytime timer decerements to 0 if present
//html is the object representing the sprite

function sprite_get(id)
    {
    var index;
    for (index in sprites)
        if(sprites[index].id==id)
            return index;
    return null;
    }

function sprite_create(id,image,x,y,z,callback,attributes)
    {
    var sprite={id:id, x:x, y:y, z:z, dx:x, dy:y, cx:x, cy:y, duration:0, position:0, queue:[], image:image, callback:callback ,timer:0, html:null, attributes:attributes};
    var index=sprite_get(id);
    if(index==null)
        sprites.push(sprite);
    else
        sprites[index]=sprite;
    }

function sprite_locate(id,x,y,z)
    {
    var index=sprite_get(id);
    if(index==null)
        return null;
    var sprite=sprites[index];
    sprite.x=x;
    sprite.y=y;
    sprite.z=z;
    sprite.dx=x;
    sprite.dy=y;
    sprite.cx=x;
    sprite.cy=y;
    sprite.duration=0;
    sprite.queue=[];
    return true;
    }

function sprite_queue_movement(id,actions)
    {
    var index=sprite_get(id);
    if(index==null)
        return null;
    var sprite=sprites[index];
    while (actions.length>0 && sprite.queue.length<16)
        sprite.queue.push(actions.shift());
    return sprite.queue.length<16;
    }

function sprite_move(id,start_x,start_y,z,end_x,end_y,duration)
    {
    sprite_locate(id,start_x,start_y,z);
    sprite_move_to(id,end_x,end_y,z,duration);
    }

function sprite_grid_move_to(id,x,y,z,duration)
    {
    var index=sprite_get(id);
    if(index==null)
        return null;
    var sprite=sprites[index];
    var start_x=sprite.dx;
    var start_y=sprite.dy;
    sprite.queue=[];
    var size,perc;
    x=Math.floor(x/tile_width)*tile_width;
    y=Math.floor(y/tile_height)*tile_height;
    while(start_x!=x || start_y!=y)
        {
        if ((start_x!=x && Math.random()<.5) || start_y==y || start_x%tile_width!=0)
            {
            //Fix x
            if(start_x<x)
                {
                //Determine distance to travel
                size=start_x%tile_width==0?tile_width:tile_width-(start_x%tile_width);
                //Increase x
                start_x+=size;
                }
            else
                {
                //Determine distance to travel
                size=start_x%tile_width==0?tile_width:start_x%tile_width;
                //Decrease x
                start_x-=size;
                }
            perc=size/tile_width;
            }
        else
            {
            //Fix y
            if(start_y<y)
                {
                //Determine distance to travel
                size=start_y%tile_height==0?tile_height:tile_height-(start_y%tile_height);
                //Increase y
                start_y+=size;
                }
            else
                {
                //Determine distance to travel
                size=start_y%tile_height==0?tile_height:start_y%tile_height;
                //Decrease y
                start_y-=size;
                }
            perc=size/tile_height;
            }
        if(sprite_queue_movement(id,[[start_x,start_y,Math.floor(duration*perc)]])==false)
            break;
        }
    sprite.z=z;
    return true;
    }

function sprite_animate()
    {
    var sprite;
    var index;
    for (index in sprites)
        {
        sprite=sprites[index];
        sprite.position+=refresh_speed;
        while(sprite.position>=sprite.duration)
            {
            sprite.x=sprite.dx;
            sprite.y=sprite.dy;
            sprite.position-=sprite.duration;
            if(sprite.queue.length>0)
                {
                sprite.dx=sprite.queue[0][0];
                sprite.dy=sprite.queue[0][1];
                sprite.duration=sprite.queue[0][2];
                sprite.queue.shift();
                }
            else
                {
                sprite.duration=0;
                sprite.position=0;
                break;
                }
            }
        if(sprite.duration==0)
            {
            sprite.cx=sprite.x;
            sprite.cy=sprite.y;
            }
        else
            {
            sprite.cx=sprite.x+(sprite.dx-sprite.x)*sprite.position/sprite.duration;
            sprite.cy=sprite.y+(sprite.dy-sprite.y)*sprite.position/sprite.duration;
            }
        if(sprite.callback)
            {
            sprite.timer--;
            if(sprite.timer<=0)
                sprite.timer=sprite.callback(sprite);
            }
        }
    return true;
    }

function sprite_render(submap)
    {
    var sprite;
    var index;
    for (index in sprites)
        {
        sprite=sprites[index];
        if(!sprite.html)
            {
            sprite.html=object_create_image(sprite.image,submap.html,sprite.attributes);
            object_default_visibility(sprite.html);
            }
        object_x(sprite.html,sprite.cx-submap.location[1]);
        object_y(sprite.html,sprite.cy-submap.location[2]);
        object_z(sprite.html,sprite.z);
        }
    return true;
    }

function sprite_transfer(submap)
    {
    var old_sprites=[];
    var index,sprite;
    for(index in sprites)
        {
        sprite=sprites[index];
        if(sprite.html)
            {
            old_sprites.push(sprite.html);
            sprite.html=object_create_image(sprite.image,submap.html,sprite.attributes);
            object_default_visibility(sprite.html);
            }
        }
    return old_sprites;
    }

function sprite_purge(old_sprites)
    {
    var index;
    for(index in old_sprites)
        object_delete(old_sprites[index]);
    return true;
    }


