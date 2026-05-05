<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 1) {
    include './erro.h';
    exit();
}

$id_user = $_SESSION['id_user'];


// =========================
// PAGINAÇÃO
// =========================
$limite = 4;
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $limite;


// =========================
// FILTRO
// =========================
$filtro = isset($_GET['nome']) ? mysqli_real_escape_string($conn, $_GET['nome']) : '';
$where = "WHERE id != '$id_user'";

if (!empty($filtro)) {
    $where .= " AND (nome LIKE '%$filtro%' OR email LIKE '%$filtro%')";
}


// =========================
// TOTAL PARA PAGINAÇÃO
// =========================
$sqlTotal = "SELECT COUNT(*) as total FROM utilizador $where";
$resTotal = mysqli_query($conn, $sqlTotal);
$total = mysqli_fetch_assoc($resTotal)['total'];
$totalPaginas = ceil($total / $limite);


// =========================
// APAGAR UTILIZADOR
// =========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apagar_id'])) {

    $id = intval($_POST['apagar_id']);

    if ($id != $id_user) {
        mysqli_query($conn, "DELETE FROM utilizador WHERE id = $id");
    }

    header("Location: gerir_utilizadores.php");
    exit();
}


// =========================
// EDITAR UTILIZADOR
// =========================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_id'])) {

    $id = intval($_POST['editar_id']);

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telemovel = $_POST['telemovel'];
    $data_nascimento = $_POST['data_nascimento'];

    $sql = "UPDATE utilizador SET 
            nome='$nome',
            email='$email',
            telemovel='$telemovel',
            data_nascimento='$data_nascimento'
            WHERE id=$id";

    mysqli_query($conn, $sql);

    header("Location: gerir_utilizadores.php");
    exit();
}


// =========================
// CARREGAR PARA EDITAR
// =========================
$utilizador_editar = null;

if (isset($_GET['editar'])) {

    $id_edit = intval($_GET['editar']);

    $res = mysqli_query($conn, "SELECT * FROM utilizador WHERE id=$id_edit");
    $utilizador_editar = mysqli_fetch_assoc($res);
}


// =========================
// LISTA UTILIZADORES
// =========================
$sql = "SELECT * FROM utilizador $where ORDER BY nome LIMIT $limite OFFSET $offset";
$res = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Algazarra</title>
<link rel="stylesheet" href="bootstrap.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #F5F5F5;
    margin: 10vh 0;
}

.btn-alterar {
    background-color: #1f3a93;
    color: white;
    font-weight: 600;
    border: none;
}

.btn-apagar {
    background-color: #dc3545;
    color: white;
    font-weight: 600;
    border: none;
}

.card {
    border-radius: 15px;
}

.navbar {
    background-color: #00d0ff !important;
}
</style>
</head>

<body>

<nav class="fixed-top navbar navbar-expand-lg">
<div class="container-fluid">
<a class="navbar-brand" href="index.php">ALGAZARRA</a>
</div>
</nav>

<div class="container">

<h1 class="text-center mb-4">Gerir utilizadores</h1>

<!-- FILTRO -->
<form method="GET" class="row mb-3 justify-content-center">
<div class="col-md-4">
<input type="text" name="nome" class="form-control"
placeholder="Pesquisar nome ou email"
value="<?php echo htmlspecialchars($filtro); ?>">
</div>

<div class="col-md-2">
<button type="submit" class="btn btn-primary">Filtrar</button>
</div>
</form>


<!-- EDITAR -->
<?php if ($utilizador_editar): ?>

<div class="card p-4 mb-4 shadow-sm">

<h4>Editar utilizador</h4>

<form method="POST">

<input type="hidden" name="editar_id" value="<?= $utilizador_editar['id'] ?>">

<input type="text" name="nome" class="form-control mb-2"
value="<?= $utilizador_editar['nome'] ?>" required>

<input type="email" name="email" class="form-control mb-2"
value="<?= $utilizador_editar['email'] ?>" required>

<input type="text" name="telemovel" class="form-control mb-2"
value="<?= $utilizador_editar['telemovel'] ?>" required>

<input type="date" name="data_nascimento" class="form-control mb-2"
value="<?= $utilizador_editar['data_nascimento'] ?>" required>

<button type="submit" class="btn btn-primary">Guardar</button>

<a href="gerir_utilizadores.php" class="btn btn-secondary">Cancelar</a>

</form>

</div>

<?php endif; ?>


<!-- LISTA -->
<div class="row justify-content-center">

<?php while ($u = mysqli_fetch_assoc($res)) { ?>

<div class="col-md-3">

<div class="card my-3 shadow-sm">

<div class="card-body">

<h5><?= $u['nome'] ?></h5>
<p><?= $u['email'] ?></p>
<p><?= $u['telemovel'] ?></p>

<a href="gerir_utilizadores.php?editar=<?= $u['id'] ?>"
class="btn btn-alterar me-2">
Alterar
</a>

<form method="POST" style="margin-top:10px;">
<input type="hidden" name="apagar_id" value="<?= $u['id'] ?>">
<button type="submit" class="btn btn-apagar">Apagar</button>
</form>

</div>

</div>

</div>

<?php } ?>

</div>


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
<div class="text-center mt-4">
<a href="index.php" class="btn btn-secondary">
⬅ Voltar
</a>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>