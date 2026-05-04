<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || !isset($_SESSION['nivel'])) {
    include './erro.php';
    exit;
}

$id_aluno = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_aluno) {
    die("ID inválido");
}

$stmt = $conn->prepare("SELECT * FROM aluno WHERE id = ?");
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();

if (!$dados) {
    header("Location: erro.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Algazarra</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color: #F5F5F5; margin:10vh 0;">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff;">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">ALGAZARRA</a>
    </div>
</nav>

<div class="container" style="padding:2vh;">

<?php if (isset($_SESSION['info'])): ?>
    <div class="alert alert-info alert-dismissible">
        <button type="button" class="btn-close" data-bs-dismiss="alert"
        onclick="window.location.href='alterar_dados_crianca.php?id=<?php echo $id_aluno; ?>'"></button>
        <strong><?php echo $_SESSION['info']; ?></strong>
    </div>
    <?php unset($_SESSION['info']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['err'])): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="btn-close" data-bs-dismiss="alert"
        onclick="window.location.href='alterar_dados_crianca.php?id=<?php echo $id_aluno; ?>'"></button>
        <strong><?php echo $_SESSION['err']; ?></strong>
    </div>
    <?php unset($_SESSION['err']); ?>
<?php endif; ?>

<div class="card" style="background-color: #C5C6D0; border:none; padding:3vh;">
<div class="card-body">

<h1 class="text-center">Alterar dados do aluno</h1>

<form action="load_alterar_dados_crianca.php" method="post">

    <input type="hidden" name="id" value="<?php echo $id_aluno; ?>">

    <div class="row">

        <div class="form-group col-md-6" style="padding-top:20px;">
            <label>Nome completo *</label>
            <input type="text" class="form-control" name="nome"
                   value="<?php echo htmlspecialchars($dados['nome']); ?>" required>
        </div>

        <div class="form-group col-md-6" style="padding-top:20px;">
            <label>Data de nascimento *</label>
            <input type="date" class="form-control"
                   name="data_nascimento"
                   value="<?php echo $dados['data_nascimento']; ?>"
                   max="<?php echo date('Y-m-d'); ?>"
                   required>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12 d-flex justify-content-end" style="padding-top:20px;">
            <input type="submit" class="btn"
                   style="background-color: #fd2121ff;color:white;font-weight:bold;"
                   value="Submeter">
        </div>
    </div>

</form>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include './rodape.php'; ?>