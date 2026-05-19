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

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Algazarra</title>
<link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color:#F5F5F5; margin:10vh 0;">

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
echo '<nav><ul class="pagination justify-content-center">';

for ($i = 1; $i <= $totalPaginas; $i++) {

    $active = ($i == $pagina) ? 'active' : '';

    echo "<li class='page-item $active'>
            <a class='page-link' href='?pagina=$i&nome=$filtro_nome&voltar=$voltar'>$i</a>
          </li>";
}

echo '</ul></nav>';
?>

<!-- BOTÃO VOLTAR -->
<div class="mt-4 text-center">
    <a href="<?php echo htmlspecialchars($voltar); ?>" class="btn btn-secondary">
        ⬅ Voltar
    </a>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include './rodape.php'; ?>
