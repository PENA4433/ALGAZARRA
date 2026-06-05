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

if (isset($_POST['voltar'])) {
    $voltar = $_POST['voltar'];
}

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
 * Admin e professor podem desinscrever qualquer criança.
 * Encarregados só podem desinscrever crianças associadas ao seu perfil.
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
        redirecionarComErro('Não tem permissão para desinscrever esta criança.', 'gerir_criancas.php');
    }

    $aluno_atual = mysqli_fetch_assoc($res_aluno);

} elseif ($nivel == 1 || $nivel == 3) {

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

/* se clicar em desinscrever */
if (isset($_POST['id_atividade']) && ctype_digit($_POST['id_atividade'])) {

    $id_atividade = (int) $_POST['id_atividade'];

    /* verificar se a inscrição existe */
    $stmt_check = mysqli_prepare($conn, "
        SELECT aluno 
        FROM inscricao 
        WHERE aluno = ? 
        AND atividade = ? 
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt_check, "ii", $id_crianca, $id_atividade);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    mysqli_stmt_close($stmt_check);

    if (!$res_check || mysqli_num_rows($res_check) == 0) {
        redirecionarComErro(
            'Esta criança não está inscrita nesta atividade.',
            "desinscrever.php?id=$id_crianca&voltar=" . urlencode($voltar)
        );
    }

    /* remover inscrição */
    $stmt_delete = mysqli_prepare($conn, "
        DELETE FROM inscricao 
        WHERE aluno = ? 
        AND atividade = ?
    ");

    mysqli_stmt_bind_param($stmt_delete, "ii", $id_crianca, $id_atividade);

    if (!mysqli_stmt_execute($stmt_delete)) {
        mysqli_stmt_close($stmt_delete);
        redirecionarComErro(
            'Não foi possível desinscrever a criança.',
            "desinscrever.php?id=$id_crianca&voltar=" . urlencode($voltar)
        );
    }

    mysqli_stmt_close($stmt_delete);

    redirecionarComInfo(
        'Criança desinscrita com sucesso.',
        "desinscrever.php?id=$id_crianca&voltar=" . urlencode($voltar)
    );

} elseif (isset($_POST['id_atividade'])) {
    redirecionarComErro(
        'Atividade inválida.',
        "desinscrever.php?id=$id_crianca&voltar=" . urlencode($voltar)
    );
}

/* listar apenas atividades em que a criança está inscrita */
$stmt_atividades = mysqli_prepare($conn, "
    SELECT DISTINCT a.id, a.titulo, a.data_inicio, a.data_fim
    FROM atividade a
    INNER JOIN inscricao i ON i.atividade = a.id
    WHERE i.aluno = ?
    ORDER BY a.data_inicio
");

mysqli_stmt_bind_param($stmt_atividades, "i", $id_crianca);
mysqli_stmt_execute($stmt_atividades);
$result = mysqli_stmt_get_result($stmt_atividades);
mysqli_stmt_close($stmt_atividades);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Desinscrever Criança</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container" style="padding:2vh; margin-top: 30px;">

    <h2 class="text-center">Escolher atividade para desinscrever</h2>
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

    <?php if ($result && mysqli_num_rows($result) > 0) { ?>

        <?php while ($atividade = mysqli_fetch_assoc($result)) { ?>

            <div class="card mt-3 p-3">

                <h5><?php echo htmlspecialchars($atividade['titulo']); ?></h5>

                <p>
                    De <?php echo date('d/m/Y', strtotime($atividade['data_inicio'])); ?>
                    até <?php echo date('d/m/Y', strtotime($atividade['data_fim'])); ?>
                </p>

                <form method="post" onsubmit="return confirm('Tem a certeza que quer desinscrever esta criança desta atividade?');">

                    <input type="hidden" name="id_crianca"
                           value="<?php echo htmlspecialchars($id_crianca); ?>">

                    <input type="hidden" name="id_atividade"
                           value="<?php echo htmlspecialchars($atividade['id']); ?>">

                    <input type="hidden" name="voltar"
                           value="<?php echo htmlspecialchars($voltar); ?>">

                    <button type="submit" class="btn btn-danger btn-sm">
                        Desinscrever desta atividade
                    </button>

                </form>

            </div>

        <?php } ?>

    <?php } else { ?>

        <div class="alert alert-warning mt-3">
            Esta criança não está inscrita em nenhuma atividade.
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