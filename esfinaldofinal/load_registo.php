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
$nome = trim($_POST['nome']);
$data_nascimento = $_POST['data_nascimento'];
$telemovel = trim($_POST['telemovel']);
$email = trim($_POST['email']);
$user = trim($_POST['user']);
$pwd = md5($_POST['pwd']);

// Validar username (sem espaços e minúsculas)
if (strpos($user, ' ') !== false || strtolower($user) !== $user) {
    $_SESSION['err'] = "O nome de utilizador '$user' não é válido.";
    header('Location: ./registo.php');
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['err'] = 'O email indicado não é válido.';
    header('Location: ./registo.php');
    exit;
}

// Função para verificar se já existe um registo numa tabela/campo
function existeValor($conn, $tabela, $campo, $valor) {
    $sql = "SELECT id FROM $tabela WHERE $campo = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) return true;

    mysqli_stmt_bind_param($stmt, "s", $valor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    return $res && mysqli_num_rows($res) > 0;
}

// Verificar se já existe utilizador com o mesmo username
if (existeValor($conn, 'utilizador', 'user', $user)) {
    $_SESSION['err'] = "Já existe um utilizador com o nome de utilizador '$user'.";
    header('Location: ./registo.php');
    exit;
}

// Verificar se o email já está associado a outro utilizador ou encarregado
if (existeValor($conn, 'utilizador', 'email', $email) || existeValor($conn, 'enc_educacao', 'email', $email)) {
    $_SESSION['err'] = 'Já existe uma conta associada a esse email.';
    header('Location: ./registo.php');
    exit;
}

// O registo público cria sempre um encarregado de educação (nível 2)
mysqli_begin_transaction($conn);

try {
    // Inserir utilizador
    $sql_user = "INSERT INTO utilizador 
                 (user, nome, data_nascimento, telemovel, email, pwd, nivel) 
                 VALUES (?, ?, ?, ?, ?, ?, 2)";

    $stmt_user = mysqli_prepare($conn, $sql_user);
    if (!$stmt_user) {
        throw new Exception('Erro técnico na preparação da query de utilizador.');
    }

    mysqli_stmt_bind_param(
        $stmt_user,
        "ssssss",
        $user,
        $nome,
        $data_nascimento,
        $telemovel,
        $email,
        $pwd
    );

    if (!mysqli_stmt_execute($stmt_user)) {
        throw new Exception('Falha técnica ao inserir utilizador.');
    }
    mysqli_stmt_close($stmt_user);

    // Inserir também o encarregado de educação correspondente.
    // As páginas de gestão/inscrição encontram o encarregado através do email do utilizador autenticado.
    $sql_enc = "INSERT INTO enc_educacao (nome, telemovel, email, morada)
                VALUES (?, ?, ?, NULL)";

    $stmt_enc = mysqli_prepare($conn, $sql_enc);
    if (!$stmt_enc) {
        throw new Exception('Erro técnico na preparação da query de encarregado.');
    }

    mysqli_stmt_bind_param($stmt_enc, "sss", $nome, $telemovel, $email);

    if (!mysqli_stmt_execute($stmt_enc)) {
        throw new Exception('Falha técnica ao inserir encarregado de educação.');
    }
    mysqli_stmt_close($stmt_enc);

    mysqli_commit($conn);
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['err'] = 'Não foi possível concluir o registo. Tente novamente.';
    header('Location: ./registo.php');
    exit;
}

mysqli_close($conn);

// Redirecionar para login
header('Location: ./login.php');
exit;
?>
