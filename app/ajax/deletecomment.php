<?php
session_start();

require_once('../libs/database.php');

$cid    = $_POST['cid'];
$pid    = $_POST['pid'];
$errors = array();

if(empty($db)) $db = connect();
if(empty($mem))
{
    $mem = new Memcached;
    $mem->addServer('localhost', 11211);
}

if($db->query("DELETE FROM comments WHERE id='$cid'"))
{
    $sql     =  "SELECT * FROM comments WHERE pid='$pid' ORDER BY date";
    $key     =  md5('stanley'.$sql);
    $mem->delete($key);

    $errors['stat'] = 'OK';
}
else
{
    $errors['db'] = 1;
}

unset($sql, $key);


echo json_encode($errors);
?>