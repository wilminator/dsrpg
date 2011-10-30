<?php
require_once INCLUDE_DIR.'errorlog.php';

$GLOBALS['__MYSQL_CONNECTION']=null;

function mysql_do_connect($user=false, $pass=false)
    {
    if(is_null($GLOBALS['__MYSQL_CONNECTION']))
        {
        $data = file(INCLUDE_DIR.'mysql.cfg');
        $host=$data[1];
        if (!$user)
            $user=$data[2];
        if (!$pass)
            $pass=$data[3];
        $GLOBALS['__MYSQL_CONNECTION']=mysql_connect($host,$user,$pass);
        if($GLOBALS['__MYSQL_CONNECTION']===false)
            log_error("The server at $host could not be accessed with the username $user and the password $pass.");
        if(mysql_select_db($data[0])===false)
            log_error("The database $db could not be selected as default.");
        }
    }

function mysql_cleanup($conn,$error_type,$data)
    {
    mysql_close($conn);
    }

function mysql_do_query($query,$bail=true)
    {
    mysql_do_connect();
    $result=mysql_query($query,$GLOBALS['__MYSQL_CONNECTION']);
    if($result===false)
        if($bail) {
            log_error(mysql_error());
            exit;
        }
        else
            log_error(mysql_error());
    return $result;
    }
?>
