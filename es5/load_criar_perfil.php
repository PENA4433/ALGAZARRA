<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 2) {
    include './erro.php';
    exit;
}

if (empty($_POST['nome']) || empty($_POST['data_nascimento']) || empty($_POST['enc_educacao'])) {
    $_SESSION['err'] = 'Não preencheu campos obrigatórios.';
    header("Location: criar_aluno.php");
    exit;
}

$nome = mysqli_real_escape_string($conn, $_POST['nome']);
$data_nascimento = $_POST['data_nascimento'];
$enc_educacao = intval($_POST['enc_educacao']);

$sql = "INSERT INTO aluno (nome, data_nascimento, enc_educacao)
        VALUES ('$nome', '$data_nascimento', $enc_educacao)";

$retVal = mysqli_query($conn, $sql);

if (!$retVal) {
    die('Erro BD: ' . mysqli_error($conn));
}

$_SESSION['info'] = "Aluno $nome criado com sucesso.";

// 🔥 AQUI ESTAVA O PROBLEMA PRINCIPAL
// garante que este ficheiro EXISTE mesmo
header("Location: gerir_criancas.php"); 
exit;
?>

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>