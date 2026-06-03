<?php
session_start();
include './basedados.h';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: atividades.php");
    exit;
}

$id_atividade = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM atividade WHERE id = ?");
$stmt->bind_param("i", $id_atividade);
$stmt->execute();

$retVal = $stmt->get_result();

if ($retVal->num_rows === 0) {
    header("Location: atividades.php");
    exit;
}

$dados = $retVal->fetch_assoc();

$titulo = $dados['titulo'];
$data_inicio = $dados['data_inicio'];
?>

<!DOCTYPE html>
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
                if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 1) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownAdmin">';
                    echo '<li><a class="dropdown-item" href="gerir_utilizadores.php">Gerir utilizadores</a></li>';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Crianças</a></li>';
                    echo '<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>';
                    echo '<li><a class="dropdown-item" href="criar_atividade.php">Criar atividade</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }

                if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPai" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownPai">';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Crianças</a></li>';
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

                echo '</li>';
                ?>
            </ul>

        </div>
    </div>
</nav>

<div class="container" style="padding: 3vh;">

    <h1 class="text-center">
        <?php echo htmlspecialchars($titulo); ?>
    </h1>

    <div class="row mt-4">

        <div class="col-md-6">
            <img class="img-fluid rounded" src="<?php echo htmlspecialchars($dados['imagem']); ?>" alt="atividade">
        </div>

        <div class="col-md-6">

            <h2>Diversão sem fim!</h2>

            <p><?php echo htmlspecialchars($dados['descricao']); ?></p>

            <h5>
                <strong>Data de início:</strong>
                <?php echo date('d/m/Y', strtotime($dados['data_inicio'])); ?>
            </h5>

            <h5>
                <strong>Data de fim:</strong>
                <?php echo date('d/m/Y', strtotime($dados['data_fim'])); ?>
            </h5>

            <h5>
                <strong>Lotação máxima:</strong>
                <?php echo htmlspecialchars($dados['lotacao_max']) . ' crianças'; ?>
            </h5>

        </div>
    </div>

<?php
if (date('Y-m-d') >= $data_inicio) {

    echo '<h2 class="text-center mt-5">Esta atividade já está a decorrer.</h2>';

} elseif (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2) {

    $id_user = (int) $_SESSION['id_user'];

    /*
     * Buscar o id do encarregado de educação associado ao utilizador logado.
     */
    $stmt_enc = $conn->prepare("
        SELECT id
        FROM enc_educacao
        WHERE email = (
            SELECT email
            FROM utilizador
            WHERE id = ?
        )
    ");

    $stmt_enc->bind_param("i", $id_user);
    $stmt_enc->execute();

    $res_enc = $stmt_enc->get_result();

    if ($res_enc && $res_enc->num_rows > 0) {

        $enc = $res_enc->fetch_assoc();
        $id_enc_educacao = (int) $enc['id'];

        /*
         * Buscar apenas os alunos deste encarregado que ainda não estão inscritos nesta atividade.
         */
        $stmt_alunos = $conn->prepare("
            SELECT id, nome
            FROM aluno
            WHERE enc_educacao = ?
            AND id NOT IN (
                SELECT aluno
                FROM inscricao
                WHERE atividade = ?
            )
            ORDER BY nome
        ");

        $stmt_alunos->bind_param("ii", $id_enc_educacao, $id_atividade);
        $stmt_alunos->execute();

        $alunos = $stmt_alunos->get_result();

        if ($alunos && $alunos->num_rows > 0) {

            echo '
            <h2 class="text-center mt-5">Está interessado/a?</h2>

            <div class="row justify-content-center mt-3">
                <div class="col-md-4">

                    <form action="inscrever.php" method="POST">

                        <input type="hidden" name="id_atividade" value="' . htmlspecialchars($id_atividade) . '">

                        <select name="id_crianca" class="form-select" required>';

            while ($aluno = $alunos->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($aluno['id']) . '">' . htmlspecialchars($aluno['nome']) . '</option>';
            }

            echo '
                        </select>

                        <button type="submit" class="btn mt-3 w-100" style="background-color:#FD7D21; color:white; font-weight:bold;">
                            Inscrever
                        </button>

                    </form>

                </div>
            </div>';

        } else {
            echo '<h2 class="text-center mt-5">Todos os seus alunos já estão inscritos nesta atividade.</h2>';
        }

    } else {
        echo '<h2 class="text-center mt-5">Encarregado de educação não encontrado.</h2>';
    }

} elseif (!isset($_SESSION['id_user'])) {

    echo '
    <div class="text-center mt-5">
        <h2>Quer participar?</h2>
        <a href="login.php" class="btn btn-primary mt-3">Faça login para inscrever</a>
    </div>';

}
?>

</div>

<?php include './rodape.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>

</body>
</html>