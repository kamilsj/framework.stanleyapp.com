<?php
require_once('config.php');

function connect()
{
    $db = new mysqli(dbhost, dblog, dbpass, dbname);

    $db->query("SET NAMES 'utf8'");
    $db->query("SET time_zone = 'Europe/Warsaw'");

    return $db;

}