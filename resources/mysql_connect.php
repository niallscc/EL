<?php
	ini_set("display_errors",1);	
	require_once($_SERVER['DOCUMENT_ROOT']."/private/my_info.php");
	$mysqli = new mysqli(get_host(), get_username(), get_password(), get_db_name());
	if (mysqli_connect_errno()) {
		echo '{"status":"error", "message":"'.mysqli_connect_error().' document root is: '.$_SERVER['DOCUMENT_ROOT'].' HOST IS: '.get_host().'}';
		exit();
	}
?>