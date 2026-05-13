<?php
session_start();
$erro_msg = isset($_SESSION['erro']) ? $_SESSION['erro'] : (isset($_SESSION['info']) ? $_SESSION['info'] : 'Erro desconhecido. Tente novamente.');
unset($_SESSION['erro']);
unset($_SESSION['info']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Erro - Algazarra</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>
<body style="background:#F5F5F5; margin:10vh 0;">
<nav class="navbar fixed-top" style="background:#08BDBD;">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">ALGAZARRA</a>
    </div>
</nav>
<div class="container" style="padding:2vh;">
    <div class="card mx-auto" style="max-width:500px; background:#C5C6D0; border:none;">
        <div class="card-body text-center p-5">
            <h2 style="color:#dc3545;">❌ Ocorreu um erro</h2>
            <p class="lead"><?= htmlspecialchars($erro_msg) ?></p>
            <a href="javascript:history.back()" class="btn btn-warning">← Voltar</a>
            <a href="index.php" class="btn btn-primary ms-2">Página inicial</a>
        </div>
    </div>
</div>
<?php include 'rodape.php'; ?>
</body>
</html>
