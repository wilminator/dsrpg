function fight_layout()
    {
    //New way to do things.
    var hero,party,group,thisgroup,tag='',small;
    for(party in fight.parties)
        {
        small=(party==player_party && get_party(party).monster);
        for(group=0;group<get_party_length(party);group++)
            {
            //Get the object for this group
            thisgroup=get_group(party,group);
            //Get the group size
            var count=get_group_length(party,group);
            //Prepare x display offsets
            var x_size=(party==player_party?160:get_picture_x(get_hero(party,group,0).html.pic));
            var x_width=x_size;
            if(thisgroup.characters.length>4)
                x_size*=.75;
            x_size=(x_size*count>720)?(720-x_width)/(count-1):x_size;
            x_width=x_size*(count-1)+x_width;
            var x_start=400-x_width/2.0;
            //Position HTML
            for(individual=0;individual<count;individual++)
                {
                //Get the hero
                hero=get_hero(party,group,individual);
                //Set the x display offest for this character
                hero.x_offset=x_start+individual*x_size+(party==player_party?80-get_picture_x(hero.html.pic)/2:0);

                if(small)//This IS the player's party.
                    {
                    if(hero.html.actionbox)
                        object_x(hero.html.actionbox,hero.x_offset);
                    if(x_width<160 && (individual & 1))
                        {
                        hero.y_offset=PIC_HERO_TOP;
                        }
                    else
                        {
                        hero.y_offset=PIC_HERO_BOTTOM;
                        }
                    }
                else if(party==player_party) //This is a normal player party
                    {
                    hero.y_offset=PIC_HERO_BOTTOM;
                    }
                else if(thisgroup.characters.length>4) //Large monster party
                    {
                    if(individual & 1)
                        {
                        hero.y_offset=PIC_ENEMY_TOP;
                        }
                    else
                        {
                        hero.y_offset=PIC_ENEMY_BOTTOM;
                        }
                    }
                else
                    {
                    hero.y_offset=PIC_ENEMY_MID;
                    }
                object_y_base(hero.html.pic,hero.y_offset);
                object_z(hero.html.pic,find_z(hero.html.pic));

                //Update the stats window
                update_bar(hero.html.hp,hero.html.hpval,hero.current.HP,hero.base.HP,small);
                if(hero.html.mp)
                    update_bar(hero.html.mp,hero.html.mpval,hero.current.MP,hero.base.MP,small);
                if(hero.current.HP==0)
                    hide_cause_is_dead(party,group,individual);
                else
                    show_cause_is_alive(party,group,individual);
                }
            }
        monster_fix_hpbars(party);
        }
    hero_update_display();
    }

function monster_fix_hpbars(party)
    {
    var group,character;
    var object;
    var x_size;
    var hero;
    for(group=0;group<get_party_length(party);++group)
        {
        object=get_hero(party,group,0).html.pic;
        x_size=Math.floor(get_picture_x(object)/2)-50;
        for(character=0;character<get_group_length(party,group);++character)
            {
            hero=get_hero(party,group,character).html.dmgbar;
            if (hero)
                object_x(hero,x_size);
            show_hp(party,group,character);
            hide_hp(party,group,character);
            }
        }
    }

function hero_update_display()
    {
    var group,character;
    for(group=0;group<get_party_length(player_party);++group)
        for(character=0;character<get_group_length(player_party,group);++character)
            {
            display_action(group,character);
            display_target(group,character);
            }
    }

function is_fight_html_loaded()
    {
    var count=0,hit=0,party,group,character;
    for(party=0;party<get_fight_length();++party)
        for(group=0;group<get_party_length(party);++group)
            for(character=0;character<get_group_length(party,group);++character)
                {
                count++;
                if(get_hero(party,group,character).html.img.complete)
                    hit++;
                }
    return [hit,count];
    }

function synchronize_fight_data(fight_data)
    {
    var party,group,character,hero,new_hero,thisgroup,new_group;
    //If the fight does not exist, then make a preliminary copy.
    if(!fight)
        fight=fight_data;
    for(party=0;party<fight_data.parties.length;++party)
        {
        //If the party does not exist, then make a preliminary copy.
        if(!get_party(party,true))
            fight.parties[party]=fight_data.parties[party];
        for(group=0;group<fight_data.parties[party].groups.length;++group)
            {
            //If the group does not exist, then make a preliminary copy.
            if(!get_group(party,group,true))
                fight.parties[party].groups[group]=fight_data.parties[party].groups[group];
            for(character=0;character<fight_data.parties[party].groups[group].characters.length;++character)
                {
                //If the character does not exist, then make a preliminary copy.
                if(!get_hero(party,group,character,true))
                    fight.parties[party].groups[group].characters[character]=
                        fight_data.parties[party].groups[group].characters[character];
                hero=get_hero(party,group,character);
                new_hero=fight_data.parties[party].groups[group].characters[character];
                //Copy over the default stuff.
                //If the character is not setup right, then setup.
                if(!hero.html)
                    {
                    //Prepare the character html objects
                    hero.html={};
                    var stats=object_create_stats(party,group,character,fight_screen);
                    hero.html.stats=stats.stats;
                    hero.html.pic=stats.heropic;
                    hero.html.img=stats.heroimg;
                    hero.html.dmgbar=stats.dmgbar;
                    hero.html.hp=stats.hp;
                    hero.html.hpval=stats.hpval;
                    hero.html.mp=stats.mp;
                    hero.html.mpval=stats.mpval;
                    hero.html.action=stats.action;
                    hero.html.target=stats.target;
                    hero.html.actpic=stats.actpic;
                    hero.html.actionbox=stats.actionbox;
                    }

                //Copy the html.
                new_hero.html=hero.html;
                //Set the HP and MP display variables
                new_hero.HPD=new_hero.current.HP;
                new_hero.HPDT=new_hero.current.HP;
                new_hero.MPD=new_hero.current.MP;
                new_hero.MPDT=new_hero.current.MP;
                //Set the show HP gauge
                new_hero.showhp=2;
                new_hero.hpdisplay=null;
                }
            //Get the object for this group
            thisgroup=get_group(party,group);
            new_group=fight_data.parties[party].groups[group];
            //Prepare the group html objects
            if(!thisgroup.html)
                {
                tag=party.toString()+'-'+group.toString()
                thisgroup.html={};
                var group_objects=object_create_group(party,group,fight_screen);
                thisgroup.html.box=group_objects.box;
                thisgroup.html.group=group_objects.group;
                thisgroup.html.group_text=group_objects.group_text;
                }
            //Copy HTML
            new_group.html=thisgroup.html;
            }
        }
    fight=fight_data;
    }
