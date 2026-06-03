<?php
    session_start();
    include './basedados.h';

    if (empty($_POST['titulo']) || empty($_POST['descricao']) || empty($_POST['data_inicio']) || empty($_POST['data_fim']) || empty($_POST['lotacao']) || empty($_POST['img']))
    {
		$_SESSION['err'] = 'Não preencheu campos obrigatórios.';
		header('refresh: 0; url = ./criar_atividade.php');
		exit;
	}
	$titulo = $_POST['titulo'];
	$descricao = $_POST['descricao'];
	$data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $lotacao = $_POST['lotacao'];
    $img = $_POST['img'];

    $sql = 'SELECT MAX(id) FROM Atividade';
	$retVal = mysqli_query($conn, $sql);
	if (!$retVal)
		die('Falha tecnica.');
	$id = ++mysqli_fetch_array($retVal)[0];

    $sql = "INSERT INTO Atividade (id, titulo, descricao, imagem, data_inicio, data_fim, lotacao_max) VALUES ('$id', '$titulo', '$descricao', '$img', '$data_inicio', '$data_fim', '$lotacao')";
    $retVal = mysqli_query($conn, $sql);
	if (!$retVal)
		die('Falha tecnica.');
    if (mysqli_affected_rows($conn) == 1)
        $_SESSION['info'] = "Criou uma nova atividade!";

    mysqli_close($conn);
    // o administrador sera redirecionado para a pagina de gestao de atividades
    header('refresh: 0; url = ./gerir_atividades.php');
?>

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>