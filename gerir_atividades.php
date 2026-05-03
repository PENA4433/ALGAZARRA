<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 1) {
    include './erro.php';
    exit;
}
?>

<html lang="pt">
<head>
	<meta charset="UTF-8">
	<title>Algazarra</title>
	<link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color:#F5F5F5; margin:10vh 0;">

<!-- NAVBAR -->
<nav class="fixed-top navbar navbar-expand-lg" style="background-color:#00d0ff;">
	<div class="container-fluid">
		<a class="navbar-brand" href="index.php">ALGAZARRA</a>

		<div class="collapse navbar-collapse">
			<ul class="navbar-nav me-auto">
				<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
				<li class="nav-item"><a class="nav-link" href="atividades.php">Atividades</a></li>
				<li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>

				<?php if ($_SESSION['nivel'] == 1) { ?>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Gestão</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="gerir_utilizadores.php">Gerir utilizadores</a></li>
							<li><a class="dropdown-item" href="gerir_alunos.php">Gerir alunos</a></li>
							<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>
							<li><a class="dropdown-item" href="criar_atividade.php">Criar atividade</a></li>
						</ul>
					</li>
				<?php } ?>
			</ul>

			<ul class="navbar-nav ms-auto">
				<li class="nav-item"><a class="nav-link" href="dados.php">Dados pessoais</a></li>
				<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container" style="padding:2vh;">

<?php
if (isset($_SESSION['info'])) {
	echo '<div class="alert alert-success">'.$_SESSION['info'].'</div>';
	unset($_SESSION['info']);
}

if (isset($_SESSION['err'])) {
	echo '<div class="alert alert-danger">'.$_SESSION['err'].'</div>';
	unset($_SESSION['err']);
}
?>

<h1 class="text-center mb-3">Gerir atividades</h1>

<?php
$sql = "SELECT * FROM atividade";
$atividades = mysqli_query($conn, $sql);

if (!$atividades) die('Erro na BD');

while ($atividade = mysqli_fetch_assoc($atividades)) {

	$id_atividade = $atividade['id'];

	$sql = "
		SELECT 
			i.aluno,
			a.nome AS aluno_nome,
			u.nome AS encarregado_nome,
			u.telemovel
		FROM inscricao i
		INNER JOIN aluno a ON i.aluno = a.id
		INNER JOIN enc_educacao u ON a.enc_educacao = u.id
		WHERE i.atividade = '$id_atividade'
		ORDER BY a.nome
	";

	$inscricoes = mysqli_query($conn, $sql);
	if (!$inscricoes) die('Erro na BD');

	if (mysqli_num_rows($inscricoes) == 0) continue;
?>

<!-- ATIVIDADE HEADER -->
<div class="row mb-2">
	<div class="col-9">
		<h4>
			<?= $atividade['titulo'] ?> -
			De <?= date('d/m/Y', strtotime($atividade['data_inicio'])) ?>
			a <?= date('d/m/Y', strtotime($atividade['data_fim'])) ?>
		</h4>
	</div>

	<div class="col-3 text-end">
		<a class="btn btn-sm btn-primary"
		   href="editar_atividade.php?id=<?= $id_atividade ?>">
			Alterar
		</a>

		<a class="btn btn-sm btn-danger"
		   href="apagar_atividade.php?id=<?= $id_atividade ?>"
		   onclick="return confirm('Tens a certeza que queres apagar esta atividade?')">
			Apagar
		</a>
	</div>
</div>

<!-- TABELA -->
<table class="table mb-4">
	<thead>
	<tr>
		<th style="background:#00d0ff;">Nome do aluno</th>
		<th style="background:#00d0ff;">Encarregado de educação</th>
		<th style="background:#00d0ff;">Contacto</th>
		<th style="background:#00d0ff;"></th>
	</tr>
	</thead>

	<?php while ($row = mysqli_fetch_assoc($inscricoes)) { ?>
	<tr>
		<td><?= $row['aluno_nome'] ?></td>
		<td><?= $row['encarregado_nome'] ?></td>
		<td><?= $row['telemovel'] ?></td>
		<td>
			<a class="text-danger fw-bold"
			   href="marcar_presencas.php?aluno=<?= $row['aluno'] ?>&atividade=<?= $id_atividade ?>">
				Marcar presenças
			</a>
		</td>
	</tr>
	<?php } ?>

</table>

<?php } ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include './rodape.php'; ?>