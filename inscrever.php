<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user'], $_SESSION['nivel'])) {
    include './erro.php';
    exit;
}

/* validar aluno */
$id_user = intval($_SESSION['id_user']);
$nivel = intval($_SESSION['nivel']);

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id_crianca = (int) $_GET['id'];
} elseif (isset($_POST['id_crianca']) && ctype_digit($_POST['id_crianca'])) {
    $id_crianca = (int) $_POST['id_crianca'];
} else {
    die("Aluno inválido");
}

$voltar = isset($_GET['voltar']) ? $_GET['voltar'] : 'gerir_criancas.php';

function redirecionarComErro($mensagem, $destino) {
    $_SESSION['err'] = $mensagem;
    header("Location: $destino");
    exit;
}

function redirecionarComInfo($mensagem, $destino) {
    $_SESSION['info'] = $mensagem;
    header("Location: $destino");
    exit;
}

/*
 * Os professores podem inscrever qualquer criança.
 * Os encarregados de educação só podem inscrever crianças associadas ao seu próprio perfil.
 */
if ($nivel == 2) {

    $stmt_enc = mysqli_prepare($conn, "
        SELECT id 
        FROM enc_educacao 
        WHERE email = (
            SELECT email 
            FROM utilizador 
            WHERE id = ?
        )
    ");

    mysqli_stmt_bind_param($stmt_enc, "i", $id_user);
    mysqli_stmt_execute($stmt_enc);
    $res_enc = mysqli_stmt_get_result($stmt_enc);
    mysqli_stmt_close($stmt_enc);

    if (!$res_enc || mysqli_num_rows($res_enc) == 0) {
        redirecionarComErro('Encarregado não encontrado na base de dados.', 'erro.php');
    }

    $enc_data = mysqli_fetch_assoc($res_enc);
    $enc_id = intval($enc_data['id']);

    $stmt_aluno = mysqli_prepare($conn, "
        SELECT id, nome 
        FROM aluno 
        WHERE id = ? 
        AND enc_educacao = ?
    ");

    mysqli_stmt_bind_param($stmt_aluno, "ii", $id_crianca, $enc_id);
    mysqli_stmt_execute($stmt_aluno);
    $res_aluno = mysqli_stmt_get_result($stmt_aluno);
    mysqli_stmt_close($stmt_aluno);

    if (!$res_aluno || mysqli_num_rows($res_aluno) == 0) {
        redirecionarComErro('Não tem permissão para inscrever esta criança.', 'gerir_criancas.php');
    }

    $aluno_atual = mysqli_fetch_assoc($res_aluno);

} elseif ($nivel == 1) {

    $stmt_aluno = mysqli_prepare($conn, "
        SELECT id, nome 
        FROM aluno 
        WHERE id = ?
    ");

    mysqli_stmt_bind_param($stmt_aluno, "i", $id_crianca);
    mysqli_stmt_execute($stmt_aluno);
    $res_aluno = mysqli_stmt_get_result($stmt_aluno);
    mysqli_stmt_close($stmt_aluno);

    if (!$res_aluno || mysqli_num_rows($res_aluno) == 0) {
        redirecionarComErro('Criança não encontrada.', 'gerir_criancas.php');
    }

    $aluno_atual = mysqli_fetch_assoc($res_aluno);

} else {
    redirecionarComErro('Acesso negado.', 'index.php');
}

/* se clicar em inscrever */
if (isset($_POST['id_atividade']) && ctype_digit($_POST['id_atividade'])) {

    $id_atividade = (int) $_POST['id_atividade'];

    /* verificar se a atividade existe */
    $stmt_atividade = mysqli_prepare($conn, "
        SELECT id, lotacao_max 
        FROM atividade 
        WHERE id = ?
    ");

    mysqli_stmt_bind_param($stmt_atividade, "i", $id_atividade);
    mysqli_stmt_execute($stmt_atividade);
    $res_atividade = mysqli_stmt_get_result($stmt_atividade);
    mysqli_stmt_close($stmt_atividade);

    if (!$res_atividade || mysqli_num_rows($res_atividade) == 0) {
        redirecionarComErro('Atividade inválida.', "inscrever.php?id=$id_crianca&voltar=" . urlencode($voltar));
    }

    $atividade_data = mysqli_fetch_assoc($res_atividade);
    $lotacao_max = intval($atividade_data['lotacao_max']);

    /* verificar duplicado */
    $stmt_check = mysqli_prepare($conn, "
        SELECT aluno 
        FROM inscricao 
        WHERE aluno = ? 
        AND atividade = ? 
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt_check, "ii", $id_crianca, $id_atividade);
    mysqli_stmt_execute($stmt_check);
    $res = mysqli_stmt_get_result($stmt_check);
    mysqli_stmt_close($stmt_check);

    if ($res && mysqli_num_rows($res) > 0) {
        redirecionarComErro('Esta criança já está inscrita nesta atividade.', "inscrever.php?id=$id_crianca&voltar=" . urlencode($voltar));
    }

    /* verificar lotação */
    $stmt_lotacao = mysqli_prepare($conn, "
        SELECT COUNT(DISTINCT aluno) AS total_inscritos 
        FROM inscricao 
        WHERE atividade = ?
    ");

    mysqli_stmt_bind_param($stmt_lotacao, "i", $id_atividade);
    mysqli_stmt_execute($stmt_lotacao);
    $res_lotacao = mysqli_stmt_get_result($stmt_lotacao);
    mysqli_stmt_close($stmt_lotacao);

    $total_inscritos = 0;

    if ($res_lotacao && mysqli_num_rows($res_lotacao) > 0) {
        $lotacao_data = mysqli_fetch_assoc($res_lotacao);
        $total_inscritos = intval($lotacao_data['total_inscritos']);
    }

    if ($total_inscritos >= $lotacao_max) {
        redirecionarComErro('A atividade atingiu o número máximo de inscritos.', "inscrever.php?id=$id_crianca&voltar=" . urlencode($voltar));
    }

    /* verificar se a criança não tem mais de 16 anos */
    $stmt_idade = mysqli_prepare($conn, "
        SELECT data_nascimento 
        FROM aluno 
        WHERE id = ?
    ");

    mysqli_stmt_bind_param($stmt_idade, "i", $id_crianca);
    mysqli_stmt_execute($stmt_idade);
    $res_idade = mysqli_stmt_get_result($stmt_idade);
    mysqli_stmt_close($stmt_idade);

    if ($res_idade && mysqli_num_rows($res_idade) > 0) {
        $aluno_data = mysqli_fetch_assoc($res_idade);
        $data_nascimento = new DateTime($aluno_data['data_nascimento']);
        $hoje = new DateTime();
        $idade = $hoje->diff($data_nascimento)->y;

        if ($idade > 16) {
            redirecionarComErro('A criança tem mais de 16 anos e não pode ser inscrita.', "inscrever.php?id=$id_crianca&voltar=" . urlencode($voltar));
        }
    }

    /* inserir inscrição com prepared statement */
    $stmt_insert = mysqli_prepare($conn, "
        INSERT INTO inscricao (aluno, atividade, dia, esta_presente) 
        VALUES (?, ?, CURDATE(), 0)
    ");

    mysqli_stmt_bind_param($stmt_insert, "ii", $id_crianca, $id_atividade);

    if (!mysqli_stmt_execute($stmt_insert)) {
        mysqli_stmt_close($stmt_insert);
        redirecionarComErro('Não foi possível concluir a inscrição.', "inscrever.php?id=$id_crianca&voltar=" . urlencode($voltar));
    }

    mysqli_stmt_close($stmt_insert);

    redirecionarComInfo('Inscrição feita com sucesso.', 'gerir_criancas.php');

} elseif (isset($_POST['id_atividade'])) {
    redirecionarComErro('Atividade inválida.', "inscrever.php?id=$id_crianca&voltar=" . urlencode($voltar));
}

/* listar atividades */
$stmt_atividades = mysqli_prepare($conn, "
    SELECT * 
    FROM atividade 
    ORDER BY data_inicio
");

mysqli_stmt_execute($stmt_atividades);
$result = mysqli_stmt_get_result($stmt_atividades);
mysqli_stmt_close($stmt_atividades);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Inscrever Criança</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container" style="padding:2vh; margin-top: 30px;">

    <h2 class="text-center">Escolher atividade</h2>
    <h5 class="text-center text-muted">
        Criança: <?php echo htmlspecialchars($aluno_atual['nome']); ?>
    </h5>

    <?php
    if (isset($_SESSION['info'])) {
        echo '<div class="alert alert-success mt-3">' . htmlspecialchars($_SESSION['info']) . '</div>';
        unset($_SESSION['info']);
    }

    if (isset($_SESSION['err'])) {
        echo '<div class="alert alert-danger mt-3">' . htmlspecialchars($_SESSION['err']) . '</div>';
        unset($_SESSION['err']);
    }
    ?>

    <?php while ($atividade = mysqli_fetch_assoc($result)) { ?>

        <div class="card mt-3 p-3">

            <h5><?php echo htmlspecialchars($atividade['titulo']); ?></h5>

            <p>
                De <?php echo date('d/m/Y', strtotime($atividade['data_inicio'])); ?>
                até <?php echo date('d/m/Y', strtotime($atividade['data_fim'])); ?>
            </p>

            <form method="post">

                <input type="hidden" name="id_crianca"
                       value="<?php echo htmlspecialchars($id_crianca); ?>">

                <input type="hidden" name="id_atividade"
                       value="<?php echo htmlspecialchars($atividade['id']); ?>">

                <button type="submit" class="btn btn-success btn-sm">
                    Inscrever nesta atividade
                </button>

            </form>

        </div>

    <?php } ?>

    <div class="mt-3">
        <a href="<?php echo htmlspecialchars($voltar); ?>" class="btn btn-secondary">
            Voltar
        </a>
    </div>

</div>

<?php include './rodape.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>

</body>
</html>