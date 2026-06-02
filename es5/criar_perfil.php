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

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>