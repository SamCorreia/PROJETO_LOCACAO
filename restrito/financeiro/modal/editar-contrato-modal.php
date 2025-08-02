<?php
	require_once('../../../config.php');
	require_once('../../../functions.php');
	$con = new DBConnection();
	verificaLogin(); getData();
	echo 'Fazendo...';
?>