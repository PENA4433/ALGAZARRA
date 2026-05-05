<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 1) {
    include './erro.php';
    exit;
}

// PAGINAÇÃO
$limite = 1;
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $limite;

// FILTRO
$filtro = isset($_GET['nome']) ? mysqli_real_escape_string($conn, $_GET['nome']) : '';
$where = "";

if (!empty($filtro)) {
    $where = "WHERE titulo LIKE '%$filtro%'";
}

// TOTAL
$sqlTotal = "SELECT COUNT(*) as total FROM atividade $where";
$resTotal = mysqli_query($conn, $sqlTotal);
$total = mysqli_fetch_assoc($resTotal)['total'];
$totalPaginas = ceil($total / $limite);

// QUERY PRINCIPAL
$sql = "SELECT * FROM atividade $where ORDER BY data_inicio DESC LIMIT $limite OFFSET $offset";
$atividades = mysqli_query($conn, $sql);

if (!$atividades) die('Erro na BD');
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

<div class="collapse navbar-collapse">
<ul class="navbar-nav me-auto">
<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
<li class="nav-item"><a class="nav-link" href="atividades.php">Atividades</a></li>
<li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>

<li class="nav-item dropdown">
<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Gestão</a>
<ul class="dropdown-menu">
<li><a class="dropdown-item" href="gerir_utilizadores.php">Gerir utilizadores</a></li>
<li><a class="dropdown-item" href="gerir_alunos.php">Gerir alunos</a></li>
<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>
<li><a class="dropdown-item" href="criar_atividade.php">Criar atividade</a></li>
</ul>
</li>
</ul>

<ul class="navbar-nav ms-auto">
<li class="nav-item"><a class="nav-link" href="dados.php">Dados pessoais</a></li>
<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
</ul>
</div>
</div>
</nav>

<div class="container" style="padding:2vh;">

<h1 class="text-center mb-3">Gerir atividades</h1>

<!-- FILTRO -->
<form method="GET" class="row mb-3">
<div class="col-md-4">
<input type="text" name="nome" class="form-control"
placeholder="Pesquisar atividade"
value="<?php echo htmlspecialchars($filtro); ?>">
</div>

<div class="col-md-2">
<button type="submit" class="btn btn-primary">Filtrar</button>
</div>
</form>

<?php
while ($atividade = mysqli_fetch_assoc($atividades)) {

$id_atividade = $atividade['id'];

$sql = "
SELECT 
i.aluno,
a.nome AS aluno_nome,
u.nome AS encarregado_nome,
u.telemovel
FROM inscricao i
INNER JOIN aluno a ON i.aluno = a.id
INNER JOIN enc_educacao u ON a.enc_educacao = u.id
WHERE i.atividade = '$id_atividade'
ORDER BY a.nome
";

$inscricoes = mysqli_query($conn, $sql);
if (!$inscricoes) die('Erro na BD');
?>

<!-- ATIVIDADE -->
<div class="row mb-2">
<div class="col-9">
<h4>
<?= $atividade['titulo'] ?> -
De <?= date('d/m/Y', strtotime($atividade['data_inicio'])) ?>
a <?= date('d/m/Y', strtotime($atividade['data_fim'])) ?>
</h4>
</div>

<div class="col-3 text-end">
<a class="btn btn-sm btn-primary"
href="editar_atividade.php?id=<?= $id_atividade ?>">
Alterar
</a>

<a class="btn btn-sm btn-danger"
href="apagar_atividade.php?id=<?= $id_atividade ?>"
onclick="return confirm('Tens a certeza?')">
Apagar
</a>
</div>
</div>

<table class="table mb-4">
<thead>
<tr>
<th style="background:#00d0ff;">Aluno</th>
<th style="background:#00d0ff;">Encarregado</th>
<th style="background:#00d0ff;">Contacto</th>
<th style="background:#00d0ff;"></th>
</tr>
</thead>

<?php while ($row = mysqli_fetch_assoc($inscricoes)) { ?>
<tr>
<td><?= $row['aluno_nome'] ?></td>
<td><?= $row['encarregado_nome'] ?></td>
<td><?= $row['telemovel'] ?></td>
<td>
<a class="text-danger fw-bold"
href="marcar_presencas.php?aluno=<?= $row['aluno'] ?>&atividade=<?= $id_atividade ?>">
Marcar presenças
</a>
</td>
</tr>
<?php } ?>

</table>

<?php } ?>

<!-- PAGINAÇÃO -->
<nav>
<ul class="pagination justify-content-center">

<?php for ($i = 1; $i <= $totalPaginas; $i++) {
$active = ($i == $pagina) ? 'active' : '';
echo "<li class='page-item $active'>
<a class='page-link' href='?pagina=$i&nome=$filtro'>$i</a>
</li>";
} ?>

</ul>
</nav>

<!-- BOTÃO VOLTAR -->
<div class="mt-4 text-center">
<a href="index.php" class="btn btn-secondary">
⬅ Voltar
</a>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include './rodape.php'; ?>