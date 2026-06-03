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
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Alunos</a></li>';
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
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Alunos</a></li>';
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

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>