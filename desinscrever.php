<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id_crianca'])) {
    header("Location: gerir_criancas.php");
    exit();
}

$id_crianca = intval($_GET['id_crianca']);

// Buscar dados da criança
$sql_crianca = "SELECT nome FROM aluno WHERE id_aluno = $id_crianca";
$resultado_crianca = mysqli_query($conn, $sql_crianca);

if (!$resultado_crianca || mysqli_num_rows($resultado_crianca) == 0) {
    header("Location: gerir_criancas.php");
    exit();
}

$crianca = mysqli_fetch_assoc($resultado_crianca);

// Buscar atividades onde a criança está inscrita
$sql = "SELECT atividade.id_atividade, atividade.nome
        FROM atividade
        INNER JOIN inscricao 
        ON atividade.id_atividade = inscricao.id_atividade
        WHERE inscricao.id_aluno = $id_crianca";

$resultado = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Desinscrever Criança</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body style="background-color: #dfe7f2;">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff; border: none;" data-bs-theme="light">
    <div class="container-fluid">
        <div class="collapse navbar-collapse justify-content-center">
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
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Sair</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dados.php">Dados pessoais</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container" style="padding-top: 130px;">
    <h1 class="text-center mb-4">Desinscrever Criança</h1>

    <h4 class="text-center mb-4">
        Criança: <?php echo htmlspecialchars($crianca['nome']); ?>
    </h4>

    <div class="card mx-auto" style="max-width: 900px;">
        <div class="card-body">

            <?php if ($resultado && mysqli_num_rows($resultado) > 0) { ?>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Atividade</th>
                            <th>Ação</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($atividade = mysqli_fetch_assoc($resultado)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($atividade['nome']); ?></td>

                                <td>
                                    <a 
                                        href="load_desinscrever.php?id_crianca=<?php echo $id_crianca; ?>&id_atividade=<?php echo $atividade['id_atividade']; ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Tem a certeza que deseja desinscrever esta criança desta atividade?');"
                                    >
                                        Desinscrever
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            <?php } else { ?>

                <div class="alert alert-warning text-center">
                    Esta criança não está inscrita em nenhuma atividade.
                </div>

            <?php } ?>

            <div class="text-center mt-4">
                <a href="gerir_criancas.php" class="btn btn-secondary">
                    ← Voltar
                </a>
            </div>

        </div>
    </div>
</div>

<?php include './rodape.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>