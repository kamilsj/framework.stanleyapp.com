<?php

class layout
{

    const  VERSION = '0.0.0.9';

	public $db;
	public $name;
	public $pic;
	public $appid;
    public $jq   = 'jq110.js';
    public $google_analytics = 0;
	public $what = 'start';
    public $lang;

    public $path = '../../';

	public function up($title)
	{

		echo
		
		'
		<!DOCTYPE html>
		<html>
		<head>
		<title>'.$title.'</title>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta content="noodp, noydir" name="robots"></meta>
		<meta id="meta_referrer" content="default" name="referrer"></meta>
		
		<!-- <link href="'.$this->path.'css/ext/default.css" rel="stylesheet" type="text/css" /> -->
		<link href="'.$this->path.'css/main.css" rel="stylesheet" type="text/css" />
		<link href="'.$this->path.'css/ext/tipped/tipped.css" rel="stylesheet" type="text/css" />
		<link href="'.$this->path.'css/ext/lightview/lightview.css" rel="stylesheet" type="text/css" />
		<link href="'.$this->path.'css/ext/uploadfive.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="'.$this->path.'js/jq/'.$this->jq.'"></script>
		<link href="https://c730088.ssl.cf2.rackcdn.com/gfx/favicon.png" rel="shortcut icon">
        <link href="https://c730088.ssl.cf2.rackcdn.com/gfx/apple-touch-icon.png" rel="apple-touch-icon">
		</head>
		<body>
		<div id="mainContainer">
		';
		
	}

	public function down()
	{
		
		echo '</div>';

        if(isset($this->appid))
        {
            echo '
            <div id="fb-root"></div>
            <script>
              window.fbAsyncInit = function() {
                FB.init({
                  appId:'.$this->appid.',
                  cookie: true,
                  xfbml: true,
                  oauth: true
                });
                FB.Event.subscribe(\'auth.login\', function(response) {
                  window.location.reload();
                });
              };
              (function() {
                var e = document.createElement(\'script\'); e.async = true;
                e.src = document.location.protocol +
                  "//connect.facebook.net/en_US/all.js";
                document.getElementById(\'fb-root\').appendChild(e);
              }());
            </script>';
        }
        echo
		'<!--[if lt IE 9]>
			<script type="text/javascript" src="'.$this->path.'js/ext/excanvas/excanvas.js"></script>
		<![endif]-->
		<script type="text/javascript" src="'.$this->path.'js/ext/popup.js"></script>
		<script type="text/javascript" src="'.$this->path.'js/ext/spinners/spinners.min.js"></script>
		<script type="text/javascript" src="'.$this->path.'js/ext/lightview/lightview.js"></script>
		<script type="text/javascript" src="'.$this->path.'js/ext/tipped/tipped.js"></script>
		<script type="text/javascript" src="'.$this->path.'js/ext/uploadfive.js"></script>
		<script type="text/javascript" src="'.$this->path.'js/main.js"></script>';

        if($this->google_analytics != 0)
        {

            echo '<script>
                    (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                    })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

                    ga(\'create\', \''.$this->google_analytics.'\', \''.url.'\');
                    ga(\'send\', \'pageview\');

                </script>';

        }

		echo '<script type="text/javascript">
			jQuery(document).ready(function($) { $(\'.tipped\').each(function(){ Tipped.create(this); }); });
		</script>
		<div id="searchArea">
			<div class="menu">
				<div id="inputText"></div>
				<div id="searchBody" class="searchBody"></div>
			<div>		
		</div>
		<br /><br />
		</body>
		</html>';
		
	}
	
	public function baner()
	{
		
		if($this->what == 'start'){ $url = '/profile'; $cl = $this->lang->main->profile; } else { $url = '/'; $cl = $this->lang->main->start; }
		
		echo '
		<div class="topBaner">
		<div class="menu">
			<table>
				<tr>
					<td><a href="'.$url.'"><img id="banerPhoto" src="'.$this->pic.'" width="35" height="35" border="0" class="tipped" title="'.$cl.'" /></a></td>
					<td style="width: 262px;"><span class="upName">'.$this->name.'</span></td>
					<td style="width: 600px;">
						<input id="search" name="search" class="search"></input>
						<div id="msg"></div>
					</td>				
					<td style="text-align: right; width: 100px;"><a href="#"><img id="logout" class="tipped" title="'.$this->lang->main->exit.'" src="'.$this->path.'gfx/poweroff.gif" width="20" height="20" border="0" /></a></td>
				</tr>
			</table>	
		</div>
		</div>';
		
		unset($url, $cl);
	}

    public function clean()
    {
        unset($_SESSION['tmpArray'], $_SESSION['dropboxAdded'], $_SESSION['tmpArrayFiles']);
        $this->db->close();
    }
	
	public function categories()
	{
		
		
	}	
}