<?php
require_once INCLUDE_DIR.'job.php';
require_once INCLUDE_DIR.'mysql.php';

require_once INCLUDE_DIR.'js_rip.php';
    if(!function_exists('clean')) {function clean($a) {return addslashes($a);}}

class JOB_STORE
    {
    //This constructor (tries to) initialize the job table.
    function JOB_STORE($reset=false)
        {

        $result=mysql_do_query("select count(*) from jobs",false);
        if($result===false || ($data=mysql_fetch_row($result))===false || $data[0]==0 || $reset===true)
            {
            //Delete tables
            mysql_do_query("DROP TABLE IF EXISTS jobs");
            mysql_do_query("DROP TABLE IF EXISTS job_abilities");
            //Recreate tables.
            mysql_do_query("
CREATE TABLE `jobs` (
  `jobid` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `need` int(11) NOT NULL default '0',
  `HP` float(13,2) NOT NULL default '0.00',
  `MP` float(13,2) NOT NULL default '0.00',
  `accuracy` float(13,2) NOT NULL default '0.00',
  `strength` float(13,2) NOT NULL default '0.00',
  `dodge` float(13,2) NOT NULL default '0.00',
  `block` float(13,2) NOT NULL default '0.00',
  `speed` float(13,2) NOT NULL default '0.00',
  `power` float(13,2) NOT NULL default '0.00',
  `resistance` float(13,2) NOT NULL default '0.00',
  `focus` float(13,2) NOT NULL default '0.00',
  PRIMARY KEY  (`jobid`)
) ;");
            mysql_do_query("
CREATE TABLE `job_abilities` (
  `jobid` int(11) unsigned NOT NULL default '0',
  `abilityid` int(11) unsigned NOT NULL default '0',
  `level` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`jobid`,`abilityid`)
) ;");
            }
        }

    function get_job($index)
        {
        $query="select * from jobs where jobid=$index";
        $result=mysql_do_query($query);
        $data=mysql_fetch_assoc($result);
        if($data===false)
            log_error("job $index does not exist in the database.");
        $query="select abilityid as ability, level from job_abilities where jobid=$index order by level";
        $result=mysql_do_query($query);
        $abilities=array();
        while($data2=mysql_fetch_assoc($result))
            $abilities[]=$data2;
        return new JOB(
            $data['name'],$data['need'],
            array($data['HP'],$data['MP'],$data['speed'],$data['accuracy'],$data['strength'],
                $data['dodge'],$data['block'],$data['power'],$data['resistance'],$data['focus']),
            $abilities);
        }

    function &get_all_jobs()
        {
        $jobs=array();
        $query="select jobid from jobs";
        $result=mysql_do_query($query);
        while(($data=mysql_fetch_row($result))!==false)
            $jobs[$data[0]]=&$this->get_job($data[0]);
        return $jobs;
        }

    function set_job(&$job,$index)
        {
        $name=mysql_real_escape_string($job->name);
        $query="
        replace into jobs
            (jobid,name,need,HP,MP,accuracy,strength,dodge,
            block,speed,power,resistance,focus)
        values
            ($index,'$name',{$job->need},{$job->upgrade["HP"]},{$job->upgrade["MP"]},{$job->upgrade["Accuracy"]},{$job->upgrade["Strength"]},{$job->upgrade["Dodge"]},
            {$job->upgrade["Block"]},{$job->upgrade["Speed"]},{$job->upgrade["Power"]},{$job->upgrade["Resistance"]},{$job->upgrade["Focus"]})";
        mysql_do_query($query);
        mysql_do_query("delete from job_abilities where jobid=$index");
        if(count($job->abilities)>0)
            {
            $abilities=array();
            foreach($job->abilities as $ab_pair)
                $abilities[]="($index,$ab_pair[ability],$ab_pair[level])";
            $query="
            replace into job_abilities
                (jobid,abilityid,level)
            values ".implode(',',$abilities);
            mysql_do_query($query);
            }
        if($index==0)
            $index=mysql_insert_id();
        return $index;
        }

    function delete_job($index)
        {
        mysql_do_query("delete from jobs where jobid=$index");
        mysql_do_query("delete from job_abilities where jobid=$index");
        }

    function write_jobs_file()
        {
        $handle=fopen(INCLUDE_DIR.'jobs.php','w');
        if($handle)
            {
            $jobs=$this->get_all_jobs();
            $job_ser=serialize($jobs);
            $job_js=php_data_to_js($jobs);
            fwrite($handle, '<?php require_once INCLUDE_DIR."job.php"; $GLOBALS["jobs"]=unserialize(<<<EOD
'.$job_ser.'
EOD
); $GLOBALS["jobs_js"]=\''.clean($job_js).'\'; ?>');
            fclose($handle);
            }
        }
    }
?>
