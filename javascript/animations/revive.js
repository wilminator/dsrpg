var revive_object=null;
var revive_events=null;

function revive_impact(pindex,gindex,cindex,tpindex,tgindex,tcindex,range,events)
    {
    revive_events=events;
    start_event();
    default_ranged_impact_processor(tpindex,tgindex,tcindex,range,revive_events);
    revive_events=null;
    finish_event();
    return 500;
    }

