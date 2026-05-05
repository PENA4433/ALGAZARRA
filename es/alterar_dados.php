<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user'])) {
    include './erro.php';
    exit();
}

$id_user = $_SESSION['id_user'];

$nome = $_POST['nome'];
$email = $_POST['email'];
$telemovel = $_POST['telemovel'];
$data_nascimento = $_POST['data_nascimento'];

$sql = "UPDATE utilizador SET 
        nome='$nome',
        email='$email',
        telemovel='$telemovel',
        data_nascimento='$data_nascimento'
        WHERE id=$id_user";

$res = mysqli_query($conn, $sql);

if ($res) {
    $_SESSION['info'] = "Dados atualizados com sucesso!";
} else {
    $_SESSION['err'] = "Erro ao atualizar dados!";
}

header("Location: dados.php");
exit();
?>