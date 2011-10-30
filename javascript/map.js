//Map and submap data
var submaps=null;
var map=null;

//Submap responsibilities
var visible_submap;
var refreshing_submap;

//Renderer timeout
var renderer_timeout=null;

function make_minimap(submap_index,minimap_index)
    {
    //Location:[map_id,horiz_slice,vert_slice]
    var minimap={html:object_get('minimap'+submap_index+'-'+minimap_index),
        location:[null,null,null],
        tiles:new Array(miniMAP_HEIGHT),
        data:null,
        requested:false,
        loaded:false};
    var base='tile'+submap_index+'-'+minimap_index+'-';
    var index,index2,object;
    for(index=0;index<miniMAP_HEIGHT;index++)
        {
        minimap.tiles[index]=new Array(miniMAP_WIDTH);
        for (index2=0;index2<miniMAP_WIDTH;index2++)
            {
            //minimap.tiles[index][index2]=object_get(base+index+'-'+index2);
            object=new Image;
            object.src='x.png';
            object.className='t';
            object['onmouseover']=function(){return onmouseover_tiles(this);};
            object['onclick']=function(){return onclick_tiles(this);};
            object_x(object,index2*tile_width);
            object_y(object,index*tile_height);
            minimap.tiles[index][index2]=minimap.html.appendChild(object);
            }
        }
    return minimap;
    }

function find_submap_coordinates(map,x,y)
    {
    //It is assumed that x and y are the center coordinates of the new
    //submap.
    //Determine the number of good tiles in the submap in each direction.
    var h_tiles=(subMAP_WIDTH-4)*miniMAP_WIDTH*tile_width;
    var v_tiles=(subMAP_HEIGHT-4)*miniMAP_HEIGHT*tile_height;
    //Find and return good x and y coordinates for this submap.
    //Our good X is a multiple of h_tiles minus the width of one minimap.
    var good_x=Math.floor(x/h_tiles)*h_tiles-miniMAP_WIDTH*tile_width*2;
    //Our good Y is a multiple of v_tiles minus the height of one minimap.
    var good_y=Math.floor(y/v_tiles)*v_tiles-miniMAP_HEIGHT*tile_height*2;
    //Return the result
    return [map,good_x,good_y];
    }

function generate_minimap_assignments()
    {
    var locations=new Array(subMAP_HEIGHT);
    var assignments=new Array(subMAP_HEIGHT);
    var index,index2,index3;
    var available=[];

    //Finish generating the arrays
    for(index=0;index<subMAP_HEIGHT;index++)
        {
        locations[index]=new Array(subMAP_WIDTH);
        assignments[index]=new Array(subMAP_WIDTH);
        }

    //Now fill in the locations table.
    for(index=0;index<subMAP_HEIGHT;index++)
        for(index2=0;index2<subMAP_WIDTH;index2++)
            locations[index][index2]=[refreshing_submap.location[0],
                refreshing_submap.location[1]+index2*tile_width*miniMAP_WIDTH,
                refreshing_submap.location[2]+index*tile_height*miniMAP_HEIGHT];

    //Now check every location to see if any minimaps conform
    for(index=0;index<subMAP_HEIGHT;index++)
        for(index2=0;index2<subMAP_WIDTH;index2++)
            for(index3=0;index3<subMAP_WIDTH*subMAP_HEIGHT;index3++)
                {
                if(refreshing_submap.minimaps[index3].location.join()==locations[index][index2].join())
                    {
                    assignments[index][index2]=index3;
                    }
                else
                    {
                    assignments[index][index2]=false;
                    available.push(index3);
                    refreshing_submap.ready=false;
                    }
                }

    //Next assign loading assignments to worthless minimaps
    //Wrap up by assigning the minimap assignments to submap.layout
    for(index=0;index<subMAP_HEIGHT;index++)
        for(index2=0;index2<subMAP_WIDTH;index2++)
            {
            if(assignments[index][index2]==false)
                {
                index3=available.pop();
                refreshing_submap.minimaps[index3].location=locations[index][index2];
                refreshing_submap.minimaps[index3].requested=false;
                refreshing_submap.minimaps[index3].loaded=false;
                assignments[index][index2]=index3;
                }
            refreshing_submap.layout[index][index2]=refreshing_submap.minimaps[assignments[index][index2]];
            object_x(refreshing_submap.layout[index][index2].html,tile_width*miniMAP_WIDTH*index2);
            object_y(refreshing_submap.layout[index][index2].html,tile_height*miniMAP_HEIGHT*index);
            }
    }

