var timerbox_timeout=null;
var timerbox=null;
var timerbox_text=null;
var time_expires=null;
var time_delayed=false;

function object_create_timerbox(parent)
    {
    timerbox=object_create('div',parent,{className:'timerbox',align:'center'});
    timerbox_text=document.createTextNode('');
    timerbox.appendChild(timerbox_text);
    object_show(timerbox);
    }

function update_timerbox(time)
    {
    if(timerbox)
        {
        if(timerbox_timeout)
            clearTimeout(timerbox_timeout);
        timerbox_text.data='';
        if(time)
            {
            var date= new Date();
            time_expires=new Date(time*1000+date.getTime());
            refresh_time_left();
            }
        }
    }

function refresh_time_left()
    {
    var diff=timeout_time_left();
    //alert(diff);
    if(diff>0 || menu_state!=MENU_STATE_FIGHT_PLAYER)
        {
        timerbox_timeout=setTimeout('refresh_time_left()',250);
        //If we are still in animation, then request an extension!
        if(diff<60 && menu_state==MENU_STATE_FIGHT_NONE)
            request_timeout_extension();
        //Note- if the player remains in menu for over a minute then the
        //request timeout extension feature is revoked.
        else if(diff<-1*60)
            time_delayed=true;
        }
    else
        {
        timerbox_timeout=null;

        //Key decisions here-
        //If gone for more than 5 minutes after timeout, then request an extension.
        if(diff<-5*60 && time_delayed==false)
            request_timeout_extension();
        else
            commit_commands();
        }

    if (diff<0) diff=0;
    var min=Math.floor(diff/60);
    var sec=diff%60;
    sec=(sec<10)?'0'+sec.toString():sec.toString();
    object_color(timerbox,(diff<=10&&(diff%2)==0)?'red':'white');
    timerbox_text.data=min.toString()+':'+sec;
    }

function timeout_time_left()
    {
    var date=new Date();
    return Math.floor((time_expires.getTime()-date.getTime()+999)/1000);
    }
