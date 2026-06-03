<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user'], $_SESSION['nivel'])) {
    include './erro.php';
    exit;
}

// Apenas encarregados de educação (nivel 2) podem acessar esta página
if ($_SESSION['nivel'] != 2) {
    $_SESSION['err'] = "Acesso negado. Apenas encarregados de educação podem inscrever alunos.";
    header("Location: index.php");
    exit;
}

$id_user = intval($_SESSION['id_user']);

// Obter o enc_educacao.id do encarregado autenticado
$stmt_enc = mysqli_prepare($conn, "SELECT id FROM enc_educacao WHERE email = (SELECT email FROM utilizador WHERE id = ?)");
mysqli_stmt_bind_param($stmt_enc, "i", $id_user);
mysqli_stmt_execute($stmt_enc);
$res_enc = mysqli_stmt_get_result($stmt_enc);
mysqli_stmt_close($stmt_enc);

if (!$res_enc || mysqli_num_rows($res_enc) == 0) {
    $_SESSION['err'] = "Encarregado não encontrado na base de dados.";
    header("Location: erro.php");
    exit;
}

$enc_data = mysqli_fetch_assoc($res_enc);
$enc_id = $enc_data['id'];

// Se clicar em inscrever
if (isset($_POST['id_aluno']) && isset($_POST['id_atividade'])) {

    $id_aluno = intval($_POST['id_aluno']);
    $id_atividade = intval($_POST['id_atividade']);

    // Verificar se o aluno pertence ao encarregado
    $stmt_aluno = mysqli_prepare($conn, "SELECT id FROM aluno WHERE id = ? AND enc_educacao = ?");
    mysqli_stmt_bind_param($stmt_aluno, "ii", $id_aluno, $enc_id);
    mysqli_stmt_execute($stmt_aluno);
    $res_aluno = mysqli_stmt_get_result($stmt_aluno);
    mysqli_stmt_close($stmt_aluno);

    if (mysqli_num_rows($res_aluno) == 0) {
        $_SESSION['err'] = "Este aluno não pertence à sua conta.";
        header("Location: inscrever_aluno_encarregado.php");
        exit;
    }

    // Verificar duplicado
    $stmt_check = mysqli_prepare($conn, "SELECT * FROM inscricao WHERE aluno = ? AND atividade = ?");
    mysqli_stmt_bind_param($stmt_check, "ii", $id_aluno, $id_atividade);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    mysqli_stmt_close($stmt_check);

    if (mysqli_num_rows($res_check) > 0) {
        $_SESSION['err'] = "O aluno já está inscrito nesta atividade.";
        header("Location: inscrever_aluno_encarregado.php");
        exit;
    }

    // Verificar se a atividade está cheia
    $stmt_lotacao = mysqli_prepare($conn, "SELECT COUNT(*) as total_inscritos, a.lotacao_max 
                      FROM inscricao i 
                      INNER JOIN atividade a ON i.atividade = a.id 
                      WHERE i.atividade = ?
                      GROUP BY a.id");
    mysqli_stmt_bind_param($stmt_lotacao, "i", $id_atividade);
    mysqli_stmt_execute($stmt_lotacao);
    $res_lotacao = mysqli_stmt_get_result($stmt_lotacao);
    mysqli_stmt_close($stmt_lotacao);
    
    if ($res_lotacao && mysqli_num_rows($res_lotacao) > 0) {
        $lotacao_data = mysqli_fetch_assoc($res_lotacao);
        if ($lotacao_data['total_inscritos'] >= $lotacao_data['lotacao_max']) {
            $_SESSION['err'] = "A atividade atingiu o número máximo de inscritos.";
            header("Location: inscrever_aluno_encarregado.php");
            exit;
        }
    }

    // Verificar se a criança não tem mais de 16 anos
    $stmt_idade = mysqli_prepare($conn, "SELECT data_nascimento FROM aluno WHERE id = ?");
    mysqli_stmt_bind_param($stmt_idade, "i", $id_aluno);
    mysqli_stmt_execute($stmt_idade);
    $res_idade = mysqli_stmt_get_result($stmt_idade);
    mysqli_stmt_close($stmt_idade);
    
    if ($res_idade && mysqli_num_rows($res_idade) > 0) {
        $aluno_data = mysqli_fetch_assoc($res_idade);
        $data_nascimento = new DateTime($aluno_data['data_nascimento']);
        $hoje = new DateTime();
        $idade = $hoje->diff($data_nascimento)->y;
        
        if ($idade > 16) {
            $_SESSION['err'] = "O aluno tem mais de 16 anos e não pode ser inscrito.";
            header("Location: inscrever_aluno_encarregado.php");
            exit;
        }
    }

    // Inserir inscrição
    $stmt_insert = mysqli_prepare($conn, "INSERT INTO inscricao (aluno, atividade, dia, esta_presente) VALUES (?, ?, CURDATE(), 0)");
    mysqli_stmt_bind_param($stmt_insert, "ii", $id_aluno, $id_atividade);
    mysqli_stmt_execute($stmt_insert);
    mysqli_stmt_close($stmt_insert);

    $_SESSION['info'] = "Aluno inscrito com sucesso na atividade.";
    header("Location: inscrever_aluno_encarregado.php");
    exit;
}

// Listar alunos do encarregado
$stmt_alunos = mysqli_prepare($conn, "SELECT id, nome, data_nascimento FROM aluno WHERE enc_educacao = ? ORDER BY nome");
mysqli_stmt_bind_param($stmt_alunos, "i", $enc_id);
mysqli_stmt_execute($stmt_alunos);
$result_alunos = mysqli_stmt_get_result($stmt_alunos);
mysqli_stmt_close($stmt_alunos);

// Listar atividades
$stmt_atividades = mysqli_prepare($conn, "SELECT * FROM atividade ORDER BY data_inicio");
mysqli_stmt_execute($stmt_atividades);
$result_atividades = mysqli_stmt_get_result($stmt_atividades);
mysqli_stmt_close($stmt_atividades);
?>

    <meta charset="UTF-8">
    <title>Inscrever Aluno</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff; border: none;" data-bs-theme="light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarColor01">
            <ul class="navbar-nav mx-auto align-items-center justify-content-center">
                <a class="navbar-brand" href="index.php">ALGAZARRA</a>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="atividades.php">Atividades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contactos.php">Contactos</a>
                </li>
                <?php
                if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPai" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownPai">';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Alunos</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
                ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php
                if (isset($_SESSION['id_user']) && isset($_SESSION['nivel'])) {
                    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>';
                }
                echo '<li class="nav-item">';
                if (isset($_SESSION['id_user']) && isset($_SESSION['nivel'])) {
                    echo '<a class="nav-link" href="dados.php">Dados pessoais</a>';
                } else {
                    echo '<a class="nav-link" href="login.php">Login</a>';
                }
                ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container" style="padding:2vh; margin-top: 80px;">

<?php
if (isset($_SESSION['info'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['info']) . '</div>';
    unset($_SESSION['info']);
}

if (isset($_SESSION['err'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['err']) . '</div>';
    unset($_SESSION['err']);
}
?>

<h2 class="text-center mb-4">Inscrever Aluno em Atividade</h2>

<div class="card shadow-sm p-4">

<form method="post">

    <div class="row">
        <div class="col-md-6 mt-3">
            <label for="id_aluno" class="form-label">Selecione o aluno:</label>
            <select name="id_aluno" id="id_aluno" class="form-control" required>
                <option value="">-- Escolha um aluno --</option>
                <?php while ($aluno = mysqli_fetch_assoc($result_alunos)) { ?>
                    <option value="<?php echo htmlspecialchars($aluno['id']); ?>">
                        <?php echo htmlspecialchars($aluno['nome']); ?> 
                        (<?php echo date('d/m/Y', strtotime($aluno['data_nascimento'])); ?>)
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-6 mt-3">
            <label for="id_atividade" class="form-label">Selecione a atividade:</label>
            <select name="id_atividade" id="id_atividade" class="form-control" required>
                <option value="">-- Escolha uma atividade --</option>
                <?php while ($atividade = mysqli_fetch_assoc($result_atividades)) { ?>
                    <option value="<?php echo htmlspecialchars($atividade['id']); ?>">
                        <?php echo htmlspecialchars($atividade['titulo']); ?> 
                        (<?php echo date('d/m/Y', strtotime($atividade['data_inicio'])); ?> a <?php echo date('d/m/Y', strtotime($atividade['data_fim'])); ?>)
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="text-end mt-4">
        <button type="submit" class="btn btn-success fw-bold">
            Inscrever Aluno
        </button>
    </div>

</form>

</div>

<!-- BOTÃO VOLTAR -->
<div class="text-start mt-3">
    <a href="gerir_criancas.php" class="btn btn-secondary">
        ⬅ Voltar
    </a>
</div>

</div>

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>
