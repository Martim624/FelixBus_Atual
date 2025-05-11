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
    <title>FelixBus - Login</title>
    <link rel="shortcut icon" type="image/png" href="logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- Barra de navegação fixa no topo -->
<nav>
    <div class="logo">FelixBus</div>
    
    <!-- Ícone do menu em dispositivos móveis -->
    <span class="menu-toggle" id="menu-toggle">&#9776;</span>

    <!-- Links do menu -->
    <div class="menu">
        <a href="login.php" class="active"><i class="fas fa-sign-in-alt"></i> Login</a>
        <a href="registo.php"><i class="fas fa-user-plus"></i> Registo</a>
        <a href="rotas.php"><i class="fas fa-route"></i> Rotas</a>
        <a href="#"><i class="fas fa-phone"></i> Contactos</a>
    </div>
</nav>



<nav>
    <a href="index.php" class="logo">FelixBus</a>
    <div class="menu">
        <a href="login.php" class="active"><i class="fas fa-sign-in-alt"></i> Login</a>
        <a href="registo.php"><i class="fas fa-user-plus"></i> Registo</a>
        <a href="rotas.php"><i class="fas fa-route"></i> Rotas</a>
        <a href="#"><i class="fas fa-phone"></i> Contactos</a>
    </div>
</nav>


<!-- Hero Banner -->
<div class="hero">
    <div class="hero-content">
        <h1 style="color: white !important">Bem-vindo à FelixBus <br></h1>
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
