<?php
session_start();
require_once('../libs/database.php');


if(empty($db)) $db = connect();

   $pid = $_POST['pid'];
   $cat = $_POST['cat'];
   $uid = $_SESSION['id'];

   $db->query("DELETE FROM evaluate WHERE uid='$uid' AND pid='$pid' AND cat='$cat'");

   $plus  = $db->query("SELECT uid FROM evaluate WHERE pid='$pid' AND cat='$cat' AND value=1")->num_rows;
   $minus = $db->query("SELECT uid FROM evaluate WHERE pid='$pid' AND cat='$cat' AND value=0")->num_rows;

   echo ($plus-$minus);

?>