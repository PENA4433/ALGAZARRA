<?php
	session_start();
	
	if (isset($_SESSION['id_user']))
		session_destroy();
	
	// apos o fim da sessao, o utilizador sera redirecionado para a pagina de login
	header('refresh: 0; url = ./login.php');
?>

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>