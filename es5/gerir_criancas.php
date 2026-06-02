<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user'], $_SESSION['nivel'])) {
    include './erro.php';
    exit;
}

$id_user = intval($_SESSION['id_user']);

// PAGINAÇÃO
$limite = 3;
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $limite;

// FILTRO
$filtro_nome = isset($_GET['nome']) ? mysqli_real_escape_string($conn, $_GET['nome']) : '';
$where = "";

if (!empty($filtro_nome)) {
    $where .= " AND a.nome LIKE '%$filtro_nome%'";
}

// QUERY PRINCIPAL
if ($_SESSION['nivel'] == 1) {

    $sql = "SELECT a.*, e.nome AS enc_nome
            FROM aluno a
            LEFT JOIN enc_educacao e ON a.enc_educacao = e.id
            WHERE 1=1 $where
            ORDER BY a.nome
            LIMIT $limite OFFSET $offset";

} else {

    // Para encarregados: encontrar o enc_educacao.id correspondente ao utilizador
    $stmt_enc = mysqli_prepare($conn, "SELECT id FROM enc_educacao WHERE email = (SELECT email FROM utilizador WHERE id = ?)");
    mysqli_stmt_bind_param($stmt_enc, "i", $id_user);
    mysqli_stmt_execute($stmt_enc);
    $res_enc = mysqli_stmt_get_result($stmt_enc);
    
    if (!$res_enc || mysqli_num_rows($res_enc) == 0) {
        // Se não encontrar encarregado, mostrar erro
        $_SESSION['erro'] = 'Encarregado não encontrado na base de dados.';
        header('Location: erro.php');
        exit;
    }
    
    $enc_data = mysqli_fetch_assoc($res_enc);
    $enc_id = $enc_data['id'];
    mysqli_stmt_close($stmt_enc);

    $sql = "SELECT a.*, e.nome AS enc_nome
            FROM aluno a
            INNER JOIN enc_educacao e ON a.enc_educacao = e.id
            WHERE e.id = $enc_id $where
            ORDER BY a.data_nascimento
            LIMIT $limite OFFSET $offset";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Erro na base de dados: " . mysqli_error($conn));
}

// CONTAR TOTAL
if ($_SESSION['nivel'] == 1) {
    $sqlTotal = "SELECT COUNT(*) as total
                 FROM aluno a
                 LEFT JOIN enc_educacao e ON a.enc_educacao = e.id
                 WHERE 1=1 $where";
} else {
    $sqlTotal = "SELECT COUNT(*) as total
                 FROM aluno a
                 INNER JOIN enc_educacao e ON a.enc_educacao = e.id
                 WHERE e.id = $enc_id $where";
}

$resTotal = mysqli_query($conn, $sqlTotal);
$total = mysqli_fetch_assoc($resTotal)['total'];
$totalPaginas = ceil($total / $limite);

// DEFINIR PÁGINA DE VOLTAR
$voltar = isset($_GET['voltar']) ? $_GET['voltar'] : 'index.php';
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
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>';
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
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>';
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

<h1 class="text-center">Gerir crianças</h1>

<!-- FILTRO -->
<form method="GET" class="row mb-3">
    <input type="hidden" name="voltar" value="<?php echo htmlspecialchars($voltar); ?>">
    
    <div class="col-md-4">
        <input type="text" name="nome" class="form-control" 
               placeholder="Pesquisar nome"
               value="<?php echo htmlspecialchars($filtro_nome); ?>">
    </div>

    <div class="col-md-2">
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </div>
</form>

<?php
echo '<table class="table mt-4">';

echo '<tr style="background:#00d0ff;">
        <th>Nome</th>
        <th>Data nascimento</th>';

if ($_SESSION['nivel'] == 1) {
    echo '<th>Encarregado</th>';
}

echo '<th>Alterar</th>
      <th>Atividades</th>
      <th>Inscrever</th>
      <th>Presenças</th>
      </tr>';

while ($aluno = mysqli_fetch_assoc($result)) {

    echo '<tr>
            <td>' . htmlspecialchars($aluno['nome']) . '</td>
            <td>' . date('d/m/Y', strtotime($aluno['data_nascimento'])) . '</td>';

    if ($_SESSION['nivel'] == 1) {
        echo '<td>' . htmlspecialchars($aluno['enc_nome']) . '</td>';
    }

    echo '<td>
            <a href="alterar_dados_crianca.php?id=' . $aluno['id'] . '&voltar='.$voltar.'">Alterar</a>
          </td>';

    echo '<td>
            <a href="atividades_crianca.php?id=' . $aluno['id'] . '&voltar='.$voltar.'">Atividades</a>
          </td>';

    echo '<td>
            <a href="inscrever.php?id=' . $aluno['id'] . '&voltar='.$voltar.'" 
               class="btn btn-success btn-sm">
                Inscrever
            </a>
          </td>';

    echo '<td>';

    // Listar todas as atividades do aluno para permitir escolha
    $stmt_at = mysqli_prepare($conn, "SELECT DISTINCT atividade FROM inscricao WHERE aluno = ? ORDER BY atividade");
    mysqli_stmt_bind_param($stmt_at, "i", $aluno['id']);
    mysqli_stmt_execute($stmt_at);
    $res_at = mysqli_stmt_get_result($stmt_at);

    if ($res_at && mysqli_num_rows($res_at) > 0) {
        // Se houver apenas uma atividade, ir direto
        if (mysqli_num_rows($res_at) == 1) {
            $atividade = mysqli_fetch_assoc($res_at);
            echo '<a href="ver_presencas.php?aluno='.$aluno['id'].'&atividade='.$atividade['atividade'].'&voltar='.$voltar.'" 
                   class="btn btn-warning btn-sm">
                   Ver presenças
                  </a>';
        } else {
            // Se houver várias, mostrar dropdown ou link para ver todas
            echo '<a href="ver_presencas.php?aluno='.$aluno['id'].'&voltar='.$voltar.'" 
                   class="btn btn-warning btn-sm">
                   Ver presenças
                  </a>';
        }
    } else {
        echo '<span class="text-muted">Sem atividades</span>';
    }

    mysqli_stmt_close($stmt_at);
    echo '</td>';
    echo '</tr>';
}

echo '</table>';

// PAGINAÇÃO
echo '';
?>

<!-- BOTÃO VOLTAR -->
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