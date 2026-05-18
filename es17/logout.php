<?php
	session_start();
	
	if (isset($_SESSION['id_user']))
		session_destroy();
	
	// apos o fim da sessao, o utilizador sera redirecionado para a pagina de login
	header('refresh: 0; url = ./login.php');
?>
