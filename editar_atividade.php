<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 1) {
    include './erro.php';
    exit;
}

$id = $_GET['id'];

// 🔒 Segurança: prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM atividade WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$atividade = mysqli_fetch_assoc($result);

if (!$atividade) {
    $_SESSION['err'] = "Atividade não encontrada.";
    header("Location: gerir_atividades.php");
    exit;
}
?>
    <meta charset="UTF-8">
    <title>Algazarra</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff; border: none;" data-bs-theme="light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarColor01">
            <ul class="navbar-nav mx-auto align-items-center justify-content-center">
                <a class="navbar-brand" href="index.php">ALGAZARRA</a>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="atividades.php">Atividades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contactos.php">Contactos</a>
                </li>
                <?php
                // Administrador
                if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 1) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownAdmin">';
                    echo '<li><a class="dropdown-item" href="gerir_utilizadores.php">Gerir utilizadores</a></li>';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Alunos</a></li>';
                    echo '<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>';
                    echo '<li><a class="dropdown-item" href="criar_atividade.php">Criar atividade</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
                // Pai
                if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPai" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownPai">';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Alunos</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
                ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php
                if (isset($_SESSION['id_user']) && isset($_SESSION['nivel'])) {
                    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>';
                }
                echo '<li class="nav-item">';
                if (isset($_SESSION['id_user']) && isset($_SESSION['nivel'])) {
                    echo '<a class="nav-link" href="dados.php">Dados pessoais</a>';
                } else {
                    echo '<a class="nav-link" href="login.php">Login</a>';
                }
                ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- NAVBAR (igual ao resto do site) -->

	<div class="container" style="padding:2vh;">

<div class="card" style="background-color:#C5C6D0; border:none; padding:3vh;">
<div class="card-body">

<h1 class="text-center mb-4">Editar atividade</h1>

<form method="POST" action="atualizar_atividade.php">

    <input type="hidden" name="id" value="<?= $atividade['id'] ?>">

    <div class="mb-3">
        <label class="form-label">Título</label>
        <input type="text" class="form-control"
               name="titulo"
               value="<?= htmlspecialchars($atividade['titulo']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Data de início</label>
        <input type="date" class="form-control"
               name="data_inicio"
               value="<?= $atividade['data_inicio'] ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Data de fim</label>
        <input type="date" class="form-control"
               name="data_fim"
               value="<?= $atividade['data_fim'] ?>">
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary">
            Guardar alterações
        </button>
        <a href="gerir_atividades.php" class="btn btn-secondary">
            Cancelar
        </a>
    </div>

</form>

</div>
</div>

</div>

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>