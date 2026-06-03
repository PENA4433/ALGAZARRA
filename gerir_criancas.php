<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user'], $_SESSION['nivel'])) {
    include './erro.php';
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$nivel = (int) $_SESSION['nivel'];

// PAGINAÇÃO
$limite = 3;
$pagina = isset($_GET['pagina']) && ctype_digit($_GET['pagina']) ? (int) $_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$offset = ($pagina - 1) * $limite;

// FILTRO
$filtro_nome = isset($_GET['nome']) ? trim($_GET['nome']) : '';

// DEFINIR PÁGINA DE VOLTAR
$voltar = isset($_GET['voltar']) ? $_GET['voltar'] : 'index.php';

// BUSCAR ENCARREGADO SE FOR NÍVEL 2
$enc_id = null;

if ($nivel == 2) {

    $stmt_enc = $conn->prepare("
        SELECT id 
        FROM enc_educacao 
        WHERE email = (
            SELECT email 
            FROM utilizador 
            WHERE id = ?
        )
    ");

    $stmt_enc->bind_param("i", $id_user);
    $stmt_enc->execute();

    $res_enc = $stmt_enc->get_result();

    if (!$res_enc || $res_enc->num_rows === 0) {
        $_SESSION['erro'] = 'Encarregado não encontrado na base de dados.';
        header('Location: erro.php');
        exit;
    }

    $enc_data = $res_enc->fetch_assoc();
    $enc_id = (int) $enc_data['id'];

    $stmt_enc->close();
}

// QUERY PRINCIPAL
if ($nivel == 1) {

    if ($filtro_nome !== '') {

        $pesquisa = '%' . $filtro_nome . '%';

        $stmt = $conn->prepare("
            SELECT a.*, e.nome AS enc_nome
            FROM aluno a
            LEFT JOIN enc_educacao e ON a.enc_educacao = e.id
            WHERE a.nome LIKE ?
            ORDER BY a.nome
            LIMIT ? OFFSET ?
        ");

        $stmt->bind_param("sii", $pesquisa, $limite, $offset);

    } else {

        $stmt = $conn->prepare("
            SELECT a.*, e.nome AS enc_nome
            FROM aluno a
            LEFT JOIN enc_educacao e ON a.enc_educacao = e.id
            ORDER BY a.nome
            LIMIT ? OFFSET ?
        ");

        $stmt->bind_param("ii", $limite, $offset);
    }

} elseif ($nivel == 2) {

    if ($filtro_nome !== '') {

        $pesquisa = '%' . $filtro_nome . '%';

        $stmt = $conn->prepare("
            SELECT a.*, e.nome AS enc_nome
            FROM aluno a
            INNER JOIN enc_educacao e ON a.enc_educacao = e.id
            WHERE a.enc_educacao = ?
            AND a.nome LIKE ?
            ORDER BY a.nome
            LIMIT ? OFFSET ?
        ");

        $stmt->bind_param("isii", $enc_id, $pesquisa, $limite, $offset);

    } else {

        $stmt = $conn->prepare("
            SELECT a.*, e.nome AS enc_nome
            FROM aluno a
            INNER JOIN enc_educacao e ON a.enc_educacao = e.id
            WHERE a.enc_educacao = ?
            ORDER BY a.nome
            LIMIT ? OFFSET ?
        ");

        $stmt->bind_param("iii", $enc_id, $limite, $offset);
    }

} else {
    header("Location: index.php");
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Erro na base de dados.");
}

// CONTAR TOTAL
if ($nivel == 1) {

    if ($filtro_nome !== '') {

        $pesquisa = '%' . $filtro_nome . '%';

        $stmt_total = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM aluno a
            LEFT JOIN enc_educacao e ON a.enc_educacao = e.id
            WHERE a.nome LIKE ?
        ");

        $stmt_total->bind_param("s", $pesquisa);

    } else {

        $stmt_total = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM aluno a
            LEFT JOIN enc_educacao e ON a.enc_educacao = e.id
        ");
    }

} else {

    if ($filtro_nome !== '') {

        $pesquisa = '%' . $filtro_nome . '%';

        $stmt_total = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM aluno a
            INNER JOIN enc_educacao e ON a.enc_educacao = e.id
            WHERE a.enc_educacao = ?
            AND a.nome LIKE ?
        ");

        $stmt_total->bind_param("is", $enc_id, $pesquisa);

    } else {

        $stmt_total = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM aluno a
            INNER JOIN enc_educacao e ON a.enc_educacao = e.id
            WHERE a.enc_educacao = ?
        ");

        $stmt_total->bind_param("i", $enc_id);
    }
}

$stmt_total->execute();
$resTotal = $stmt_total->get_result();

$total = 0;

if ($resTotal && $resTotal->num_rows > 0) {
    $total = (int) $resTotal->fetch_assoc()['total'];
}

$totalPaginas = ceil($total / $limite);
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
                if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 1) {
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

                if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPai" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownPai">';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Crianças</a></li>';
                    echo '<li><a class="dropdown-item" href="criar_perfil.php">Inscrever criança</a></li>';
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

                echo '</li>';
                ?>
            </ul>

        </div>
    </div>
</nav>

<div class="container" style="padding:2vh;">

    <h1 class="text-center">Gerir Crianças</h1>

    <form method="GET" class="row mb-3">

        <input type="hidden" name="voltar" value="<?php echo htmlspecialchars($voltar); ?>">

        <div class="col-md-4">
            <input type="text"
                   name="nome"
                   class="form-control"
                   placeholder="Pesquisar nome"
                   value="<?php echo htmlspecialchars($filtro_nome); ?>">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>

    </form>

    <table class="table mt-4">

        <tr style="background:#00d0ff;">
            <th>Nome</th>
            <th>Data nascimento</th>

            <?php if ($nivel == 1) { ?>
                <th>Encarregado</th>
            <?php } ?>

            <th>Alterar</th>
            <th>Atividades</th>
            <th>Inscrever</th>
            <th>Desinscrever</th>
            <th>Presenças</th>
        </tr>

        <?php while ($aluno = $result->fetch_assoc()) { ?>

            <tr>
                <td><?php echo htmlspecialchars($aluno['nome']); ?></td>

                <td>
                    <?php echo date('d/m/Y', strtotime($aluno['data_nascimento'])); ?>
                </td>

                <?php if ($nivel == 1) { ?>
                    <td><?php echo htmlspecialchars($aluno['enc_nome']); ?></td>
                <?php } ?>

                <td>
                    <a href="alterar_dados_crianca.php?id=<?php echo htmlspecialchars($aluno['id']); ?>&voltar=<?php echo urlencode($voltar); ?>">
                        Alterar
                    </a>
                </td>

                <td>
                    <a href="atividades_crianca.php?id=<?php echo htmlspecialchars($aluno['id']); ?>&voltar=<?php echo urlencode($voltar); ?>">
                        Atividades
                    </a>
                </td>

                <td>
                    <a href="inscrever.php?id=<?php echo htmlspecialchars($aluno['id']); ?>&voltar=<?php echo urlencode($voltar); ?>"
                       class="btn btn-success btn-sm">
                        Inscrever
                    </a>
                </td>

                <td>
                    <a href="desinscrever.php?id_crianca=<?php echo htmlspecialchars($aluno['id']); ?>&voltar=<?php echo urlencode($voltar); ?>"
                       class="btn btn-danger btn-sm">
                        Desinscrever
                    </a>
                </td>

                <td>
                    <?php
                    $stmt_at = $conn->prepare("
                        SELECT DISTINCT atividade 
                        FROM inscricao 
                        WHERE aluno = ? 
                        ORDER BY atividade
                    ");

                    $id_aluno = (int) $aluno['id'];

                    $stmt_at->bind_param("i", $id_aluno);
                    $stmt_at->execute();

                    $res_at = $stmt_at->get_result();

                    if ($res_at && $res_at->num_rows > 0) {

                        if ($res_at->num_rows == 1) {
                            $atividade = $res_at->fetch_assoc();

                            echo '<a href="ver_presencas.php?aluno=' . htmlspecialchars($aluno['id']) . '&atividade=' . htmlspecialchars($atividade['atividade']) . '&voltar=' . urlencode($voltar) . '" class="btn btn-warning btn-sm">
                                    Ver presenças
                                  </a>';
                        } else {
                            echo '<a href="ver_presencas.php?aluno=' . htmlspecialchars($aluno['id']) . '&voltar=' . urlencode($voltar) . '" class="btn btn-warning btn-sm">
                                    Ver presenças
                                  </a>';
                        }

                    } else {
                        echo '<span class="text-muted">Sem atividades</span>';
                    }

                    $stmt_at->close();
                    ?>
                </td>
            </tr>

        <?php } ?>

    </table>

    <?php if ($totalPaginas > 1) { ?>

        <nav class="mt-3">
            <ul class="pagination justify-content-center">

                <?php for ($i = 1; $i <= $totalPaginas; $i++) { ?>

                    <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                        <a class="page-link"
                           href="gerir_criancas.php?pagina=<?php echo $i; ?>&nome=<?php echo urlencode($filtro_nome); ?>&voltar=<?php echo urlencode($voltar); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>

                <?php } ?>

            </ul>
        </nav>

    <?php } ?>

    <div class="mt-4 text-center">
        <a href="<?php echo htmlspecialchars($voltar); ?>" class="btn btn-secondary">
            ⬅ Voltar
        </a>
    </div>

</div>

<?php include './rodape.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>

</body>
</html>