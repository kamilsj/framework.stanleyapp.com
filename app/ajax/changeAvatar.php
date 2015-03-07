<?php
session_start();

require_once('../libs/database.php');

$pid     = $_POST['num'];
$uid     = $_SESSION['id'];
$json    = array();

if(empty($db)) $db = connect();

if(empty($mem))
{

    $mem = new Memcached;
    $mem->addServer('localhost', 11211);

}







unset($pid, $uid, $json);