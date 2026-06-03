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
                    echo '<li><a class="dropdown-item" href="inscrever_aluno_professor.php">Inscrever aluno</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
                // Pai
                if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPai" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownPai">';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Alunos</a></li>';
                    echo '<li><a class="dropdown-item" href="criar_perfil.php">Adicionar Alunos</a></li>';
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

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>