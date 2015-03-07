<?php
session_start();
require_once('../libs/database.php');

const failed = 7;

$email   = strtolower($_POST['email']);
$email   = str_replace(' ', '', $email);
$passwd  = $_POST['passwd'];
$passwd  = str_replace(' ', '', $passwd);

$errors  = array();



if(empty($db)) $db = connect();

if(!empty($_SERVER['HTTP_CLIENT_IP'])){$ip=$_SERVER['HTTP_CLIENT_IP'];
}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];} else {$ip=$_SERVER['REMOTE_ADDR'];}

if($email && $passwd)
{

	$resp = $db->query("SELECT passwd, id FROM users WHERE email='$email' LIMIT 1");
	
	if($resp->num_rows == 1)
	{
		$tmp = $resp->fetch_assoc();
		
		$errors['email'] = 1;

        $resp = $db->query("SELECT * FROM blockLogin WHERE email='$email' AND block=1 LIMIT 1");

        if($resp->num_rows == 0)
        {
            if(sha1($passwd) == $tmp['passwd'])
            {

                /*
                 *  some small security updates - check IP address
                 */

                $errors['stat']    = 'OK';

                $errors['passwd']  = 1;
                $errors['email']   = 1;

                if($tmp['id'] > 0)
                {

                    $_SESSION['id'] = $tmp['id'];
                    setcookie('userid',  $tmp['id'], time()+1209600);
                    setcookie('secode', $tmp['passwd'], time()+1209600);

                }
                else $errors['id'] = 0;
                unset($resp, $tmp, $_SESSION['loginsFailed']);

            }else $errors['passwd'] = 0;

            //Uncoment it if you want to test block layout one the Login Page!
            //$errors['block'] = 1;

        }else
        {
            $tmp = $resp->fetch_assoc();
            $date = $tmp['date'];

            if(strtotime('now') - strtotime($date) >= 259200)
            {
                $db->query("UPDATE blockLogin SET block=0 WHERE email='$email' AND date='$date'");
                $errors['block'] = 1; unset($date);

            }else
            {
                $errors['block'] = 0;
            }

        }

	}else
	{
		$errors['passwd'] = 0;
		$errors['email']  = 0;
	}

}
else
{
		$errors['passwd'] = 0;
		$errors['email']  = 0;
}

/*
 *     BLOCK LOGIN AFTER 3 TIMIES
 *
 */


if($errors['email'] == 1 && $errors['passwd'] == 0  && $errors['block'] == 0)
{

    if(empty($_SESSION['loginsFailed']))
    {
        $_SESSION['loginsFailed'][0] = 0;
        $_SESSION['loginsFailed'][1] = $errors['email'];

    }else
    {

        if($_SESSION['loginsFailed'][1] === $errors['email'])
        {
            $_SESSION['loginsFailed'][0]++;

            if($_SESSION['loginsFailed'][0] >= failed)
            {
                /*
                 *  BLOCK ACCOUNT ALGORITHM
                 */

                $db->query("INSERT INTO blockLogin SET ip='$ip', email='$email', block=1");
                unset($_SESSION['loginsFailed']);
                $errors['block'] = 1;

            }
        }
    }

}

echo json_encode($errors);