<?php
session_start();


session_unset();
session_destroy();

/*
	delete cookies
*/

setcookie('userid',  '', time()-1209600);
setcookie('secode', '', time()-1209600);

?>