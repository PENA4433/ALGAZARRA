<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || !isset($_SESSION['nivel'])) {
    include './erro.php';
    exit();
}

$id_user = $_SESSION['id_user'];

$sql = "SELECT * FROM utilizador WHERE id = $id_user";
$res = mysqli_query($conn, $sql);

if (!$res) {
    die('Erro na BD: ' . mysqli_error($conn));
}

$dados = mysqli_fetch_assoc($res);

if (!$dados) {
    die("Utilizador não encontrado");
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dados pessoais</title>
    <link rel="stylesheet" href="bootstrap.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #F5F5F5;
            margin: 10vh 0;
        }
    </style>
</head>

<body>

<nav class="fixed-top navbar navbar-expand-lg" style="background-color:#00d0ff;">
    <div class="container-fluid">
        <div class="navbar-collapse collapse" id="navbarColor01">
            <ul class="navbar-nav mx-auto align-items-center justify-content-center">
                <a class="navbar-brand" href="index.php">ALGAZARRA</a>
            </ul>
        </div>
    </div>
</nav>
	<div class="container" style="padding:2vh;">

<?php
if (isset($_SESSION['info'])) {
    echo '<div class="alert alert-success">' . $_SESSION['info'] . '</div>';
    unset($_SESSION['info']);
}

if (isset($_SESSION['err'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['err'] . '</div>';
    unset($_SESSION['err']);
}
?>

<div class="card shadow-sm p-4">

<h2 class="text-center">Dados pessoais</h2>

<form action="alterar_dados.php" method="post">

    <div class="row">

        <div class="col-md-6 mt-3">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control" value="<?= $dados['nome'] ?>" required>
        </div>

        <div class="col-md-6 mt-3">
            <label>Data de nascimento</label>
            <input type="date" name="data_nascimento" class="form-control" value="<?= $dados['data_nascimento'] ?>" required>
        </div>

        <div class="col-md-6 mt-3">
            <label>Telemóvel</label>
            <input type="text" name="telemovel" class="form-control" value="<?= $dados['telemovel'] ?>" required>
        </div>

        <div class="col-md-6 mt-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= $dados['email'] ?>" required>
        </div>

        <div class="col-md-6 mt-3">
            <label>Utilizador</label>
            <input type="text" class="form-control" value="<?= $dados['user'] ?>" disabled>
        </div>

    </div>

    <div class="text-end mt-4">
        <button type="submit" class="btn btn-danger fw-bold">
            Guardar alterações
        </button>
    </div>

</form>

<!-- BOTÃO VOLTAR -->
<div class="text-start mt-3">
    <a href="index.php" class="btn btn-secondary">
        ⬅ Voltar
    </a>
</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
