var new_command=null;
var new_using=null;
var old_target=null;
var target_range=0;
var find_target=0;
var target_dead=0;

function action_menu_selection(choice)
    {
    if (menu_state!=MENU_STATE_FIGHT_ACTION)
        set_menu_state(MENU_STATE_FIGHT_PLAYER);

    var hero=get_hero(player_party,curr_hero[0],curr_hero[1]);
    old_target=hero.target;
    new_command=choice;

    var targetrange;

    //Based on choice, we now determine if we need the List Menu.
    //We need the menu for spells, skills, items, and weapon changes.
    switch(choice)
        {
        case COMMAND_USE_ITEM:
            //Build list of items.
            list_menu_add_useable_items(hero);
            set_menu_state(MENU_STATE_FIGHT_LIST);
            break;
        case COMMAND_EQUIP_ITEM:
            //Build list of equipable weapons and ammo.
            list_menu_add_equipable_items(hero);
            set_menu_state(MENU_STATE_FIGHT_LIST);
            break;
        case COMMAND_USE_SKILL:
            //Build list of useable skills.
            list_menu_add_skills(hero);
            set_menu_state(MENU_STATE_FIGHT_LIST);
            break;
        case COMMAND_CAST_SPELL:
            //Build list of useable spells.
            list_menu_add_spells(hero);
            set_menu_state(MENU_STATE_FIGHT_LIST);
            break;
        case COMMAND_FIGHT_LHAND:
            //Based on left weapon, target enemy.
            select_action_attack(hero,hero.equipment.lhand,hero.equipment.lammo);
            break;
        case COMMAND_FIGHT_RHAND:
            //Based on right weapon, target enemy.
            select_action_attack(hero,hero.equipment.rhand,hero.equipment.rammo);
            break;
        case COMMAND_FIGHT_DEFEND:
        case COMMAND_FIGHT_FLEE:
            //Defend or run.
            hero.command=choice;
            hero.using=0;
            new_command=null;
            display_action(curr_hero[0],curr_hero[1]);
            display_target(curr_hero[0],curr_hero[1]);
            set_menu_state(MENU_STATE_FIGHT_PLAYER);
            break;
        }
    }

function select_action_attack(hero,hand,ammo)
    {
    var range=find_target_range(hero,hand,ammo);
    if(range==-2)
        {
        hero.command=choice;
        hero.target=[1,0,0];
        hero.using=0;
        set_menu_state(MENU_STATE_FIGHT_PLAYER);
        relay_player_commands(player_party,curr_hero[0],curr_hero[1]);
        }
    else
        {
        target_range=range;
        find_target=2;
        new_using=0;
        set_menu_state(MENU_STATE_FIGHT_TARGET);
        }
    display_action(curr_hero[0],curr_hero[1]);
    display_target(curr_hero[0],curr_hero[1]);
    }

function find_target_range(person,weapon,ammo)
    {
    if(weapon==null)
        return items[0].targets;
    if(ammo==null)
        return items[person.inventory[weapon].item].targets;
    return items[person.inventory[ammo].item].targets;
    }

function fetch_icon(command,person)
    {
    switch(command)
        {
        case COMMAND_FIGHT_LHAND: //Left Weapon
            return fetch_weapon_icon(person,person.equipment.lhand,person.equipment.lammo);
        case COMMAND_FIGHT_RHAND: //Right Weapon
            return fetch_weapon_icon(person,person.equipment.rhand,person.equipment.rammo);
        case COMMAND_USE_ITEM: //Use item
            return 'item.png';
        case COMMAND_EQUIP_ITEM: //Equip new weapon
            return 'no_fight.png';
        case COMMAND_USE_SKILL: //Use skill
            return 'skill.png';
        case COMMAND_CAST_SPELL: //Cast spell
            return 'spell.png';
        case COMMAND_FIGHT_DEFEND: //Defend
            return 'defend.png';
        case COMMAND_FIGHT_FLEE: //Run
            return 'none.png';
        case COMMAND_EQUIP_AMMO: //Equip new weapon and ammo
            return 'no_fire.png';
        }
    return 'none.png';
    }

