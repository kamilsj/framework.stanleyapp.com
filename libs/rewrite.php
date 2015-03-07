<?php

function readURL($url){

    $tmp = array();

    if(strlen($url) > 1){
        @$url = strtolower($url);
        if($url[0] == '/') $url = substr($url, 1);
        @list($tmp['controller'], $tmp['action'], $tmp['id']) = explode('/', $url);

        return $tmp;

    }else return 0;
}