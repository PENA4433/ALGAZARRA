<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user'], $_SESSION['nivel'])) {
    include './erro.php';
    exit;
}

/* validar aluno */
if (!isset($_GET['id'])) {
    die("Aluno inválido");
}

$id_crianca = intval($_GET['id']);

/* se clicar em inscrever */
if (isset($_POST['id_atividade'])) {

    $id_atividade = intval($_POST['id_atividade']);

    /* verificar duplicado */
    $check = "SELECT * FROM inscricao 
              WHERE aluno = $id_crianca 
              AND atividade = $id_atividade";

    $res = mysqli_query($conn, $check);

    if (mysqli_num_rows($res) > 0) {
        $_SESSION['err'] = "Já está inscrito nesta atividade.";
        header("Location: gerir_criancas.php");
        exit;
    }

    /* inserir inscrição */
    $sql = "INSERT INTO inscricao (aluno, atividade, dia, esta_presente)
            VALUES ($id_crianca, $id_atividade, CURDATE(), 0)";

    mysqli_query($conn, $sql);

    $_SESSION['info'] = "Inscrição feita com sucesso.";
    header("Location: gerir_criancas.php");
    exit;
}

/* listar atividades */
$sql = "SELECT * FROM atividade ORDER BY data_inicio";
$result = mysqli_query($conn, $sql);
?>

    <meta charset="UTF-8">
    <title>Inscrever criança</title>
    <link rel="stylesheet" href="bootstrap.min.css">


<div class="container">

<h2 class="text-center">Escolher atividade</h2>

<?php while ($atividade = mysqli_fetch_assoc($result)) { ?>

    <div class="card mt-3 p-3">

        <h5><?php echo htmlspecialchars($atividade['titulo']); ?></h5>

        <p>
            De <?php echo date('d/m/Y', strtotime($atividade['data_inicio'])); ?>
            até <?php echo date('d/m/Y', strtotime($atividade['data_fim'])); ?>
        </p>

        <form method="post">

            <input type="hidden" name="id_atividade"
                   value="<?php echo $atividade['id']; ?>">

            <button type="submit" class="btn btn-success btn-sm">
                Inscrever nesta atividade
            </button>

        </form>

    </div>

<?php } ?>

<!-- voltar -->
<div class="mt-3">
    <a href="gerir_criancas.php" class="btn btn-secondary">
        Voltar
    </a>
</div>

</div>

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>