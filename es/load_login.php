<?php
	session_start();
	include './basedados.h';
	
	if (isset($_SESSION['id_user']) || isset($_SESSION['nivel']))
		include './erro.h';
	
	// verificar se o utilizador preencheu os campos obrigatorios
	if (empty($_POST['user']) || empty($_POST['pwd']))
	{
		$_SESSION['err'] = 'Introduza corretamente os seus dados de acesso.';
		header('refresh: 0; url = ./login.php');
		exit;
	}
	$user = $_POST['user'];
	$pwd = md5($_POST['pwd']);
	
	$sql = "SELECT id, nivel
			FROM Utilizador
			WHERE user = '$user' AND pwd = '$pwd'";
	$retVal = mysqli_query($conn, $sql);
	if (!$retVal)
		die('Falha tecnica.');
	
	// se a consulta retornar uma e apenas uma linha entao o utilizador existe
	if (mysqli_num_rows($retVal) != 1)
	{
		$_SESSION['err'] = 'Introduza corretamente os seus dados de acesso.';
		header('refresh: 0; ./login.php');
		exit;
	}
	// definir as variaveis de sessao
	$dados = mysqli_fetch_assoc($retVal);
	$_SESSION['id_user'] = $dados['id'];
	$_SESSION['nivel'] = $dados['nivel'];
	
	mysqli_close($conn);
	// os administradores serao redirecionados para a pagina de gestao de utilizadores
	if ($_SESSION['nivel'] == 1)
		header('refresh: 0; url = ./index.php');
	// os utilizadores serao redirecionados para a pagina dos seus dados pessoais
	else
		header('refresh: 0; url = ./index.php');
?>