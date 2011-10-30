var curr_hero=null;

function select_hero(party,group,individual)
    {
    //If we are looking for a hero to direct...
    if(party==player_party)
        {
        if(menu_state==MENU_STATE_FIGHT_PLAYER)
            {
            var hero=get_hero(player_party,group,individual);
            if(curr_hero)
                {
                var old_hero=curr_hero;
                curr_hero=[group,individual];
                unhighlight_hero(player_party,old_hero[0],old_hero[1]);
                if(curr_hero.join()==old_hero.join())
                    {
                    play_sound(audio_clips['unselect']);
                    set_menu_state(MENU_STATE_FIGHT_ACTION);
                    }
                else
                    {
                    play_sound(audio_clips['select']);
                    }
                }
            curr_hero=[group,individual];
            object_border(hero.html.stats,'solid 2px yellow');
            object_border(hero.html.pic,'solid 2px yellow');
            set_stats_zindex(player_party,group,individual,true);
            if(group!=player_slide.oldgroup)
                hero_slide_center(player_party,group);
            }
        //If want to change heroes
        else if (menu_state==MENU_STATE_FIGHT_ACTION)
            {
            play_sound(audio_clips['cancel']);
            set_menu_state(MENU_STATE_FIGHT_PLAYER);
            }
        }
    //If we are looking for a hero to target...
    if (menu_state==MENU_STATE_FIGHT_TARGET)
        {
        var hero=get_hero(player_party,curr_hero[0],curr_hero[1]);
        hero.command=new_command;
        hero.using=new_using;
        hero.target=[party,group,individual];
        unhighlight_hero_range(party,group,individual);
        target_dead=0;
        if(party==player_party)
            hero_slide_center(party,curr_hero[0]);
        play_sound(audio_clips['select']);
        set_menu_state(MENU_STATE_FIGHT_PLAYER);
        display_action(curr_hero[0],curr_hero[1]);
        display_target(curr_hero[0],curr_hero[1]);
        relay_player_commands(player_party,curr_hero[0],curr_hero[1]);
        highlight_hero_range(party,group,individual);
        }
    //Otherwise ignore.
    }

