<?php
session_start();
include './basedados.h';

if (!isset($_POST['id'], $_POST['nome'], $_POST['data_nascimento'], $_POST['telemovel'], $_POST['email'], $_POST['user'], $_POST['pwd'])) {
    $_SESSION['err'] = 'Dados inválidos.';
    header("Location: alterar_dados.php");
    exit;
}

$id_utilizador = intval($_POST['id']);

$nome = trim($_POST['nome']);
$data_nascimento = $_POST['data_nascimento'];
$telemovel = trim($_POST['telemovel']);
$email = trim($_POST['email']);
$user = trim($_POST['user']);
$pwd = md5($_POST['pwd']); // (ideal: password_hash, mas mantenho igual ao teu sistema atual)

if ($nome === '' || $data_nascimento === '' || $telemovel === '' || $email === '' || $user === '' || $pwd === '') {
    $_SESSION['err'] = 'Não preencheu campos obrigatórios.';
    header("Location: alterar_dados.php?id=$id_utilizador");
    exit;
}

$stmt = $conn->prepare("
    UPDATE utilizador
    SET nome = ?, 
        data_nascimento = ?, 
        telemovel = ?, 
        email = ?, 
        user = ?, 
        pwd = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssssssi",
    $nome,
    $data_nascimento,
    $telemovel,
    $email,
    $user,
    $pwd,
    $id_utilizador
);

if ($stmt->execute()) {
    $_SESSION['info'] = 'Dados atualizados com sucesso!';
    header("Location: dados.php");
    exit;
} else {
    die("Erro SQL: " . $conn->error);
}
?>

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>