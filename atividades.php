<?php
session_start();
include './basedados.h';
?>

<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Algazarra</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>

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
                if (isset($_SESSION['id_user'], $_SESSION['nivel']) && $_SESSION['nivel'] == 1) {
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

                // Encarregado / Pai
                if (isset($_SESSION['id_user'], $_SESSION['nivel']) && $_SESSION['nivel'] == 2) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPai" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownPai">';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Alunos</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }

                // Professor
                if (isset($_SESSION['id_user'], $_SESSION['nivel']) && $_SESSION['nivel'] == 3) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownProfessor" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownProfessor">';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Alunos</a></li>';
                    echo '<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
                ?>

            </ul>

            <ul class="navbar-nav ms-auto">
                <?php
                if (isset($_SESSION['id_user'], $_SESSION['nivel'])) {
                    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="dados.php">Dados pessoais</a></li>';
                } else {
                    echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
                }
                ?>
            </ul>

        </div>
    </div>
</nav>

<div class="container" style="padding:3vh;">

    <div class="text-center">
        <h1>Atividades</h1>
    </div>

    <div class="row justify-content-center mt-4">

        <?php
        $sql = 'SELECT * FROM atividade ORDER BY data_inicio';
        $atividades = mysqli_query($conn, $sql);

        if (mysqli_num_rows($atividades) > 0) {

            while ($atividade = mysqli_fetch_assoc($atividades)) {

                echo '
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card m-3" style="width: 20rem;">

                        <img src="' . htmlspecialchars($atividade['imagem']) . '" class="card-img-top" style="height:200px; object-fit:cover;">

                        <div class="card-body text-center">

                            <h5 class="card-title">' . htmlspecialchars($atividade['titulo']) . '</h5>

                            <p>
                                ' . date('d/m/Y', strtotime($atividade['data_inicio'])) . ' - ' . date('d/m/Y', strtotime($atividade['data_fim'])) . '
                            </p>

                            <a href="pag_atividades.php?id=' . htmlspecialchars($atividade['id']) . '" class="btn btn-custom w-100">
                                Saber mais
                            </a>

                        </div>

                    </div>
                </div>';
            }

        } else {
            echo '<h3 class="text-center mt-4">Não estão a decorrer atividades.</h3>';
        }
        ?>

    </div>
</div>

<?php include './rodape.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>

</body>
</html>