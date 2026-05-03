<?php
	session_start();
	include './basedados.h';
?>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Algazarra</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap.css">
    <style>
        body {
            /* Define a imagem como fundo fixo */
            background-image: url('img/index2.png');
            background-attachment: fixed; /* Torna a imagem ESTÁTICA ao fazer scroll */
            background-size: cover;       /* Torna a imagem ADAPTÁVEL a qualquer ecrã */
            background-position: center;  /* Centraliza para não cortar partes erradas */
            background-repeat: no-repeat; /* Impede que a imagem se duplique[cite: 1] */
            min-height: 100vh;            /* Garante que o fundo cubra a altura toda[cite: 1] */
            margin: 0;
        }

        /* Ajuste para o conteúdo não ficar por baixo da navbar fixa azul */
        .container {
            padding-top: 100px; 
        }
    </style>
</head>

<body style="background-color: #F5F5F5;">
	<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff; border: none;" data-bs-theme="light">
	<div class="container-fluid">
		<a class="navbar-brand" href="index.php">ALGAZARRA</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav me-auto">
				<li class="nav-item">
					<a class="nav-link active" href="index.php">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="atividades.php">Atividades</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="contactos.php">Contactos</a>
				</li>
				<?php
				// Administrador
				if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 1)
					echo
				'<li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="gerir_utilizadores.php">Gerir utilizadores</a></li>
						<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>
						<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>
						<li><a class="dropdown-item" href="criar_atividade.php">Criar atividade</a></li>
                    </ul>
                </li>';
				// Pai
				if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2)
					echo
				'<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>
					</ul>
				</li>';
				?>
			</ul>
			<ul class="navbar-nav ms-auto">
			<?php
			if (isset($_SESSION['id_user']) && isset($_SESSION['nivel']))
				echo 
				'<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>';
			echo
				'<li class="nav-item">
					<a class="nav-link" href="'; if (isset($_SESSION['id_user']) && isset($_SESSION['nivel'])) echo "dados.php\">Dados pessoais</a>"; else echo "login.php\">Login/Registo</a>";
			?>
				</li>
			</ul>
		</div>
	</div>
	</nav>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
<?php
	include './rodape.php'; 
?>