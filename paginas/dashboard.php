<?php
session_start();
require_once("../basededados/basedados.h");

// Verifica autenticação e perfil
if (!isset($_SESSION['id']) || !isset($_SESSION['idPerfil'])) {
    header("Location: login.php");
    exit();
}

// Obter dados do usuário
$idUtilizador = $_SESSION['id'];
$idPerfil = $_SESSION['idPerfil'];
$nome = $_SESSION['username'];

// Função para verificar se o usuário tem um perfil específico (por ID)
function temPerfil($idPerfilRequerido) {
    return $_SESSION['idPerfil'] == $idPerfilRequerido;
}

// Função para obter o saldo da carteira do usuário
function obterSaldoCarteira($ligacao, $idUtilizador) {
    $query = "SELECT saldo FROM carteira WHERE idUtilizador = ?";
    $stmt = mysqli_prepare($ligacao, $query);
    mysqli_stmt_bind_param($stmt, "i", $idUtilizador);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $carteira = mysqli_fetch_assoc($result);
    return $carteira['saldo'] ?? 0.00;
}

// Obter saldo atual
$saldo = obterSaldoCarteira($ligacao, $idUtilizador);

// Função para mostrar alertas/promoções ativas
function mostrarAlertas($ligacao) {
    $query = "SELECT titulo, mensagem FROM alerta 
              ORDER BY dataPublicacao DESC LIMIT 3";
    $result = mysqli_query($ligacao, $query);
    
    if (mysqli_num_rows($result) > 0) {
        echo '<div class="alertas">';
        echo '<h3>Alertas e Promoções</h3>';
        while ($alerta = mysqli_fetch_assoc($result)) {
            echo '<div class="alerta">';
            echo '<h4>'.htmlspecialchars($alerta['titulo']).'</h4>';
            echo '<p>'.htmlspecialchars($alerta['mensagem']).'</p>';
            echo '</div>';
        }
        echo '</div>';
    }
}

// Função auxiliar para obter o nome do perfil
function obterNomePerfil($idPerfil) {
    switch($idPerfil) {
        case 1: return 'Visitante';
        case 2: return 'Cliente';
        case 3: return 'Funcionário';
        case 4: return 'Administrador';
        default: return 'Desconhecido';
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | FelixBus</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: url('paisagem.jpg') center/cover no-repeat;
            background-size: cover;
            color: #333;
            overflow: hidden;
        }

        .background-blur {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            z-index: -1;
        }

        h1 {
            text-align: center;
            font-size: 2.5em;
            color:rgb(255, 255, 255);
            padding-top: 20px;
        }

        .dashboard {
            display: grid;
            grid-template-columns: 1fr 3fr;
            gap: 20px;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background-color: #ffffff;
            padding: 25px;
            color: #333;
            overflow: hidden;
        }
        .background-blur {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            z-index: -1;
        }
        h1 {
            text-align: center;
            font-size: 2.5em;
            color: #28a745;
            padding-top: 20px;
        }
        .dashboard {
            display: grid;
            grid-template-columns: 1fr 3fr;
            gap: 20px;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .sidebar {
            background-color: #ffffff;
            padding: 25px;            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transition: box-shadow 0.3s ease;
        }
        .sidebar:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
        }
        .sidebar h2 {
            font-size: 1.8em;
            margin-bottom: 25px;
            color: #28a745;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }
        .sidebar p {
            font-size: 1.15em;
            color: #555;
            margin: 12px 0;
        }
        .saldo {
            font-size: 1.7em;
            color: #28a745;
            font-weight: 700;
            margin: 10px 0 30px;
            transition: color 0.3s ease;
        }
        .saldo:hover {
            color: #1e7e34;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 25px 0;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        ul li {
            margin: 15px 0;
        }
        ul li a {
            font-size: 1.1em;
            text-decoration: none;
            color: #28a745;
            padding: 10px 15px;
            border-radius: 8px;
            display: block;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        ul li a:hover {
            background-color: #28a745;
            color: #fff;
            box-shadow: 0 0 8px #28a745aa;
        }
        .content {
            background: #fff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            max-height: 85vh;
        }
        .card {
            background: #f9fdf9;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transform: translateY(-4px);
        }
        .card h2, .card h3 {
            color: #218838;
            margin-bottom: 20px;
        }
        .alertas {
            margin-bottom: 25px;
        }
        .alerta {
            background: #e0f3e0;
            border-left: 6px solid #28a745;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
            color: #2f6627;
            transition: background-color 0.3s ease;
        }
        .alerta h4 {
            margin-bottom: 8px;
            font-size: 1.25em;
        }
        .alerta p {
            font-size: 1.05em;
            line-height: 1.4;
        }
        .alerta a {
            color: #1e7e34;
            font-weight: 600;
            text-decoration: none;
        }
        .alerta a:hover {
            text-decoration: underline;
        }
        /* Bilhetes */
        .bilhete-item {
            background: #ffffff;
            border: 1px solid #d1e7dd;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 15px;
            box-shadow: 0 1px 6px rgba(40, 167, 69, 0.1);
            transition: box-shadow 0.3s ease;
        }
        .bilhete-item:hover {
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.25);
        }
        .bilhete-item strong {
            color: #1e7e34;
        }
        /* Scrollbar customizada para content, para desktops */
        .content::-webkit-scrollbar {
            width: 10px;
        }
        .content::-webkit-scrollbar-thumb {
            background-color: #28a745;
            border-radius: 10px;
        }
        .content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        /* Responsividade */
        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
                padding: 15px;
            }
            .content {
                max-height: none;
                overflow: visible;
                padding: 20px;
            }
            .sidebar {
                box-shadow: none !important;
                padding: 20px;
                border-radius: 12px 12px 0 0;
                margin-bottom: 20px;
            }
            h1 {
                font-size: 2em;
                padding-top: 10px;
            }
            .saldo {
                font-size: 1.3em;
            }
        }
    </style>
