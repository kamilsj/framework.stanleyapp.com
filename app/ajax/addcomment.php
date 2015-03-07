<?php
session_start();

require_once('../libs/database.php');

$pid  = $_POST['pid'];
$body = $_POST['body'];

$errors = array();

if(empty($db)) $db = connect();
if(empty($mem))
{
	$mem = new Memcached;
	$mem->addServer('localhost', 11211);
}
		

if(strlen($body)>0 && strlen($body)<4096)
{

	$body = htmlspecialchars($body);
    $body = addslashes($body);

    $uid  = $_SESSION['id'];
	
	if($db->query("INSERT INTO comments SET uid='$uid', pid='$pid', body='$body'"))		
	{
		
		$cid = $db->insert_id;
		
		$sql = "SELECT * FROM comments WHERE pid='$pid' ORDER BY date";
		$mem->delete(md5('stanley'.$sql));
		
		
		if(isset($mem))
		{
			$sql   = "SELECT * FROM users WHERE id='$uid'";
			$key    = md5('stanley'.$sql);
			$user  = $mem->get($key);
		
			if(!$user)
			{
			
				$half = $db->query($sql)->fetch_assoc();	
				$mem->set($key, $half, 864000);
				$user = $mem->get($key); 
			}	
		
		}
		
		if(strlen(@$user['img'])>0)	$pic = $user['img']; else $pic = 'gfx/faces/23.png';
		if(strlen(@$user['fname'])>0 && strlen($user['sname'])>0) $name = $user['fname'].' '.$user['sname']; else $name = $user['zhname'];
		
		$errors['stat'] = 'OK';
		$errors['cid']  = $cid;
		$errors['div']  = '<div class="comment" id="comment'.$cid.'">
							 <table celpadding="0" cellspacing="0">
							 	<tr>
								<td><img src="'.$pic.'" width="25" height="25" class="tipped" title="'.$name.'" /></td>
								<td>'.stripslashes($body).'</td>
								</td>
							 </table>									
							 </div>';
	}
	
	
}else
{
	$errors['len'] = 1;
}	
	
	
unset($id);	
echo json_encode($errors);


?>