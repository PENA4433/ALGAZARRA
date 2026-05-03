<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 1) {
    include './erro.h';
    exit();
}

$id_user = $_SESSION['id_user'];


// =========================
// APAGAR UTILIZADOR
// =========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apagar_id'])) {

    $id = intval($_POST['apagar_id']);

    if ($id != $id_user) {
        mysqli_query($conn, "DELETE FROM utilizador WHERE id = $id");
    }

    header("Location: gerir_utilizadores.php");
    exit();
}


// =========================
// EDITAR UTILIZADOR (UPDATE)
// =========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_id'])) {

    $id = intval($_POST['editar_id']);

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telemovel = $_POST['telemovel'];
    $data_nascimento = $_POST['data_nascimento'];

    $sql = "UPDATE utilizador SET 
            nome='$nome',
            email='$email',
            telemovel='$telemovel',
            data_nascimento='$data_nascimento'
            WHERE id=$id";

    mysqli_query($conn, $sql);

    header("Location: gerir_utilizadores.php");
    exit();
}


// =========================
// CARREGAR UTILIZADOR PARA EDITAR
// =========================
$utilizador_editar = null;

if (isset($_GET['editar'])) {

    $id_edit = intval($_GET['editar']);

    $res = mysqli_query($conn, "SELECT * FROM utilizador WHERE id=$id_edit");
    $utilizador_editar = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
	<meta charset="UTF-8">
	<title>Algazarra</title>
	<link rel="stylesheet" href="bootstrap.min.css">

	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

	<style>
		body {
			font-family: 'Poppins', sans-serif;
			background-color: #F5F5F5;
			margin: 10vh 0;
		}

		* {
			font-family: 'Poppins', sans-serif;
		}

		.btn-alterar {
			background-color: #1f3a93;
			color: white;
			font-weight: 600;
			border: none;
		}

		.btn-alterar:hover {
			background-color: #162a6f;
		}

		.btn-apagar {
			background-color: #dc3545;
			color: white;
			font-weight: 600;
			border: none;
		}

		.btn-apagar:hover {
			background-color: #bb2d3b;
		}

		.card {
			border-radius: 15px;
		}

		.navbar {
			background-color: #00d0ff !important;
		}
	</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="fixed-top navbar navbar-expand-lg">
	<div class="container-fluid">
		<a class="navbar-brand" href="index.php">ALGAZARRA</a>

		<div class="collapse navbar-collapse">

			<ul class="navbar-nav me-auto">
				<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
				<li class="nav-item"><a class="nav-link" href="atividades.php">Atividades</a></li>
				<li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>
			</ul>

		</div>
	</div>
</nav>

<div class="container">

<h1 class="text-center mb-4">Gerir utilizadores</h1>

<!-- ========================= -->
<!-- FORM EDITAR -->
<!-- ========================= -->
<?php if ($utilizador_editar): ?>

<div class="card p-4 mb-4 shadow-sm">

	<h4>Editar utilizador</h4>

	<form method="POST">

		<input type="hidden" name="editar_id" value="<?= $utilizador_editar['id'] ?>">

		<div class="mb-2">
			<input type="text" name="nome" class="form-control"
				value="<?= $utilizador_editar['nome'] ?>" required>
		</div>

		<div class="mb-2">
			<input type="email" name="email" class="form-control"
				value="<?= $utilizador_editar['email'] ?>" required>
		</div>

		<div class="mb-2">
			<input type="text" name="telemovel" class="form-control"
				value="<?= $utilizador_editar['telemovel'] ?>" required>
		</div>

		<div class="mb-2">
			<input type="date" name="data_nascimento" class="form-control"
				value="<?= $utilizador_editar['data_nascimento'] ?>" required>
		</div>

		<button type="submit" class="btn btn-primary">Guardar alterações</button>

		<a href="gerir_utilizadores.php" class="btn btn-secondary">Cancelar</a>

	</form>

</div>

<?php endif; ?>


<!-- ========================= -->
<!-- LISTA UTILIZADORES -->
<!-- ========================= -->

<div class="row justify-content-center">

<?php
$sql = "SELECT * FROM utilizador WHERE id != '$id_user' ORDER BY nome";
$res = mysqli_query($conn, $sql);

while ($u = mysqli_fetch_assoc($res)) {
?>

<div class="col-md-3">

	<div class="card my-3 shadow-sm">

		<div class="card-body">

			<h5><?= $u['nome'] ?></h5>

			<p><?= $u['email'] ?></p>
			<p><?= $u['telemovel'] ?></p>

			<!-- EDITAR -->
			<a href="gerir_utilizadores.php?editar=<?= $u['id'] ?>"
				class="btn btn-alterar me-2">
				Alterar dados
			</a>

			<!-- APAGAR -->
			<form method="POST" style="margin-top:10px;">
				<input type="hidden" name="apagar_id" value="<?= $u['id'] ?>">
				<button type="submit" class="btn btn-apagar">Apagar</button>
			</form>

		</div>

	</div>

</div>

<?php } ?>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>