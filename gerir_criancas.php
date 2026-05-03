<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user'], $_SESSION['nivel'])) {
    include './erro.php';
    exit;
}

$id_user = intval($_SESSION['id_user']);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Algazarra</title>
<link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color:#F5F5F5; margin:10vh 0;">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color:#00d0ff;">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">ALGAZARRA</a>
    </div>
</nav>

<div class="container" style="padding:2vh;">

<h1 class="text-center">Gerir crianças</h1>

<?php

if ($_SESSION['nivel'] == 1) {

    $sql = "SELECT a.*, e.nome AS enc_nome
            FROM aluno a
            LEFT JOIN enc_educacao e ON a.enc_educacao = e.id
            ORDER BY a.nome";

} else {

    $sql = "SELECT a.*, e.nome AS enc_nome
            FROM aluno a
            INNER JOIN enc_educacao e ON a.enc_educacao = e.id
            WHERE e.id = $id_user
            ORDER BY a.data_nascimento";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Erro na base de dados: " . mysqli_error($conn));
}

echo '<table class="table mt-4">';

echo '<tr style="background:#00d0ff;">
        <th>Nome</th>
        <th>Data nascimento</th>';

if ($_SESSION['nivel'] == 1) {
    echo '<th>Encarregado</th>';
}

echo '<th>Alterar</th>
      <th>Atividades</th>
      <th>Inscrever</th>
      <th>Presenças</th>
      </tr>';

while ($aluno = mysqli_fetch_assoc($result)) {

    echo '<tr>
            <td>' . htmlspecialchars($aluno['nome']) . '</td>
            <td>' . date('d/m/Y', strtotime($aluno['data_nascimento'])) . '</td>';

    if ($_SESSION['nivel'] == 1) {
        echo '<td>' . htmlspecialchars($aluno['enc_nome']) . '</td>';
    }

    // ALTERAR
    echo '<td>
            <a href="alterar_dados_crianca.php?id=' . $aluno['id'] . '">Alterar</a>
          </td>';

    // ATIVIDADES
    echo '<td>
            <a href="atividades_crianca.php?id=' . $aluno['id'] . '">Atividades</a>
          </td>';

    // INSCREVER
    echo '<td>
            <a href="inscrever.php?id=' . $aluno['id'] . '" 
               class="btn btn-success btn-sm">
                Inscrever
            </a>
          </td>';

    // PRESENÇAS
    echo '<td>';

    $sqlAt = "SELECT atividade FROM inscricao 
              WHERE aluno = " . $aluno['id'] . " 
              LIMIT 1";

    $resAt = mysqli_query($conn, $sqlAt);
    $atividade = mysqli_fetch_assoc($resAt);

    if ($atividade) {

        echo '<a href="marcar_presencas.php?crianca='.$aluno['id'].'&atividade='.$atividade['atividade'].'" 
               class="btn btn-warning btn-sm">
               Ver presenças
              </a>';

    } else {

        echo '<span class="text-muted">Sem atividades</span>';
    }

    echo '</td>';

    echo '</tr>';
}

echo '</table>';
?>

<!-- BOTÃO VOLTAR -->
<div class="mt-3">
    <a href="javascript:history.back()" class="btn btn-secondary">
        Voltar
    </a>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include './rodape.php'; ?>