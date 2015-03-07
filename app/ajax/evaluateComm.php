<?php
session_start();

require_once('../libs/database.php');

if(empty($db)) $db = connect();

$pid    = $_POST['pid'];
$cat    = $_POST['cat'];
$value  = $_POST['value'];
$uid    = $_SESSION['id'];

if($db->query("SELECT uid FROM evaluate WHERE uid='$uid' AND cat='$cat' AND pid='$pid'")->num_rows==0)
{
   $db->query("INSERT INTO evaluate SET uid='$uid', pid='$pid', cat='$cat', value='$value'");
}
else
{
   $db->query("UPDATE evaluate SET value='$value' WHERE uid='$uid'AND cat='$cat' AND pid='$pid'");
}


$plus  = $db->query("SELECT uid FROM evaluate WHERE pid='$pid' AND cat='$cat' AND value=1")->num_rows;
$minus = $db->query("SELECT uid FROM evaluate WHERE pid='$pid' AND cat='$cat' AND value=0")->num_rows;

echo ($plus-$minus);





?>