//Develop a wrapper to get data asynchronously.
function load_minimap(minimap_index,loader)
    {
    var minimap=refreshing_submap.minimaps[minimap_index];
    if(minimap.requested==false)
        {
        //We now know we need to load this minimap.
        //Check to see if the visible submap has our data.
        var index;
        for(index in visible_submap.minimaps)
            if(visible_submap.minimaps[index].location.join(',')
                == minimap.location.join(','))
                {
                //We have a winner!
                //Call receive_minimap_data with the data from this one.
                receive_minimap_data(visible_submap.minimaps[index].data,minimap_index,loader);
                //Return true because we found one.
                return true;
                }
        //First request the data we need.
        request_minimap_data(minimap,minimap_index,loader);
        //The catch now is this will be completed when
        //PHP sends us the map data.
        //Return false becacuse the map is not ready.
        return false;
        }
    //Return true because the map is ready.
    }

//this runs on the server
//Develop a wrapper to get data asynchronously.
function request_minimap_data(minimap,minimap_index,loader)
    {
    //First find data offsets for this dump.
    var temp_x=minimap.location[1]/tile_width;
    var temp_y=minimap.location[2]/tile_height;
    //Constrain the offsets to 0 and MAP_WIDTH/height
    temp_x-=Math.floor(temp_x/MAP_WIDTH)*MAP_WIDTH;
    temp_y-=Math.floor(temp_y/MAP_HEIGHT)*MAP_HEIGHT;
    //RPC to the server
    data_request('fetch_map_data',minimap.location[0],temp_x%MAP_WIDTH,temp_y%MAP_HEIGHT,minimap_index,loader);
    //Now mark the minimap as requested.
    minimap.requested=true;
    //Just in case mark the minmap as not loaded.
    minimap.loaded=false;
    }


function receive_minimap_data(map,minimap_index,loader)
    {
    var minimap=refreshing_submap.minimaps[minimap_index];

    //PHP sent us the map data.
    //Now process it.
    var index,index2;
    for(index=0;index<miniMAP_HEIGHT;index++)
        for(index2=0;index2<miniMAP_WIDTH;index2++)
            minimap.tiles[index][index2].src=tileimgs[map[index][index2]].src;
    //Store the map data in case we need it again.
    minimap.data=map;
    //This minmap is now ready.
    minimap.loaded=true;
    //Now mark the minimap as requested just in case.
    minimap.requested=true;
    //Check to see if all minimaps are loaded.
    //If so, then make the refreshing_submap ready.
    var ready=true;
    var count=0;
    for(index in refreshing_submap.minimaps)
        if(refreshing_submap.minimaps[index].loaded==false)
            ready=false;
        else
            count++;
    refreshing_submap.ready=ready;
    window.status=count.toString()+'/'+refreshing_submap.minimaps.length.toString();
    if(loader)
        {
        set_loader_percentage(count*100/refreshing_submap.minimaps.length);
        if (ready)
            map_load_done();
        }
    }

