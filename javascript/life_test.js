function party_is_dead(party)
    {
    var group,sum=true;
    for(group=0;group<get_party_length(party);++group)
        sum=(sum&&group_is_dead(party,group));
    return sum;
    }

function group_is_dead(party,group)
    {
    var character,sum=true;
    for(character=0;character<get_group_length(party,group);++character)
        sum=(sum&&(get_hero(party,group,character).current.HP<=0));
    return sum;
    }

function group_living_count(party,group)
    {
    var character,sum=0;
    for(character=0;character<get_group_length(party,group);++character)
        if(get_hero(party,group,character).current.HP>0)
            sum++;
    return sum;
    }

function test_own_party_dead(party)
    {
    return party_is_dead(party);
    }

function test_other_parties_dead(party)
    {
    var index,sum=true;
    for (index=0;index<fight.parties.length;++index)
        if (index!=party) sum=(sum&&party_is_dead(index));
    return sum;
    }

