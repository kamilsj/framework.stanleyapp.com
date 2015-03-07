<?php

function setLanguage()
{

	if(!empty($_SERVER['HTTP_CLIENT_IP'])){$ip=$_SERVER['HTTP_CLIENT_IP'];} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];} else { $ip=$_SERVER['REMOTE_ADDR']; }
	
	$response=@file_get_contents('http://www.netip.de/search?query='.$ip);	 
		
		$patterns=array(); 
		$patterns["domain"] = '#Domain: (.*?)&nbsp;#i'; 
		$patterns["country"] = '#Country: (.*?)&nbsp;#i'; 
		$patterns["state"] = '#State/Region: (.*?)<br#i'; 
		$patterns["town"] = '#City: (.*?)<br#i'; 
		
		$ipInfo=array();
	 
	foreach ($patterns as $key => $pattern){ $ipInfo[$key] = preg_match($pattern,$response,$value) && !empty($value[1]) ? $value[1] : 'not found'; }
	 
	$code = strtolower(substr($ipInfo["country"], 0, 2));
    $file = null;

	switch ($code)
    {
        case 'en':
        {
            $file = 'eng.xml';
            break;
        }
        case 'tw':
        {
            $file = 'tw.xml';
            break;
        }
        case 'zh':
        {
            $file = 'zh.xml';
            break;
        }
        case 'pl':
        {
            $file = 'pl.xml';
            break;
        }
        default:
        {
            $file = 'eng.xml';
            break;
        }
    }

    unset($ipInfo, $response);

    if(file_exists('locales/'.$file)) return simplexml_load_file('locales/'.$file); else return simplexml_load_file('locales/eng.xml');

}

?>