<?php
session_start();

require_once('../libs/database.php');

if(empty($db)) $db = connect();

$old  = $_POST['old'];
$pwd1 = $_POST['pwd1'];
$pwd2 = $_POST['pwd2'];

$errors = array();

$oldSHA1 = sha1($old);
$uid     = $_SESSION['id'];

$res = $db->query("SELECT passwd, block FROM users WHERE id='$uid' LIMIT 1")->fetch_assoc();

if($oldSHA1 == $res['passwd'] && $res['block'] == 0)
{
    if($pwd1 == $pwd2)
    {
        if($pwd1 != $old)
        {
            $newSHA1 = sha1($pwd1);
            $db->query("UPDATE users SET passwd = '$newSHA1' WHERE id='$uid'");
            $errors['stat'] = 'OK';

        }else
        {
            $errors['oldsame'] = 1;
        }


    }else
    {
        $errors['nsame'] = 1;
    }
}
else
{
    $errors['nold'] = 1;
}



echo json_encode($errors);