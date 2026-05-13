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

    $sql = "SELECT a.*, e.nome AS enc_nome
            FROM aluno a
            INNER JOIN enc_educacao e ON a.enc_educacao = e.id
            WHERE e.id = $id_user $where
            ORDER BY a.data_nascimento
            LIMIT $limite OFFSET $offset";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Erro na base de dados: " . mysqli_error($conn));
}

// CONTAR TOTAL
$sqlTotal = "SELECT COUNT(*) as total
             FROM aluno a
             LEFT JOIN enc_educacao e ON a.enc_educacao = e.id
             WHERE 1=1 $where";

if ($_SESSION['nivel'] != 1) {
    $sqlTotal .= " AND e.id = $id_user";
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
        <a class="navbar-brand" href="index.php">ALGAZARRA</a>
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

    $sqlAt = "SELECT atividade FROM inscricao 
              WHERE aluno = " . $aluno['id'] . " 
              LIMIT 1";

    $resAt = mysqli_query($conn, $sqlAt);
    $atividade = mysqli_fetch_assoc($resAt);

    if ($atividade) {

        echo '<a href="marcar_presencas.php?crianca='.$aluno['id'].'&atividade='.$atividade['atividade'].'&voltar='.$voltar.'" 
               class="btn btn-warning btn-sm">
               Ver presenças
              </a>';

    } else {

        echo '<span class="text-muted">Sem atividades</span>';
    }

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