function fetch_weapon_icon(person,hand,ammo)
    {
    if (hand==null)
        return 'fist.png';
    //If the item in hand has no attacks, then show a shield.
    else if(items[person.inventory[hand].item].attack_count==0)
        return 'shield.png';
    //If the ammo is the weapon, show a bomb.
    else if(ammo==hand && hand!=null)
        return 'bomb.png';
    //If there is a weapon and it requires no ammo, show a sword.
    else if(items[person.inventory[hand].item].ammo_type=='')
        return 'fight.png';
    //If there is no ammo, show a 'no-fire' gun.
    else if(ammo==null)
        return 'no_fire.png';
    //Otherwise this is a loaded gun.
    return 'fire.png';
    }

function fix_weapon_icon(object,person,hand,ammo)
    {
    object_set_image(object,fetch_weapon_icon(person,hand,ammo),menu_images);
    }

function show_action_menu(visible)
    {
    var object,index;
    if(visible)
        {
        //TODO:get icons in the action menu to reflect the options for that person
        var person=get_hero(player_party,curr_hero[0],curr_hero[1]);

        //Fix left hand
        object=object_get('action0');
        if(person.equipment.rhand!=null && person.equipment.rhand==person.equipment.lhand)
            object_hide(object);
        else
            {
            object_default_visibility(object);
            fix_weapon_icon(object,person,person.equipment.lhand,person.equipment.lammo);
            }

        //Fix right hand
        object=object_get('action1');
        fix_weapon_icon(object,person,person.equipment.rhand,person.equipment.rammo);

        //Fix spells
        object=object_get('action4');
        hit=false;
        for(index in person.abilities)
            if(abilities[person.abilities[index]].type==1)
                hit=true;
        if(hit)
            object_default_visibility(object);
        else
            object_hide(object);

        //Fix skills
        object=object_get('action5');
        hit=false;
        for(index in person.abilities)
            if(abilities[person.abilities[index]].type==0)
                hit=true;
        if(hit)
            object_default_visibility(object);
        else
            object_hide(object);

        if(person.html.actionbox)
            {
            object_x(object_get('actionmenu'),object_get_x(person.html.actionbox));
            object_y(object_get('actionmenu'),object_get_y(person.html.actionbox)-100);
            }
        else
            object_x(object_get('actionmenu'),object_get_x(person.html.stats));
        }
    else
        {
        object_x(object_get('actionmenu'),'-300px');
        }
    }

function display_target(group,individual)
    {
    var person=get_hero(player_party,group,individual);
    var targetrange;
    var out;
    var effect;
    var can_be_dead=0;
    var command=(new_command!=null && [group,individual].join()==curr_hero.join()?new_command:person.command);
    var using=(new_using!=null && [group,individual].join()==curr_hero.join()?new_using:person.using);
    switch(command)
        {
        case COMMAND_FIGHT_LHAND: //Left weapon
            targetrange=find_target_range(person,person.equipment.lhand,person.equipment.lammo);
            break;
        case COMMAND_FIGHT_RHAND: //Right weapon
            targetrange=find_target_range(person,person.equipment.rhand,person.equipment.rammo);
            break;
        case COMMAND_USE_ITEM: //Use item
            if (using<person.inventory.length)
                {
                targetrange=items[person.inventory[using].item].targets;
                effect=items[person.inventory[using].item].effect;
                can_be_dead=only_living_targets(effect)?0:1;
                }
            else
                targetrange=0;
            break;
        case COMMAND_EQUIP_ITEM: //Equip weapon
            out='';
            targetrange=-255;
            break;
        case COMMAND_EQUIP_AMMO: //Equip weapon and ammo
            if(person.target[1]!=-1)
                out=items[person.inventory[person.target[1]].item].name;
            else
                out='No ammo selected';
            targetrange=-255;
            break;
        case COMMAND_USE_SKILL: //Use skill
        case COMMAND_CAST_SPELL: //Cast spell
            targetrange=abilities[person.abilities[using]].targets;
            effect=abilities[person.abilities[using]].effect;
            can_be_dead=only_living_targets(effect)?0:1;
            break;
        case COMMAND_FIGHT_DEFEND: //Defend
        case COMMAND_FIGHT_FLEE: //Run!!
            out='';
            targetrange=-255;
            break;
        default:
            targetrange=0;
        }

        //Calculate the display.
        if(targetrange==-2)
            {
            if(person.target[0]==player_party)
                out='Entire Party';
            else
                out=get_party(person.target[0]).name+" (Party)";
            }
        else if (targetrange==-1)
            {
            if(person.target[0]==player_party)
                out=get_group(player_party,person.target[1]).name;
            else if(person.target[0]>=0)
                out=get_group(person.target[0],person.target[1]).name+" (Group)";
            else if(find_target==2)
                out='Target enemy group...';
            else
                out='Target hero group...';
            }
        else if(find_target==2)
            {
            out='Target enemy';
            if(targetrange>0)
                out+=' (+'+targetrange+')';
            out+='...';
            }
        else if(find_target==1)
            {
            out='Target hero';
            if(targetrange>0)
                out+=' (+'+targetrange+')';
            out+='...';
            }
        else if(targetrange>=0)
            {
            var targetcount=targetrange*2+1;
            var targetparty=get_party(person.target[0]);
            var targetgroup=get_group(person.target[0],person.target[1]);
            if(!targetgroup) alert(person.target);
            var targetfirst=person.target[2]-targetrange;
            out=[];
            for(index=0;index<targetcount;index++)
                {
                if(index+targetfirst>=0
                    && index+targetfirst<targetgroup.characters.length)
                    {
                    target=targetgroup.characters[index+targetfirst];
                    if(target.current.HP>0 || can_be_dead)
                        {
                        if(person.target[0]==player_party || out=='')
                            out.push(target.name);
                        else
                            out.push(target.name.slice(-1));
                        }
                    }
                }
            out=out.join(", ");
            }

    //Set target caption to out.
    person.html.target.data=out;
    }

