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

<!-- BOTÃO VOLTAR -->
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