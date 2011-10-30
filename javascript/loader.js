function set_loader_message(msg)
    {
    object_get('loader_message').firstChild.data=msg;
    }

function set_loader_percentage(perc)
    {
    //var width=perc*792;
    var newperc=Math.floor(perc);
    object_width(object_get('loader_percent'),newperc+'%');
    //object_width(object_get('loader_percent'),width);
    object_get('loader_number').firstChild.data=newperc+'%';
    }

