<?php
session_start();
include_once('config.php');
$con = new DBConnection();

	if(isset($_GET['acao']) && $_GET['acao'] == 'true'){
		try{
			session_destroy();
			setcookie ('user', ' ', time() - 3600);
			setcookie ('password', ' ', time() - 3600);
		}
		catch(PDOException $i)
		{
			echo 'Error: ' . $i->getMessage();
		}
		echo "<script>location.href='index.php'</script>";
		exit;		
	}
?>