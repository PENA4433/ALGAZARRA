<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 2) {
    include './erro.php';
    exit;
}

// Validar campos obrigatórios
if (empty($_POST['nome']) || empty($_POST['data_nascimento']) || empty($_POST['enc_educacao'])) {
    $_SESSION['err'] = 'Não preencheu todos os campos obrigatórios.';
    header("Location: criar_perfil.php");
    exit;
}

$nome = trim($_POST['nome']);
$data_nascimento = $_POST['data_nascimento'];
$enc_educacao = intval($_POST['enc_educacao']);

// Validar nome (não vazio e comprimento razoável)
if (strlen($nome) < 2 || strlen($nome) > 100) {
    $_SESSION['err'] = 'O nome deve ter entre 2 e 100 caracteres.';
    header("Location: criar_perfil.php");
    exit;
}

// Validar data de nascimento
$data_obj = DateTime::createFromFormat('Y-m-d', $data_nascimento);
if (!$data_obj) {
    $_SESSION['err'] = 'Data de nascimento inválida.';
    header("Location: criar_perfil.php");
    exit;
}

// Validar se a data não é no futuro
$hoje = new DateTime();
if ($data_obj > $hoje) {
    $_SESSION['err'] = 'A data de nascimento não pode ser no futuro.';
    header("Location: criar_perfil.php");
    exit;
}

// Validar idade: máximo 16 anos
$idade = $hoje->diff($data_obj)->y;
if ($idade > 16) {
    $_SESSION['err'] = 'A criança deve ter no máximo 16 anos.';
    header("Location: criar_perfil.php");
    exit;
}

// Verificar se o encarregado existe e pertence ao utilizador autenticado
$id_user = intval($_SESSION['id_user']);
$stmt_verify = mysqli_prepare($conn, "SELECT id FROM enc_educacao WHERE id = ? AND email = (SELECT email FROM utilizador WHERE id = ?)");
mysqli_stmt_bind_param($stmt_verify, "ii", $enc_educacao, $id_user);
mysqli_stmt_execute($stmt_verify);
$res_verify = mysqli_stmt_get_result($stmt_verify);
mysqli_stmt_close($stmt_verify);

if (!$res_verify || mysqli_num_rows($res_verify) == 0) {
    $_SESSION['err'] = 'Encarregado inválido ou não autorizado.';
    header("Location: criar_perfil.php");
    exit;
}

// Inserir criança usando prepared statement
$stmt_insert = mysqli_prepare($conn, "INSERT INTO aluno (nome, data_nascimento, enc_educacao) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt_insert, "ssi", $nome, $data_nascimento, $enc_educacao);
$retVal = mysqli_stmt_execute($stmt_insert);
mysqli_stmt_close($stmt_insert);

if (!$retVal) {
    $_SESSION['err'] = 'Erro ao criar criança. Tente novamente.';
    header("Location: criar_perfil.php");
    exit;
}

$_SESSION['info'] = "Criança " . htmlspecialchars($nome) . " adicionada com sucesso.";
header("Location: gerir_criancas.php");
exit;
?>
