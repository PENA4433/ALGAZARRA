<?php
session_start();
include './basedados.h';

// Se já estiver logado, bloqueia acesso ao registo
if (isset($_SESSION['id_user']) || isset($_SESSION['nivel'])) {
    include './erro.h';
    exit;
}

// Validar campos obrigatórios
if (
    empty($_POST['nome']) ||
    empty($_POST['data_nascimento']) ||
    empty($_POST['telemovel']) ||
    empty($_POST['email']) ||
    empty($_POST['user']) ||
    empty($_POST['pwd'])
) {
    $_SESSION['err'] = 'Não preencheu campos obrigatórios.';
    header('Location: ./registo.php');
    exit;
}

// Recolher dados
$nome = $_POST['nome'];
$data_nascimento = $_POST['data_nascimento'];
$telemovel = $_POST['telemovel'];
$email = $_POST['email'];
$user = $_POST['user'];
$pwd = md5($_POST['pwd']);

// Validar username (sem espaços e minúsculas)
if (strpos($user, ' ') !== false || strtolower($user) !== $user) {
    $_SESSION['err'] = "O nome de utilizador '$user' não é válido.";
    header('Location: ./registo.php');
    exit;
}

// Função para verificar se user existe
function userExiste($conn, $user) {
    $user = mysqli_real_escape_string($conn, $user);

    $sql = "SELECT id FROM utilizador WHERE user = '$user'";
    $res = mysqli_query($conn, $sql);
    if (!$res) return true;

    return mysqli_num_rows($res) > 0;
}

// Verificar se já existe utilizador
if (userExiste($conn, $user)) {
    $_SESSION['err'] = "Já existe um utilizador com o nome de utilizador '$user'.";
    header('Location: ./registo.php');
    exit;
}

// 🔥 REMOVIDO: tabela Administrador (não existe na tua BD)

// Inserir utilizador (AUTO_INCREMENT deve estar ativo)
$sql = "INSERT INTO utilizador 
        (user, nome, data_nascimento, telemovel, email, pwd) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die('Erro técnico na preparação da query.');
}

mysqli_stmt_bind_param(
    $stmt,
    "ssssss",
    $user,
    $nome,
    $data_nascimento,
    $telemovel,
    $email,
    $pwd
);

$ok = mysqli_stmt_execute($stmt);

if (!$ok) {
    die('Falha técnica ao inserir utilizador.');
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Redirecionar para login
header('Location: ./login.php');
exit;
?>