<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 2) {
    include './erro.php';
    exit;
}

// descobrir encarregado logado
$id_user = $_SESSION['id_user'];

$sql_enc = "SELECT id FROM enc_educacao WHERE email = (
    SELECT email FROM utilizador WHERE id = '$id_user'
)";
$res_enc = mysqli_query($conn, $sql_enc);

if (!$res_enc) {
    die("Erro BD: " . mysqli_error($conn));
}

$enc = mysqli_fetch_assoc($res_enc);
$enc_id = $enc['id'];
?>

<html lang="pt">
<head>
	<meta charset="UTF-8">
	<title>Algazarra</title>
	<link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color:#F5F5F5;margin:10vh 0;">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color:#08BDBD;">
<div class="container-fluid">
	<a class="navbar-brand" href="index.php">ALGAZARRA</a>
</div>
</nav>

<div class="container" style="padding:2vh;">

<?php
if (isset($_SESSION['err'])) {
    echo '<div class="alert alert-danger alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"
            onclick="window.location.href=\'criar_perfil.php\'"></button>
            <strong>' . $_SESSION['err'] . '</strong>
          </div>';
    unset($_SESSION['err']);
}
?>

<div class="card" style="background:#C5C6D0;border:none;padding:3vh;">
<div class="card-body">

<h1 class="text-center">Criar aluno</h1>

<form action="load_criar_perfil.php" method="POST">

    <input type="hidden" name="enc_educacao" value="<?php echo $enc_id; ?>">

    <div class="row">

        <div class="form-group col-md-6" style="padding-top:20px;">
            <label>Nome completo *</label>
            <input type="text" class="form-control" name="nome" required>
        </div>

        <div class="form-group col-md-6" style="padding-top:20px;">
            <label>Data de nascimento *</label>
            <input type="date" class="form-control" name="data_nascimento"
                   max="<?php echo date('Y-m-d'); ?>" required>
        </div>

    </div>

    <div style="text-align:right;margin-top:5vh;">
        <input type="submit" class="btn"
               style="background:#FD7D21;color:white;font-weight:bold;"
               value="Criar aluno">
    </div>

</form>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include './rodape.php'; ?>