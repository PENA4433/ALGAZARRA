<?php
session_start();
include './basedados.h';

/* 🔐 Apenas professores (nível 1) */
if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 1) {
    $_SESSION['erro'] = 'Login necessário. Apenas professores podem marcar presenças.';
    header('Location: erro.php');
    exit;
}

$id_aluno = isset($_GET['aluno']) ? intval($_GET['aluno']) : 0;
$id_atividade = isset($_GET['atividade']) ? intval($_GET['atividade']) : 0;

if ($id_aluno == 0 || $id_atividade == 0) {
    $_SESSION['erro'] = 'Parâmetros inválidos: aluno ou atividade em falta.';
    header('Location: erro.php');
    exit;
}

/* Nome do aluno */
$stmt = mysqli_prepare($conn, "SELECT nome FROM aluno WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_aluno);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$res || mysqli_num_rows($res) == 0) {
    $_SESSION['erro'] = 'Aluno não encontrado.';
    header('Location: erro.php');
    exit;
}

$nome_aluno = mysqli_fetch_assoc($res)['nome'];
mysqli_stmt_close($stmt);

/* Nome da atividade */
$stmt = mysqli_prepare($conn, "SELECT titulo FROM atividade WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_atividade);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$res || mysqli_num_rows($res) == 0) {
    $_SESSION['erro'] = 'Atividade não encontrada.';
    header('Location: erro.php');
    exit;
}

$titulo_atividade = mysqli_fetch_assoc($res)['titulo'];
mysqli_stmt_close($stmt);

/* Inscrições (dias) */
$stmt = mysqli_prepare($conn, "SELECT * FROM inscricao WHERE aluno = ? AND atividade = ? ORDER BY dia");
mysqli_stmt_bind_param($stmt, "ii", $id_aluno, $id_atividade);
mysqli_stmt_execute($stmt);
$dias = mysqli_stmt_get_result($stmt);

if (!$dias) {
    $_SESSION['erro'] = 'Erro ao carregar inscrições: ' . mysqli_error($conn);
    header('Location: erro.php');
    exit;
}

mysqli_stmt_close($stmt);
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


	<div class="container" style="padding:2vh;">

<h2 class="text-center">Marcar presenças</h2>

<h4 class="text-center mb-4">
    Aluno: <?= htmlspecialchars($nome_aluno) ?><br>
    Atividade: <?= htmlspecialchars($titulo_atividade) ?>
</h4>

<?php
if (isset($_SESSION['info'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo $_SESSION['info'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['info']);
}
?>

<table class="table">
    <tr>
        <th>Data</th>
        <th>Estado da Presença</th>
        <th>Ações</th>
    </tr>

<?php if (mysqli_num_rows($dias) == 0) { ?>

    <tr>
        <td colspan="3" class="text-center text-muted py-4">
            Nenhuma inscrição encontrada para este aluno nesta atividade.
        </td>
    </tr>

<?php } else {

    while ($dia = mysqli_fetch_assoc($dias)) {
        $data = $dia['dia'];
        $presente = $dia['esta_presente'];
?>

    <tr>
        <td><?= date('d/m/Y', strtotime($data)) ?></td>
        <td>
            <span class="badge <?= $presente ? 'bg-success' : 'bg-danger' ?>">
                <?= $presente ? 'Presente' : 'Ausente' ?>
            </span>
        </td>
        <td>
            <?php if ($presente) { ?>
                <a href="desmarcar.php?aluno=<?= $id_aluno ?>&atividade=<?= $id_atividade ?>&dia=<?= $data ?>" 
                   class="btn btn-sm btn-warning">
                    Desmarcar
                </a>
            <?php } else { ?>
                <a href="marcar.php?aluno=<?= $id_aluno ?>&atividade=<?= $id_atividade ?>&dia=<?= $data ?>" 
                   class="btn btn-sm btn-success">
                    Marcar
                </a>
            <?php } ?>
        </td>
    </tr>

<?php
    }
}
?>

</table>

<div class="mt-4 text-center">
    <a href="gerir_atividades.php" class="btn btn-secondary">
        ⬅ Voltar
    </a>
</div>

</div>

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>