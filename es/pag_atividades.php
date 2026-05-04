<?php
session_start();
include './basedados.h';

$id_atividade = $_GET['id'];
$sql = "SELECT * FROM Atividade WHERE id = '$id_atividade'";
$retVal = mysqli_query($conn, $sql);
if (!$retVal)
	die('Falha tecnica.');

$dados = mysqli_fetch_assoc($retVal);
$titulo = $dados['titulo'];
$data_inicio = $dados['data_inicio'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Algazarra</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color: #F5F5F5; margin: 10vh 0 10vh 0;">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff; border: none;" data-bs-theme="light">
	<div class="container-fluid">
		<a class="navbar-brand" href="index.php">ALGAZARRA</a>

		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav me-auto">
				<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
				<li class="nav-item"><a class="nav-link active" href="atividades.php">Atividades</a></li>
				<li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>

				<?php
				if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 1) {
					echo '
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Gestão</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="gerir_utilizadores.php">Gerir utilizadores</a></li>
							<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>
							<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>
							<li><a class="dropdown-item" href="criar_atividade.php">Criar atividade</a></li>
						</ul>
					</li>';
				}

				if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2) {
					echo '
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Gestão</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>
						</ul>
					</li>';
				}
				?>
			</ul>

			<ul class="navbar-nav ms-auto">
				<?php
				if (isset($_SESSION['id_user']))
					echo '<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>';

				echo '<li class="nav-item">';
				if (isset($_SESSION['id_user']))
					echo '<a class="nav-link" href="dados.php">Dados pessoais</a>';
				else
					echo '<a class="nav-link" href="login.php">Login/Registo</a>';
				echo '</li>';
				?>
			</ul>
		</div>
	</div>
</nav>

<!-- 🔥 SEM CARD EXTERIOR AQUI -->
<div class="container" style="padding: 3vh;">

	<h1 class="text-center"><?php echo $titulo; ?></h1>

	<div class="row mt-4">
		
		<div class="col-md-6">
			<img class="img-fluid rounded" src="<?php echo $dados['imagem']; ?>" alt="atividade">
		</div>

		<div class="col-md-6">
			<h2>Diversão sem fim!</h2>

			<p><?php echo $dados['descricao']; ?></p>

			<h5><strong>Data de início:</strong> <?php echo date('d/m/Y', strtotime($dados['data_inicio'])); ?></h5>
			<h5><strong>Data de fim:</strong> <?php echo date('d/m/Y', strtotime($dados['data_fim'])); ?></h5>
			<h5><strong>Lotação máxima:</strong> <?php echo $dados['lotacao_max'] . ' crianças'; ?></h5>
		</div>
	</div>

<?php
if (date('Y-m-d') >= $data_inicio) {

	echo '<h2 class="text-center mt-5">Esta atividade já está a decorrer.</h2>';

} elseif (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2) {

	$id_pai = $_SESSION['id_user'];

	$sql = "SELECT COUNT(*)
			FROM Crianca c
			WHERE c.pai = '$id_pai'
			AND c.id NOT IN (
				SELECT i.crianca FROM Inscricao i WHERE i.atividade = '$id_atividade'
			)";

	$retVal = mysqli_query($conn, $sql);
	$num_criancas = mysqli_fetch_array($retVal)[0];

	if ($num_criancas > 0) {

		echo '
		<h2 class="text-center mt-5">Está interessado/a?</h2>

		<div class="row justify-content-center mt-3">
			<div class="col-md-4">
				<form action="inscrever.php" method="POST">

					<input type="hidden" name="id_atividade" value="' . $id_atividade . '">

					<select name="id_crianca" class="form-select">';

					$sql = "SELECT id, nome FROM Crianca c
							WHERE c.pai = '$id_pai'
							AND c.id NOT IN (
								SELECT i.crianca FROM Inscricao i WHERE i.atividade = '$id_atividade'
							)";

					$criancas = mysqli_query($conn, $sql);

					while ($crianca = mysqli_fetch_assoc($criancas)) {
						echo '<option value="' . $crianca['id'] . '">' . $crianca['nome'] . '</option>';
					}

		echo '
					</select>

					<button type="submit" class="btn mt-3 w-100" style="background-color:#FD7D21; color:white; font-weight:bold;">
						Inscrever
					</button>

				</form>
			</div>
		</div>';
	}
}
?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include './rodape.php'; ?>