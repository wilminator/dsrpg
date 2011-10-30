<?php
//Tell where the includes are.
define ('INCLUDE_DIR','include/');

//Prepare the error trapping functions
require_once INCLUDE_DIR.'errorlog.php';

require_once INCLUDE_DIR.'constants.php';
require_once INCLUDE_DIR.'jobs.php';
require_once INCLUDE_DIR.'items.php';

function test_stat_avarage($job,$level,$passes,$depth,$climb)
    {
    for($pass=0;$pass<$passes;$pass++)
    	{
    	$stats[$pass]=array();
    	$stats[$pass]['stat']=array();
  		$stats['info']['job']=$job;
  		$stats['info']['name']=$GLOBALS['jobs'][$job]->name;
  		$stats['info']['level']=$level;
  		$stats['info']['passes']=$passes;
  		$stats['info']['depth']=$depth;
  		$stats['info']['climb']=$climb;

  		$character=hero($job->name,$index,$level,$null);
  		foreach($character->base as $stat=>$val)
			$stats[$pass]['stat'][$stat]=$val;

		$stats[$pass]['pxp']=$character->pxp;

    	for($count=1;$count<$depth;$count++)
    		{
    		$character=hero($GLOBALS['jobs'][$job]->name,$job,$level,$null);
    		foreach($character->base as $stat=>$val)
				$stats[$pass]['stat'][$stat]+=$val;
    		$stats[$pass]['stat']['pxp']+=$character->pxp;
    		}

    	foreach($stats[$pass]['stat'] as $stat=>$val)
    		$stats[$pass]['stat'][$stat]/=$depth;
    	if($climb)
    		{
    		foreach($job->upgrade as $stat=>$val)
    			$job->upgrade[$stat]+=.01;
    		}
        }
    return $stats;
    }

function display_stat_average($stats)
    {
	$statavg=array();
	$passes=$stats['info']['passes'];
	echo "Job {$stats['info']['job']}:{$stats['info']['name']} Level:{$stats['info']['level']}\n<table>";
	echo "<tr><th>Pass</th>";
	foreach($stats[0]['stat'] as $stat=>$val)
		echo "<th>$stat</th>";
    echo "<th>PXP</th>";
	echo "</tr>\n";
	for($pass=0;$pass<$passes;$pass++)
		{
		echo "<tr><th>$pass</th>";
		foreach($stats[$pass]['stat'] as $stat=>$val)
			{
			if(!isset($statavg[$stat]))
				$statavg[$stat]=$val;
			else
				$statavg[$stat]+=$val;
			echo "<td>$val</td>";
			}
		echo "</tr>\n";
		}
	echo "<tr><th>Average</th>";
	foreach($statavg as $stat=>$val)
		echo "<th>".($val/$passes)."</th>";
	echo "</tr>\n";
	echo "</table>\n";
    }

if(isset($_GET['climb']))
    $climb=true;
else
    $climb=false;

if(isset($_GET['passes']))
    $passes=$_GET['passes'];
else
    $passes=5;

if(isset($_GET['depth']))
    $depth=$_GET['depth'];
else
    $depth=1000.0;

if(isset($_GET['level']))
    {
    $level=explode(',',$_GET['level']);
    if (count($level)==1)
        $level=$level[0];
    }
else
    $level=array(13,14,19,11,12);

if(isset($_GET['jobstart']))
    $jobstart=$_GET['jobstart'];
else
    $jobstart=min(array_keys($jobs));

if(isset($_GET['jobend']))
    $jobend=$_GET['jobend'];
elseif(!isset($_GET['jobstart']))
    $jobend=count($jobs)-1;
else
    $jobend=max(array_keys($jobs));

$null=null;

$start=time();

for ($index=$jobstart;$index<=$jobend;$index++)
    if (isset($jobs[$index])){
    if(is_array($level))
        $mylevel=$level[$index];
    else
        $mylevel=$level;
    $stats=test_stat_avarage($index,$mylevel,$passes,$depth,$climb);
    display_stat_average($stats);
    }

$end=time();
$diff=$end-$start;
echo "$diff seconds elapsed";
?>
