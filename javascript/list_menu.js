var list_menu_items=[];
var list_menu_position=0;

function list_menu_selection(choice)
    {
    var hero=get_hero(player_party,curr_hero[0],curr_hero[1]);
    //choice is the index number from the list menu.
    //Based on new_command, we now determine how to handle targeting.
    switch(new_command)
        {
        case COMMAND_USE_ITEM: //Items
            new_using=list_menu_items[choice].value
            var item=items[hero.inventory[new_using].item];
            list_menu_target_effect(item.targets,item.effect);
            break;
        case COMMAND_EQUIP_ITEM: //Equip new weapon
            new_using=list_menu_items[choice].value
            //Now pick a location for the equipment
            set_menu_state(MENU_STATE_FIGHT_EQUIP);
            break;
        case COMMAND_EQUIP_AMMO: //Equipping weapon and ammo!!
            hero.command=new_command;
            hero.using=new_using;
            new_command=null;
            new_using=null;
            hero.target=[new_equip_location,list_menu_items[choice].value,0];
            display_action(curr_hero[0],curr_hero[1]);
            display_target(curr_hero[0],curr_hero[1]);
            relay_player_commands(player_party,curr_hero[0],curr_hero[1]);
            set_menu_state(MENU_STATE_FIGHT_PLAYER);
            break;
        case COMMAND_USE_SKILL: //Spells and skills are the same.
        case COMMAND_CAST_SPELL:
            new_using=list_menu_items[choice].value
            var ability=abilities[hero.abilities[new_using]];
            list_menu_target_effect(ability.targets,ability.effect);
            break;
        }
    }

function list_menu_target_effect(targets,effect)
    {
    var hero=get_hero(player_party,curr_hero[0],curr_hero[1]);
    if(targets==-2)
        {
        hero.command=new_command;
        hero.using=new_using;
        hero.target=[(is_effect_bad(effect)?1-player_party:player_party),0,0];
        new_command=null;
        new_using=null;
        set_menu_state(MENU_STATE_FIGHT_PLAYER);
        relay_player_commands(player_party,curr_hero[0],curr_hero[1]);
        }
    else
        {
        target_range=targets;
        target_dead=only_living_targets(effect)?0:1;
        find_target=is_effect_bad(effect)?2:1;
        set_menu_state(MENU_STATE_FIGHT_TARGET);
        }
    display_action(curr_hero[0],curr_hero[1]);
    display_target(curr_hero[0],curr_hero[1]);
    return true;
    }

function list_menu_render()
    {
    var object;
    var text;

    for (index=0;index<8;index++)
        {
        object=object_get('menurow'+index.toString());
        if(index+list_menu_position>=list_menu_items.length)
            {
            object_hide(object);
            }
        else
            {
            object_default_visibility(object);

            menuitem=list_menu_items[list_menu_position+index];

            object=object_get('menupic'+index.toString());
            object_set_image(object,menuitem.pic);

            object=object_get('menuitem'+index.toString());
            text=object.firstChild;
            text.replaceData(0,text.data.length,menuitem.item);

            object=object_get('menuqty'+index.toString());
            text=object.firstChild;
            text.replaceData(0,text.data.length,menuitem.qty);
            if(menuitem.qtycolor!=null)
                object_color(object,menuitem.qtycolor);
            else
                object_color(object,'');
            }
        }
    object=object_get('menudesc');
    text=object.firstChild;
    text.replaceData(0,text.data.length,'');

    object=object_get('menulast');
    if(list_menu_position==0)
        object_hide(object);
    else
        object_default_visibility(object);

    object=object_get('menunext');
    if(list_menu_position+8>=list_menu_items.length)
        object_hide(object);
    else
        object_default_visibility(object);
    }

function list_menu_highlight(index)
    {
    var object=object_get('menurow'+index.toString());
    if(list_menu_items[index+list_menu_position].selectable==false)
        object_background_color(object,'rgb(96,0,0)');
    else
        object_background_color(object,'rgb(0,96,0)');
    object=object_get('menudesc');
    text=object.firstChild;
    text.replaceData(0,text.data.length,list_menu_items[index+list_menu_position].desc);
    }

function list_menu_unhighlight(index)
    {
    var object=object_get('menurow'+index.toString());
    object_background_color(object,'');
    object=object_get('menudesc');
    text=object.firstChild;
    text.replaceData(0,text.data.length,'');
    }

function list_menu_cancel()
    {
    play_sound(audio_clips['cancel']);
    new_command=null;
    nuw_using=null;
    set_menu_state(MENU_STATE_FIGHT_PLAYER);
    }

