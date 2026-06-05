<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user'], $_SESSION['nivel'])) {
    include './erro.php';
    exit;
}

$nivel = (int) $_SESSION['nivel'];

if ($nivel != 1 && $nivel != 3) {
    include './erro.php';
    exit;
}

// PAGINAÇÃO
$limite = 1;
$pagina = isset($_GET['pagina']) && ctype_digit($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$offset = ($pagina - 1) * $limite;

// FILTRO
$filtro = isset($_GET['nome']) ? trim($_GET['nome']) : '';

// TOTAL DE ATIVIDADES
if ($filtro !== '') {
    $pesquisa = '%' . $filtro . '%';

    $stmtTotal = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM atividade 
        WHERE titulo LIKE ?
    ");

    $stmtTotal->bind_param("s", $pesquisa);

} else {
    $stmtTotal = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM atividade
    ");
}

$stmtTotal->execute();
$resTotal = $stmtTotal->get_result();
$total = 0;

if ($resTotal && $resTotal->num_rows > 0) {
    $total = (int) $resTotal->fetch_assoc()['total'];
}

$totalPaginas = ceil($total / $limite);
$stmtTotal->close();

// QUERY PRINCIPAL DAS ATIVIDADES
if ($filtro !== '') {
    $pesquisa = '%' . $filtro . '%';

    $stmtAtividades = $conn->prepare("
        SELECT *
        FROM atividade
        WHERE titulo LIKE ?
        ORDER BY data_inicio DESC
        LIMIT ? OFFSET ?
    ");

    $stmtAtividades->bind_param("sii", $pesquisa, $limite, $offset);

} else {
    $stmtAtividades = $conn->prepare("
        SELECT *
        FROM atividade
        ORDER BY data_inicio DESC
        LIMIT ? OFFSET ?
    ");

    $stmtAtividades->bind_param("ii", $limite, $offset);
}

$stmtAtividades->execute();
$atividades = $stmtAtividades->get_result();

if (!$atividades) {
    die('Erro na BD');
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Algazarra</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>

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
                // Admin
                if (isset($_SESSION['id_user'], $_SESSION['nivel']) && $_SESSION['nivel'] == 1) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownAdmin">';
                    echo '<li><a class="dropdown-item" href="gerir_utilizadores.php">Gerir utilizadores</a></li>';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Crianças</a></li>';
                    echo '<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>';
                    echo '<li><a class="dropdown-item" href="criar_atividade.php">Criar atividade</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }

                // Encarregado
                if (isset($_SESSION['id_user'], $_SESSION['nivel']) && $_SESSION['nivel'] == 2) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPai" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownPai">';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Crianças</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }

                // Professor
                if (isset($_SESSION['id_user'], $_SESSION['nivel']) && $_SESSION['nivel'] == 3) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownProfessor" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownProfessor">';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Crianças</a></li>';
                    echo '<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
                ?>

            </ul>

            <ul class="navbar-nav ms-auto">
                <?php
                if (isset($_SESSION['id_user'], $_SESSION['nivel'])) {
                    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="dados.php">Dados pessoais</a></li>';
                } else {
                    echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
                }
                ?>
            </ul>

        </div>
    </div>
</nav>

<div class="container" style="padding:2vh;">

    <h1 class="text-center mb-3">Gerir atividades</h1>

    <form method="GET" class="row mb-3">

        <div class="col-md-4">
            <input type="text"
                   name="nome"
                   class="form-control"
                   placeholder="Pesquisar atividade"
                   value="<?php echo htmlspecialchars($filtro); ?>">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>

    </form>

    <?php while ($atividade = $atividades->fetch_assoc()) { ?>

        <?php
        $id_atividade = (int) $atividade['id'];

        $stmtInscricoes = $conn->prepare("
            SELECT 
                i.aluno,
                a.nome AS aluno_nome,
                u.nome AS encarregado_nome,
                u.telemovel
            FROM inscricao i
            INNER JOIN aluno a ON i.aluno = a.id
            INNER JOIN enc_educacao u ON a.enc_educacao = u.id
            WHERE i.atividade = ?
            GROUP BY i.aluno, a.nome, u.nome, u.telemovel
            ORDER BY a.nome
        ");

        $stmtInscricoes->bind_param("i", $id_atividade);
        $stmtInscricoes->execute();

        $inscricoes = $stmtInscricoes->get_result();

        if (!$inscricoes) {
            die('Erro na BD');
        }
        ?>

        <div class="row mb-2">

            <div class="<?php echo ($nivel == 1) ? 'col-9' : 'col-12'; ?>">
                <h4>
                    <?php echo htmlspecialchars($atividade['titulo']); ?> -
                    De <?php echo date('d/m/Y', strtotime($atividade['data_inicio'])); ?>
                    a <?php echo date('d/m/Y', strtotime($atividade['data_fim'])); ?>
                </h4>
            </div>

            <?php if ($nivel == 1) { ?>
                <div class="col-3 text-end">

                    <a class="btn btn-sm btn-primary"
                       href="editar_atividade.php?id=<?php echo htmlspecialchars($id_atividade); ?>">
                        Alterar
                    </a>

                    <a class="btn btn-sm btn-danger"
                       href="apagar_atividade.php?id=<?php echo htmlspecialchars($id_atividade); ?>"
                       onclick="return confirm('Tens a certeza?')">
                        Apagar
                    </a>

                </div>
            <?php } ?>

        </div>

        <table class="table mb-4">

            <thead>
                <tr>
                    <th style="background:#00d0ff;">Aluno</th>
                    <th style="background:#00d0ff;">Encarregado</th>
                    <th style="background:#00d0ff;">Contacto</th>
                    <th style="background:#00d0ff;">Presenças</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($inscricoes->num_rows > 0) { ?>

                <?php while ($row = $inscricoes->fetch_assoc()) { ?>

                    <tr>
                        <td><?php echo htmlspecialchars($row['aluno_nome']); ?></td>

                        <td><?php echo htmlspecialchars($row['encarregado_nome']); ?></td>

                        <td><?php echo htmlspecialchars($row['telemovel']); ?></td>

                        <td>
                            <a class="btn btn-warning btn-sm"
                               href="marcar_presencas.php?aluno=<?php echo htmlspecialchars($row['aluno']); ?>&atividade=<?php echo htmlspecialchars($id_atividade); ?>">
                                Marcar presenças
                            </a>
                        </td>
                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="4" class="text-muted text-center">
                        Sem alunos inscritos nesta atividade.
                    </td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

        <?php $stmtInscricoes->close(); ?>

    <?php } ?>

    <?php if ($totalPaginas > 1) { ?>

        <nav class="mt-3">
            <ul class="pagination justify-content-center">

                <?php for ($i = 1; $i <= $totalPaginas; $i++) { ?>

                    <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                        <a class="page-link"
                           href="gerir_atividades.php?pagina=<?php echo $i; ?>&nome=<?php echo urlencode($filtro); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>

                <?php } ?>

            </ul>
        </nav>

    <?php } ?>

    <div class="mt-4 text-center">
        <a href="index.php" class="btn btn-secondary">
            ⬅ Voltar
        </a>
    </div>

</div>

<?php include './rodape.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>

</body>
</html>