</head>
<body>
    <div class="background-blur"></div>
    <h1>FelixBus - Painel de Controle</h1>
    
    <div class="dashboard" role="main" aria-label="Painel de controle do usuário">
        <aside class="sidebar" aria-label="Menu lateral de navegação">
            <h2>Menu</h2>
            <p>Bem-vindo, <strong><?= htmlspecialchars($nome) ?></strong></p>
            <p>Perfil: <strong><?= obterNomePerfil($idPerfil) ?></strong></p>
            
            <?php if (temPerfil(2) || temPerfil(3) || temPerfil(4)): ?>
                <p class="saldo" aria-live="polite" aria-atomic="true">Saldo: <?= number_format($saldo, 2, ',', '.') ?> €</p>
            <?php endif; ?>
            
            <hr>
            
            <!-- Menu comum a todos os perfis autenticados -->
            <ul>
                <li><a href="meus_dados.php">Meus Dados</a></li>
                <li><a href="logout.php">Terminar Sessão</a></li>
            </ul>
            
            <!-- Menu específico para clientes (ID 2) -->
            <?php if (temPerfil(2)): ?>
                <h3>Cliente</h3>
                <ul>
                    <li><a href="consultar_rotas.php">Consultar Rotas</a></li>
                    <li><a href="comprar_bilhete.php">Comprar Bilhetes</a></li>
                    <li><a href="meus_bilhetes.php">Meus Bilhetes</a></li>
                    <li><a href="carteira.php">Gerir Carteira</a></li>
                </ul>
            <?php endif; ?>
            
            <!-- Menu específico para funcionários (ID 3) -->
            <?php if (temPerfil(3)): ?>
                <h3>Funcionário</h3>
                <ul>
                    <li><a href="gerir_bilhetes.php">Gerir Bilhetes</a></li>
                    <li><a href="consultar_clientes.php">Consultar Clientes</a></li>
                    <li><a href="validar_bilhetes.php">Validar Bilhetes</a></li>
                </ul>
            <?php endif; ?>
            
            <!-- Menu específico para administradores (ID 4) -->
            <?php if (temPerfil(4)): ?>
                <h3>Administrador</h3>
                <ul>
                    <li><a href="gerir_utilizadores.php">Gerir Utilizadores</a></li>
                    <li><a href="gerir_rotas.php">Gerir Rotas</a></li>
                    <li><a href="gerir_alertas.php">Gerir Alertas</a></li>
                    <li><a href="relatorios.php">Relatórios</a></li>
                </ul>
            <?php endif; ?>
        </aside>
        
        <section class="content">
            <?php mostrarAlertas($ligacao); ?>
            
            <div class="card" aria-label="Resumo das funcionalidades do usuário">
                <h2>Resumo</h2>
                
                <?php if (temPerfil(2)): ?>
                    <p>Você tem acesso às funcionalidades de cliente:</p>
                    <ul>
                        <li>Consulta e compra de bilhetes</li>
                        <li>Gestão da sua carteira pessoal</li>
                        <li>Visualização dos seus bilhetes adquiridos</li>
                    </ul>
                <?php endif; ?>
                
                <?php if (temPerfil(3)): ?>
                    <p>Você tem acesso às funcionalidades de funcionário:</p>
                    <ul>
                        <li>Gestão de bilhetes para clientes</li>
                        <li>Consulta de informações de clientes</li>
                        <li>Validação de bilhetes</li>
                    </ul>
                <?php endif; ?>
                
                <?php if (temPerfil(4)): ?>
                    <p>Você tem acesso às funcionalidades de administrador:</p>
                    <ul>
                        <li>Gestão completa do sistema</li>
                        <li>Criação e edição de rotas</li>
                        <li>Administração de usuários</li>
                        <li>Publicação de alertas e promoções</li>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- Exibir últimos bilhetes para clientes -->
            <?php if (temPerfil(2)): ?>
                <div class="card" aria-label="Últimos bilhetes adquiridos">
                    <h3>Meus Últimos Bilhetes</h3>
                    <?php
                    $query = "SELECT r.origem, r.destino, r.dataViagem, r.hora 
                              FROM bilhete b 
                              JOIN rota r ON b.idRota = r.id 
                              WHERE b.idUtilizador = ? 
                              ORDER BY b.dataCompra DESC LIMIT 3";
                    $stmt = mysqli_prepare($ligacao, $query);
                    mysqli_stmt_bind_param($stmt, "i", $idUtilizador);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($bilhete = mysqli_fetch_assoc($result)) {
                            echo '<div class="bilhete-item">';
                            echo '<p><strong>Rota:</strong> '.htmlspecialchars($bilhete['origem']).' &rarr; '.htmlspecialchars($bilhete['destino']).'</p>';
                            echo '<p><strong>Data:</strong> '.date('d/m/Y', strtotime($bilhete['dataViagem'])).'</p>';
                            echo '<p><strong>Hora:</strong> '.date('H:i', strtotime($bilhete['hora'])).'</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>Nenhum bilhete recente encontrado.</p>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