function get_attack_action(person,weapon,ammo)
    {
    var out='',item;
    if(weapon==null)
        item=0;
    else
        item=person.inventory[weapon].item;
    out=items[item].name;
    if(ammo!=null && person.inventory[ammo])
        {
        if(person.inventory[ammo].qty>0)
            out+=" ("+person.inventory[ammo].qty+")";
        else
            out+=" (Out of Ammo)";
        }
    return out;
    }

function display_action(group,individual)
    {
    var person=get_hero(player_party,group,individual);
    var weapon;
    var ammo;
    var item;
    var out;
    var command=(new_command!=null && [group,individual].join()==curr_hero.join()?new_command:person.command);
    var using=(new_using!=null && [group,individual].join()==curr_hero.join()?new_using:person.using);
    switch(command)
        {
        case COMMAND_FIGHT_LHAND:
            out=get_attack_action(person,person.equipment.lhand,person.equipment.lammo);
            break;
        case COMMAND_FIGHT_RHAND:
            out=get_attack_action(person,person.equipment.rhand,person.equipment.rammo);
            break;
        case COMMAND_USE_ITEM:
            if (person.inventory[using])
                {
                out=items[person.inventory[using].item].name;
                if(person.inventory[using].qty>1)
                    out+=" ("+person.inventory[using].qty+")";
                }
            else
                out="Nothing";
            break;
        case COMMAND_EQUIP_ITEM: //Equipping
        case COMMAND_EQUIP_AMMO: //Equipping with ammo
            if (using!=-1)
                out=items[person.inventory[using].item].name;
            else
                out="Unequiping "+items[person.inventory[person.target[0]].item].name;
            break;
        case COMMAND_USE_SKILL:
        case COMMAND_CAST_SPELL:
            var out=abilities[person.abilities[using]].name;
            break;
        case COMMAND_FIGHT_DEFEND:
            out='Defending';
            break;
        case COMMAND_FIGHT_FLEE:
            out='Running!!!';
            break;
        default:
            out="";
        }

    //Set action caption to out.
    person.html.action.data=out;

    //Get action icon
    object_set_image(person.html.actpic,fetch_icon(command,person),menu_images);
    }

function highlight_action_menu_icon(object)
    {
    if (menu_state!=MENU_STATE_FIGHT_PLAYER&&menu_state!=MENU_STATE_FIGHT_ACTION&&menu_state!=MENU_STATE_FIGHT_TARGET)
        return false;
    object_border(object,'solid white');
    object_z(object,30);
    }

function unhighlight_action_menu_icon(object)
    {
    if (menu_state!=MENU_STATE_FIGHT_PLAYER&&menu_state!=MENU_STATE_FIGHT_ACTION&&menu_state!=MENU_STATE_FIGHT_TARGET)
        return false;
    object_border(object,'solid black');
    object_z(object,25);
    }