function force_submap_completion(map,x,y,loader)
    {
    //Check the submap to see if it is ready.
    //If not, then make it ready.
    if(refreshing_submap.ready==false)
        {
        //First, check to see if the right location was being loaded.
        var coords=find_submap_coordinates(map,x,y);
        if(refreshing_submap.location.join()!=coords.join())
            {
            //We must restructure this refreshing_submap at coords.
            refreshing_submap.location=coords;
            //Generate the minimap assignemnts.
            generate_minimap_assignments();
            }
        //Now we have a valid refreshing_submap location.
        //Next cause any unloaded minimaps to be loaded.
        var index;
        for(index=0;index<subMAP_WIDTH*subMAP_HEIGHT;index++)
            load_minimap(index,loader);
        //All the missing refreshing_submaps have been requested.
        //Now we wait for everything to come together.
        //Return false to let the renderer know we are not ready.
        return false;
        }
    //We can display.
    return true;
    }

function refresh_submap(map,x,y)
    {
    //First, check to see if the right location was being loaded.
    var coords=find_submap_coordinates(map,x,y);
    if(refreshing_submap.location.join()!=coords.join())
        {
        //We must restructure this refreshing_submap at coords.
        refreshing_submap.location=coords;
        //Generate the minimap assignemnts.
        generate_minimap_assignments();
        }
    //Now we have a valid refreshing_submap location.
    //If the refreshing_submap is not ready, then work on it.
    if(refreshing_submap.ready==false)
        {
        //Here's the deal:
        //Look through each of the minimaps until we find one that is unloaded.
        //If it is not requested, then request it.
        var index;
        for(index=0;index<refreshing_submap.minimaps.length;index++)
            if(refreshing_submap.minimaps[index].loaded==false && refreshing_submap.minimaps[index].requested==false)
                {
                if(load_minimap(index,false)==false)
                    break;
                }
        }
    }


function display_map(map,x,y)
    {
    var refreshed=false;
    //First see if the current submap is adequate for showing this location.
    //It is fair game if the x and y location are contained inside
    //BUT the location is not contained in a perimeter minimap.
    if(x<visible_submap.location[1]+miniMAP_WIDTH*tile_width
        || x>=visible_submap.location[1]+(subMAP_WIDTH-1)*miniMAP_WIDTH*tile_width
        || y<visible_submap.location[2]+miniMAP_HEIGHT*tile_height
        || y>=visible_submap.location[2]+(subMAP_HEIGHT-1)*miniMAP_HEIGHT*tile_height
        || visible_submap.ready==false)
        {
        //This map is bad.  Force the completion of the refreshing map and
        //Then swap the refreshing and visible submaps.
        //If we try to force completion but the function returns false, then
        //we are waiting for the server to respond. Bail.
        if(force_submap_completion(map,x,y,false)==false)
            return;
        //Swap maps.
        var temp_submap=visible_submap;
        visible_submap=refreshing_submap;
        refreshing_submap=temp_submap;

        //Transfer sprites to the now visible submap.
        var old_sprites=sprite_transfer(visible_submap);

        //Indicate we did a transfer
        refreshed=true;
        }
    //The map is now valid.  Procede to locate the submap by x and y.
    object_x(visible_submap.html,screen_width/2-x+visible_submap.location[1]-tile_width/2);
    object_y(visible_submap.html,screen_height/2-y+visible_submap.location[2]-tile_height/2);

    //If we had sprites, they would be (re)positioned here.
    //Note- attach sprites to submap, not screen.  Keeps
    //them from skipping.
    sprite_render(visible_submap);

    //If this is a refresh then switch the maps then kill the old sprite html objects.
    if(refreshed)
        {
        //Show the visible submap and hide the refreshing.
        object_show(visible_submap.html);
        object_hide(refreshing_submap.html);

        //Kill the old sprite html objects
        sprite_purge(old_sprites);
        }

    //Check to see if we are in a red zone.  If so, then start updating
    //the refreshing_submap so that when we reach the edge of the
    //visible_submap we are also at the center of the refreshing_submap.
    var minimap_x=Math.floor((x-visible_submap.location[1])/(tile_width*miniMAP_WIDTH));
    var minimap_y=Math.floor((y-visible_submap.location[2])/(tile_height*miniMAP_HEIGHT));
    var update=false;
    if(x<visible_submap.location[1]+miniMAP_WIDTH*tile_width*2)
        {
        //We are at a position where the submaps need to be refreshed for when we
        //walk off the map to the left.
        //Find the corresponding minimap and make its location our new x.
        minimap_x--;
        update=true;
        }
    else if(x>=visible_submap.location[1]+(subMAP_WIDTH-2)*miniMAP_WIDTH*tile_width)
        {
        //We are at a position where the submaps need to be refreshed for when we
        //walk off the map.
        minimap_x++;
        update=true;
        }
    if(y<visible_submap.location[2]+miniMAP_HEIGHT*tile_height*2)
        {
        //We are at a position where the submaps need to be refreshed for when we
        //walk off the map.
        minimap_y--;
        update=true;
        }
    else if(y>=visible_submap.location[2]+(subMAP_HEIGHT-2)*miniMAP_HEIGHT*tile_height)
        {
        //We are at a position where the submaps need to be refreshed for when we
        //walk off the map.
        minimap_y++;
        update=true;
        }
    if(update)
        {
        var location=visible_submap.layout[minimap_y][minimap_x].location;
        refresh_submap(location[0],location[1],location[2]);
        }
    }

