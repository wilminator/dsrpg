var MENU_STATE_FIGHT_NONE=0;
var MENU_STATE_FIGHT_PLAYER=1;
var MENU_STATE_FIGHT_ACTION=2;
var MENU_STATE_FIGHT_LIST=3;
var MENU_STATE_FIGHT_TARGET=4;
var MENU_STATE_FIGHT_EQUIP=5;
var MENU_STATE_FIGHT_WAIT=6;

var menu_state=MENU_STATE_FIGHT_NONE;

function set_menu_state(state)
    {
    menu_state=state;
    switch (menu_state)
        {
        case MENU_STATE_FIGHT_NONE: //Freeze input
        case MENU_STATE_FIGHT_WAIT: //Freeze input
            commit_button_hide();
            show_action_menu(false);
            list_menu_hide();
            equip_menu_hide();
            target_cancel_hide();
            break;
        case MENU_STATE_FIGHT_PLAYER: //Select Current hero/Commit/Activate Action Menu
            commit_button_show();
            show_action_menu(false);
            list_menu_hide();
            equip_menu_hide();
            target_cancel_hide();
            new_command=null;
            new_using=null;
            target_range=0;
            find_target=0;
            break;
        case MENU_STATE_FIGHT_ACTION: //Use Action Menu to choose new action/Cancel
            commit_button_hide();
            show_action_menu(true);
            list_menu_hide();
            equip_menu_hide();
            target_cancel_hide();
            break;
        case MENU_STATE_FIGHT_LIST: //Use List Menu to pick Item/Spell/Skill
            commit_button_hide();
            show_action_menu(false);
            list_menu_show();
            equip_menu_hide();
            target_cancel_hide();
            break;
        case MENU_STATE_FIGHT_TARGET: //Select target of action
            commit_button_hide();
            show_action_menu(false);
            list_menu_hide();
            equip_menu_hide();
            target_cancel_show();
            break;
        case MENU_STATE_FIGHT_EQUIP: //Equipment Location Menu
            commit_button_hide();
            show_action_menu(false);
            list_menu_hide();
            equip_menu_show();
            target_cancel_hide();
            break;
        }
    return false;
    }

function target_cancel()
    {
    new_command=null;
    new_using=null;
    var hero=get_hero(player_party,curr_hero[0],curr_hero[1]);
    hero.target=old_target;
    old_target=null;
    target_dead=0;  
    find_target=0;
    display_action(curr_hero[0],curr_hero[1]);
    display_target(curr_hero[0],curr_hero[1]);
    set_menu_state(MENU_STATE_FIGHT_PLAYER);
    play_sound(audio_clips['cancel']);
    }

function target_cancel_hide()
    {
    object_default_visibility(object_get('cancel'));
    }

function target_cancel_show()
    {
    object_show(object_get('cancel'));
    }

function list_menu_show()
    {
    object_show(object_get('menuwindow'));
    }

function list_menu_hide()
    {
    object_default_visibility(object_get('menuwindow'));
    }

function equip_menu_show()
    {
    equip_menu_render();
    object_show(object_get('equipwindow'));
    }

function equip_menu_hide()
    {
    object_default_visibility(object_get('equipwindow'));
    }

function commit_button_show()
    {
    object_show(object_get('commit'));
    }

function commit_button_hide()
    {
    object_hide(object_get('commit'));
    }

