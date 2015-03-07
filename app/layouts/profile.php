<?php
if(strlen(@$user['img'])>0)	$pic = $user['img']; else $pic = 'gfx/faces/23.png';
if(strlen(@$user['fname'])>0 && strlen($user['sname'])>0) $name = $user['fname'].' '.$user['sname']; else $name = $user['zhname'];

$layout->name  = $name;
$layout->pic   = $pic;

$layout->baner();

/*
 *
 */
if(isset($a)) $in=$a; else $in='general';

?>
<link href="../../css/ext/checkbox.css" rel="stylesheet" type="text/css" />
<div class="mainArea native margin">
	<div class="inside">	
		<div class="sp1">
			<ul style="list-style: none; padding-left: 10px;">
                <li><a href="/profile/general" class="big">General</a></li>
                <li><a href="/profile/privacy" class="big">Privacy</a></li>
                <li><a href="/profile/stat" class="big">Statistics</a></li>
                <li><a href="/profile/public"class="big"><br />Public profile</a></li>
			</ul>
		</div>
		<div class="sp2">

		    <div id="profileArea">
                <?php
                    switch($in)
                    {
                        case 'general':
                        {
                            require 'in/profile/general.php';
                            break;
                        }
                        case 'privacy':
                        {
                            require 'in/profile/privacy.php';
                            break;
                        }
                        case 'stat':
                        {
                            require 'in/profile/stat.php';
                            break;
                        }
                        case 'public':
                        {
                            require 'in/profile/public.php';
                            break;
                        }
                        default:
                        {

                            break;
                        }
                    }

                ?>
		    </div>
        </div>
		<div class="sp3">
			
		</div>	
	</div>
</div>


<script type="text/javascript" src="../js/profile.js"></script>