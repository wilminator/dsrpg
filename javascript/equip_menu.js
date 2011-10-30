var new_equip_location;
var equipped_item;

function equip_menu_selection(choice)
    {
    var hero=get_hero(player_party,curr_hero[0],curr_hero[1]);

    if(new_using!=-1)
        {
        //Determine the side the item is being equipped on.
        if(choice.indexOf('hand')!=1
            || choice.indexOf('arm')!=1
            || choice.indexOf('ammo')!=1)
            new_equip_location=(choice.slice(0,1)=='r'?0:1);
        else
            new_equip_location=0;
        }
    else
        {
        //Indicate what we are unequipping.
        new_equip_location=hero.equipment[choice];
        }

    //choice is the string of the location to be equipped.
    //If the new weapon requires ammo, change new_command to 9
    //and open the select ammo list menu.
    if(equipped_item && equipped_item.ammo_type!='' &&
        equipped_item.equip_type.join(',').indexOf('ammo')==-1)
        {
        //set menu list for ammo type.
        list_menu_add_equipable_ammo(hero,equipped_item.ammo_type);
        new_command=COMMAND_EQUIP_AMMO;
        set_menu_state(MENU_STATE_FIGHT_LIST);
        }
    else
        {
        hero.command=new_command;
        hero.using=new_using;
        hero.target=[new_equip_location,0,0];
        display_action(curr_hero[0],curr_hero[1]);
        display_target(curr_hero[0],curr_hero[1]);
        set_menu_state(MENU_STATE_FIGHT_PLAYER);
        }
    }

function equip_menu_render()
    {
    var object,textobj,text,eitem,item,index,index2,may_equip;
    var hero=get_hero(player_party,curr_hero[0],curr_hero[1]);

    if(new_using==-1)
        equipped_item=null;
    else
        equipped_item=items[hero.inventory[new_using].item];

    for (index=0;index<equip_points.length;index++)
        {
        object=object_get('equiprow'+equip_points[index]);
        //Modify the border color if the item in
        //question can be equipped here.
        may_equip=false;
        if(equipped_item)
            {
            for(index2=0;index2<equipped_item.equip_type.length&&may_equip==false;index2++)
                if (equip_points[index].indexOf(equipped_item.equip_type[index2])!=-1)
                    may_equip=true;
            }
        if(may_equip)
            {
            object_border_color(object,'rgb(0,192,0)');
            object.selectable=true;
            }
        else
            {
            object_border_color(object,'');
            object.selectable=false;
            }

        //See if there is something equipped here.
        if(hero.equipment[equip_points[index]]!=null)
            item=hero.inventory[hero.equipment[equip_points[index]]];
        else
            item=null;
        //eval('eitem=hero.equipment.'+equip_points[index]+';');
        //if(eitem!=null)
        //    item=hero.inventory[eitem];
        //else
        //    item=null;

        object=object_get('equippic'+equip_points[index]);
        if(item)
            object_set_image(object,items[item.item].menu_pic,item_images);
        else
            object_set_image(object,'bignone.png',item_images);

        object=object_get('equipitem'+equip_points[index]);
        textobj=object.firstChild;
        if(item)
            {
            text=items[item.item].name;
            if (item.qty>1)
                text+=' ('+item.qty.toString()+')';
            if(equipped_item==null)
                {
                object=object_get('equiprow'+equip_points[index]);
                object_border_color(object,'rgb(0,192,0)');
                object.selectable=true;
                }
            }
        else
            text='';
        textobj.replaceData(0,textobj.data.length,text);
        }

    object=object_get('menudesc');
    text=object.firstChild;
    text.replaceData(0,text.data.length,'');

    object=object_get('menulast');
    if(list_menu_position==0)
        object.visibility='hidden';
    else
        object.visibility='';

    object=object_get('menunext');
    if(list_menu_position+8>=list_menu_items.length)
        object.visibility='hidden';
    else
        object.visibility='';
    }

function equip_menu_highlight(slot)
    {
    var index,index2,slots,eitem,side;
    var must_remove=[],affected_weapons=[];
    var object=object_get('equiprow'+slot);
    if(object.selectable)
        {
        var hero=get_hero(player_party,curr_hero[0],curr_hero[1]);

        //Set sides, if needed.
        if(slot.indexOf('hand')!=1
            || slot.indexOf('arm')!=1
            || slot.indexOf('ammo')!=1)
            side=slot.slice(0,1);
        else
            side='r';

        if(new_using!=-1)
            slots=equipped_item.equip_type.join(',').split(',');
        else
            slots=[slot];

        for(index=0;index<slots.length;index++)
            {
            if(slots[index]=='hand'
                || slots[index]=='arm'
                || slots[index]=='ammo')
                slots[index]=side+slots[index];
            if(hero.equipment[slots[index]]!=null)
                affected_weapons.push(hero.equipment[slots[index]]);
            }
        if(new_using!=-1)
            affected_weapons.push(new_using);

        for(index=0;index<equip_points.length;index++)
            {
            eval('eitem=hero.equipment.'+equip_points[index]+';');
            for(index2=0;index2<affected_weapons.length;index2++)
                if(hero.equipment[equip_points[index]]==affected_weapons[index2])
                    must_remove.push(equip_points[index]);
            }

        //now bad-highlight all slots that will have to be unequipped
        //for this item to be worn.
        for (index=0;index<must_remove.length;index++)
            {
            object=object_get('equiprow'+must_remove[index]);
            object_background_color(object,'rgb(128,0,0)');
            }

        //now pro-highlight all slots that will be filled using this
        //weapon.
        for (index=0;index<slots.length;index++)
            {
            object=object_get('equiprow'+slots[index]);
            object_border_color(object,'rgb(192,192,0)');
            object_background_color(object,'rgb(0,128,128)');
            }
        }
    }

function equip_menu_unhighlight(index)
    {
    var index;
    var object=object_get('equiprow'+index);
    if(object.selectable)
        {
        //Now unhighlight all slots.
        for (index=0;index<equip_points.length;index++)
            {
            object=object_get('equiprow'+equip_points[index]);
            object_background_color(object,'');
            if (object.selectable)
                object_border_color(object,'rgb(0,192,0)');
            }
        }
    }

function equip_menu_cancel()
    {
    target_cancel();
    play_sound(audio_clips['cancel']);
    set_menu_state(MENU_STATE_FIGHT_PLAYER);
    }

function equip_menu_select(choice)
    {
    if (menu_state!=MENU_STATE_FIGHT_EQUIP)
        set_menu_state(MENU_STATE_FIGHT_PLAYER);

    if(object_get('equiprow'+choice).selectable==false)
        return false;
    play_sound(audio_clips['select']);
    equip_menu_unhighlight(choice);
    equip_menu_selection(choice);
    }

function highlight_equip_menu_icon(object)
    {
    object_border(object,'solid white');
    object_z(object,30);
    }

function unhighlight_equip_menu_icon(object)
    {
    object_border(object,'solid black');
    object_z(object,25);
    }

