<?php
session_start();
require_once('../libs/database.php');

$q    = $_POST['q'];
$spec = $_POST['spec'];	

$research = array();
$results  = array();
$sql	  = null;
$resp     = null;
$json     = array();

if(empty($db)) $db = connect();

/*

let's see what is going to be done

*/

function posts($what, $limit, $db)
{
	return $db->query("SELECT * FROM posts WHERE body LIKE '%$what%' ORDER BY RAND() LIMIT $limit");
}

function comments($what, $limit, $db)
{
	return $db->query("SELECT * FROM posts WHERE body LIKE '%$what%' ORDER BY RAND() LIMIT $limit");
}

function users($what, $limit, $db)
{
    return $db->query("SELECT * FROM users WHERE (sname LIKE '%$what%' OR fname LIKE '%$what%' OR zhname LIKE '%$what%') ORDER BY RAND() LIMIT $limit");
}


if($spec == '')
{

    $users     = users($q, 3, $db);
    $posts     = posts($q, 5, $db);
	$comments  = comments($q, 5, $db);
    $files     = null;

}
else
{

	list($research) = explode(' ', $q);

	if(count($research) <= 2)
	{



	}

}

/*
preparing results
*/

while($usersTmp = $users->fetch_assoc() || $postsTmp = $posts->fetch_assoc() || $commentsTmp = $comments->fetch_assoc())
{


}



unset($ask);
echo json_encode($json);
?>