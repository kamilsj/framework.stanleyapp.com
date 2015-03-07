<?php
session_start();

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'on');

date_default_timezone_set('Europe/Warsaw');
/*
 *
 *
 */

if(file_exists('libs/config.php'))
{
    require_once('libs/config.php');

    if(file_exists('libs/database.php') && file_exists('libs/modules.php') && file_exists('libs/lang.php') && file_exists('libs/rewrite.php'))
    {
        require_once('libs/database.php');
        require_once('libs/modules.php');
        require_once('libs/lang.php');
        require_once('libs/rewrite.php');

    }else exit('Fatal, total error! Could you clone stanley repository once again, please!');


    global $layout;
    global $db;
    global $user;
    global $posts;
    global $lang;
    global $path;

    if(class_exists('Memcached'))
    {

        global $mem;
        $mem = new Memcached;
        $mem->addServer('localhost', 11211);

    }
    else
    {
        exit('Need memcache to run');

    }


    $db         	= connect();
    $user      		= null;
    $posts 			= null;
    $lang       	= setLanguage();
    $layout     	= new layout();
    $layout->db 	= $db;
    $layout->lang   = $lang;

    $url      = array();
    $url      = readURL($_SERVER['REQUEST_URI']);  //.htaccess based on Zend Framework solutions
    $path     = '';

    if(isset($url)){

        if($url['controller'])$what = $url['controller']; else $what = 'start';
        $a  = $url['action'];
        $what_id = $url['id'];

        $allowed = array('category');

        if(in_array($what, $allowed)){

            if($a){ $what_id = $a; $path = '../'; }
            if($what){ $a = $what; $path.= $path;};
            $what  = 'start';
            $layout->path = $path;
        }

    }else { $path = ''; $layout->path = $path;}

    if(empty($_SESSION['id']))
    {
        if(isset($_COOKIE['userid']) && isset($_COOKIE['secode']))
        {
            $id   = $_COOKIE['id'];
            $user = $db->query("SELECT * FROM users WHERE id='$id' LIMIT 1")->fetch_assoc();

            if($user['passwd'] == $_COOKIE['secode']) $_SESSION['id'] = $_COOKIE['userid'];
            unset($user);
        }

    }


    if(isset($_SESSION['id']))
    {
        /*
         *  inside the webpage ... only registered users
         */

        $id = $_SESSION['id'];


        if($what)
        {

            if(empty($user)) $user = $db->query("SELECT * FROM users WHERE id='$id' LIMIT 1")->fetch_assoc();
            $layout->what = $what;
            $layout->jq   = 'jq20.js';
            if(defined('gog_analytics_num')){ if(gog_analytics_num != 'CHANGE_ME') $layout->google_analytics = gog_analytics_num; }

            $layout->up(title);

            switch($what)
            {
                case 'start':
                {
                    require('layouts/start.php');
                    break;
                }
                case 'profile':
                {
                    require('layouts/profile.php');
                    break;
                }
                case 'messages':
                {
                    require('layouts/messages.php');
                    break;
                }
                case 'board':
                {
                    require('layouts/board.php');
                    break;
                }
                case 'files':
                {
                    require('layouts/files.php');
                    break;
                }
                case 'admim':
                {
                    require('layouts/admin.php');
                    break;
                }
                default:
                {
                    require('layouts/start.php');
                    break;
                }
            }

            $layout->down();

        }else
        {

        }

    }else
    {
        /*
        login page
        */
        if(defined('social') && defined('fbid') && defined('fbsec'))
        {

            if(social == 1)
            {
                if(fbid!='CHANGE_ME' && fbsec!='CHANGE_ME')
                {
                    require_once('libs/ext/facebook/facebook.php');

                    if(class_exists('Facebook'))
                    {


                        $fb = new Facebook(array(
                            'appId'  => fbid,
                            'secret' => fbsec,
                        ));

                        $user   = $fb->getUser();
                        $fblog  = $fb->getLoginUrl(
                            array(
                                'scope'         => 'email',
                                'redirect_uri'  => url
                            ));

                        if ($user)
                        {
                            try
                            {

                                $user_profile = $fb->api('/me');

                            } catch (FacebookApiException $e)
                            {

                            }
                        }

                        $layout->appid	= $fb->getAppID();
                    }
                }
                elseif(twkey!='CHANGE_ME' && twsec!='CHANGE_ME')
                {


                    require_once('libs/ext/twitter/twitteroauth.php');


                }
            }

        }

        $layout->up($lang->enter->title);
        require('layouts/enter.php');
        $layout->down();

    }

    $layout->clean();
}else{

    if(file_exists('libs/config.php.example'))
    {
        /*
         * START INSTALLATION PROCESS 
         */

        




    } else exit('No configuration file!');
}

?>
