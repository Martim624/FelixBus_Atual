<?php
// index.php
require_once("../basededados/basedados.h");

// Carregar alertas/promocoes
$alertas = mysqli_query($ligacao, "SELECT * FROM alerta ORDER BY dataPublicacao DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>FelixBus - Bem-vindo</title>
    <link rel="shortcut icon" type="image/png" href="logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            color: #333;
        }

        /* Estilos para o Hero Banner */
        .hero {
            position: relative;
            height: 700px; /* Aumentamos a altura */
            background: url('banner.jpg') center/cover no-repeat;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .hero::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Escurece a imagem */
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.6);
        }

        .hero p {
            font-size: 1.5em;
            margin-bottom: 20px;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .hero-buttons a {
            background-color:rgb(15, 255, 7);
            color: #333;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: background-color 0.3s;
        }

        .hero-buttons a:hover {
            background-color:rgb(4, 109, 1);
        }

        /* Estilos de navegação fixa */
        nav {
            background: linear-gradient(135deg, #006400, #32CD32); /* Gradiente de verde */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: background 0.3s ease;
        }

        /* Logo */
        .logo {
            color: white;
            font-size: 1.8em;
            font-weight: bold;
            text-transform: uppercase;
            margin-left: 20px;
        }

        /* Links do menu */
        nav a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            font-weight: bold;
            font-size: 1.1em;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #4CAF50; /* Cor de fundo no hover */
            border-radius: 5px;
        }

        /* Menu Responsivo */
        .menu-toggle {
            display: none;
            font-size: 2em;
            color: white;
            cursor: pointer;
            margin-right: 20px;
        }

        .menu {
            display: flex;
            gap: 30px;
        }

        /* Estilos para o menu em telas pequenas */
        @media (max-width: 768px) {
            .menu {
                display: none;
                flex-direction: column;
                background-color: #333;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                padding: 20px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }

            nav a {
                padding: 12px 15px;
                font-size: 1.2em;
            }

            .menu.active {
                display: block;
            }

            .menu-toggle {
                display: block;
            }
        }

        /* Layout do conteúdo */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 20px 20px;
        }

        .promo-cards {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .promo-card {
            background-color: #ffffff;
            padding: 20px;
            flex: 1;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .promo-card h3 {
            margin-bottom: 15px;
        }

        .promo-card p {
            font-size: 1.1em;
            margin-bottom: 15px;
        }

        .promo-card a {
            background-color:rgb(15, 255, 7);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .promo-card a:hover {
            background-color:rgb(4, 109, 1);
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2em;
            }

            .hero p {
                font-size: 1.2em;
            }

            .promo-cards {
                flex-direction: column;
            }

            .promo-card {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Barra de navegação fixa no topo -->
<nav>
    <div class="logo">FelixBus</div>
    
    <!-- Ícone do menu em dispositivos móveis -->
    <span class="menu-toggle" id="menu-toggle">&#9776;</span>

    <!-- Links do menu -->
    <div class="menu" id="menu">
        <a href="login.php">Login</a>
        <a href="registo.php">Registo</a>
        <a href="rotas.php">Rotas</a>
        <a href="#">Contactos</a>
    </div>
</nav>

<!-- Hero Banner -->
<div class="hero">
    <div class="hero-content">
        <h1>Bem-vindo à FelixBus</h1>
        <p>Viaje com conforto, segurança e eficiência.</p>
        <div class="hero-buttons">
            <a href="rotas.php">Ver Rotas</a>
            <a href="registo.php">Registe-se Agora</a>
        </div>
    </div>
</div>

<!-- Conteúdo principal -->
<div class="container">
    <h2>Promoções Exclusivas</h2>
    <div class="promo-cards">
        <div class="promo-card">
            <h3>20% de Desconto em Viagens de Fim de Semana</h3>
            <p>Aproveite os descontos e viaje com a FelixBus para diversos destinos a preços incríveis!</p>
            <a href="#">Saiba Mais</a>
        </div>
        <div class="promo-card">
            <h3>Leve um Amigo e Economize</h3>
            <p>Viaje com um amigo e ambos ganham 10% de desconto na próxima viagem!</p>
            <a href="#">Aproveite Agora</a>
        </div>
        <div class="promo-card">
            <h3>Desconto Estudante</h3>
            <p>Estudantes têm 15% de desconto em todas as rotas FelixBus com o cartão de estudante!</p>
            <a href="#">Ver Descontos</a>
        </div>
    </div>

    <h2>Informações e Promoções</h2>
    <?php while ($alerta = mysqli_fetch_assoc($alertas)): ?>
        <div class="alertas">
            <h3><?= htmlspecialchars($alerta['titulo']) ?></h3>
            <p><?= nl2br(htmlspecialchars($alerta['mensagem'])) ?></p>
            <small>Publicado em: <?= date("d/m/Y H:i", strtotime($alerta['dataPublicacao'])) ?></small>
        </div>
    <?php endwhile; ?>

    <h2>Sobre Nós</h2>
    <p>A FelixBus é uma empresa dedicada ao transporte interurbano de passageiros. Oferecemos rotas regulares entre diversas cidades, com horários flexíveis, preços acessíveis e um serviço de atendimento ao cliente de excelência. Nosso compromisso é proporcionar uma experiência segura e confortável para todos os nossos passageiros.</p>

    <h3>Testemunhos</h3>
    <p>"Viajei com a FelixBus e fiquei impressionado com a qualidade do serviço. O ônibus era confortável e chegou no horário!" - João Silva</p>
    <p>"Recomendo a FelixBus para todas as minhas viagens. Atendimento excelente e preços justos." - Maria Oliveira</p>
</div>

<!-- Rodapé -->
<footer>
    &copy; <?= date("Y") ?> FelixBus. Todos os direitos reservados.
</footer>

<script>
    const menuToggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');

    menuToggle.addEventListener('click', () => {
        menu.classList.toggle('active');
    });
</script>

</body>
</html>