function list_menu_next()
    {
    if(list_menu_position+8<list_menu_items.length)
        list_menu_position+=8;
    list_menu_render();
    play_sound(audio_clips['hover']);
    }


function list_menu_last()
    {
    if(list_menu_position>0)
        list_menu_position-=8;
    list_menu_render();
    play_sound(audio_clips['hover']);
    }

function list_menu_select(choice)
    {
    if (menu_state!=MENU_STATE_FIGHT_LIST)
        set_menu_state(MENU_STATE_FIGHT_PLAYER);

    if(list_menu_items[choice+list_menu_position].selectable==false)
        return false;
    play_sound(audio_clips['select']);
    list_menu_unhighlight(choice);
    list_menu_selection(choice+list_menu_position);
    return true;
    }

function list_menu_reset()
    {
    list_menu_items=[];
    list_menu_position=0;
    }

function list_menu_add_item(value,pic,item,qty,qtycolor,desc,selectable)
    {
    var menuitem={value:value,pic:pic,item:item,qty:qty,qtycolor:qtycolor,desc:desc,selectable:selectable};
    list_menu_items.push(menuitem);
    }

function list_menu_add_spells(person)
    {
    list_menu_reset();
    var index;
    var ability;

    for(index in person.abilities)
        {
        ability=abilities[person.abilities[index]];
        if(ability.type==0)
            {
            if(person.current.MP<ability.mp_used)
                list_menu_add_item(index,ability_images+ability.menu_pic,ability.name,ability.mp_used.toString()+' MP','red',ability.description,false);
            else
                list_menu_add_item(index,ability_images+ability.menu_pic,ability.name,ability.mp_used.toString()+' MP',null,ability.description,true);
            }
        }
    list_menu_render();
    }

function list_menu_add_skills(person)
    {
    list_menu_reset();
    var index;
    var ability;

    for(index in person.abilities)
        {
        ability=abilities[person.abilities[index]];
        if(ability.type==1)
            if(person.current.MP<ability.mp_used)
                list_menu_add_item(index,ability_images+ability.menu_pic,ability.name,ability.mp_used.toString()+' MP','red',ability.description,false);
            else
                list_menu_add_item(index,ability_images+ability.menu_pic,ability.name,ability.mp_used.toString()+' MP',null,ability.description,true);
        }
    list_menu_render();
    }

function list_menu_add_useable_items(person)
    {
    list_menu_reset();
    var index;
    var item,qty;

    for(index in person.inventory)
        {
        item=items[person.inventory[index].item];
        if (item.effect!=0)
            {
            qty=person.inventory[index].qty;
            if(qty==0)
                list_menu_add_item(index,item_images+item.menu_pic,item.name,qty.toString(),'red',item.description,false);
            else
                list_menu_add_item(index,item_images+item.menu_pic,item.name,qty.toString(),null,item.description,true);
            }
        }
    list_menu_render();
    }

function list_menu_add_equipable_items(person)
    {
    list_menu_reset();
    var index;
    var item,qty;

    for(index in person.inventory)
        {
        item=items[person.inventory[index].item];
        if (item.equip_type!=null)
            {
            qty=person.inventory[index].qty;
            if(qty==0)
                list_menu_add_item(index,item_images+item.menu_pic,item.name,qty.toString(),'red',item.description,false);
            else
                list_menu_add_item(index,item_images+item.menu_pic,item.name,qty.toString(),null,item.description,true);
            }
        }
    //Now add nothing as a possible equipment choice.
    item=items[0];
    list_menu_add_item(-1,item_images+item.menu_pic,item.name,'',null,item.description,true);
    list_menu_render();
    }

function list_menu_add_equipable_ammo(person,ammo_type)
    {
    list_menu_reset();
    var index;
    var item,qty;

    for(index in person.inventory)
        {
        item=items[person.inventory[index].item];
        if (item.equip_type && item.equip_type.join(',')=='ammo' && item.ammo_type==ammo_type)
            {
            qty=person.inventory[index].qty;
            if(qty==0)
                list_menu_add_item(index,item_images+item.menu_pic,item.name,qty.toString(),'red',item.description,false);
            else
                list_menu_add_item(index,item_images+item.menu_pic,item.name,qty.toString(),null,item.description,true);
            }
        }
    //Now add nothing as a possible equipment choice.
    item=items[0];
    list_menu_add_item(-1,item_images+item.menu_pic,item.name,'',null,item.description,true);
    list_menu_render();
    }

function highlight_list_menu_icon(object)
    {
    object_border(object,'solid white');
    object_z(object,30);
    }

function unhighlight_list_menu_icon(object)
    {
    object_border(object,'solid black');
    object_z(object,25);
    }