function onmouseover_tiles(object)
    {
  	sprite_locate('cursor',
        visible_submap.location[1]+object_get_x(object.parentNode)+object_get_x(object),
        visible_submap.location[2]+object_get_y(object.parentNode)+object_get_y(object),
        200);
    }

function onclick_tiles(object)
    {
  	sprite_locate('cursor',
        visible_submap.location[1]+object_get_x(object.parentNode)+object_get_x(object),
        visible_submap.location[2]+object_get_y(object.parentNode)+object_get_y(object),
        200);
  	sprite_grid_move_to('player',
        visible_submap.location[1]+object_get_x(object),
        visible_submap.location[2]+object_get_y(object),
        200,800);
    }

function onclick_cursor(object)
    {
  	sprite_grid_move_to('player',
        visible_submap.location[1]+object_get_x(object),
        visible_submap.location[2]+object_get_y(object),
        200,800);
    }


function init_load_map()
    {
    /*
    Huge todo:
    Next get the map to load the map info.
    After that, pre-load the map data for the current position.
    Then we can swap the loader and the screen.
    Note THIS sequence will happen any time a new map is entered.
    */
    //If the renderer is running, kill it.
    if(renderer_timeout)
        stopTimeout(renderer_timeout);
    renderer_timeout=null;

    //Set the loader message and percentage.
    set_loader_message('Loading map geometry');
    set_loader_percentage(0);

	//Show the loader
	object_show(object_get("loader"));
	object_hide(object_get("map"));

	map_load_done();
    }

function load_map_data(width,height,tiles)
	{
	MAP_WIDTH=width;
	MAP_HEIGHT=height;
	var count;
	tileimgs=Array();
	for(count=0;count<tilefiles.length;count++)
		{
		tileimgs[count]=new Image;
		tileimgs[count].src=images+tiles[count];
		}

    //Set the loader message and percentage.
    set_loader_message('Loading map data');
    set_loader_percentage(0);

	//Now cause the map to be loaded behind the scenes.
	//This call will cause us to proceed to map_load_done
	force_submap_completion(map,x,y,true);
	}


function map_load_done()
    {
	//Showing the completed screen
	object_show(object_get("map"));
	object_hide(object_get("loader"));

	//Start the background renderer
	background_renderer();
    }


function background_renderer()
    {
	//Display map (relocate map and sprites)
	sprite_animate();
	//Get the player sprite id
	var id=sprite_get('player');
	xpos=sprites[id].cx;
	ypos=sprites[id].cy;
	display_map(currmap,xpos,ypos);

	if(object_get('x'))
	   {
    	object_get('x').value=xpos;
    	object_get('y').value=ypos;
    	object_get('ox').value=xpos%tile_width;
    	object_get('oy').value=ypos%tile_height;
        }

    //Start this process again in 25 ms
   renderer_timeout=setTimeout("background_renderer()",refresh_speed);

    }

