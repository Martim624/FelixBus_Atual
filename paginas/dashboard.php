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
    <link rel="stylesheet" href="style.css">
    <title>Dashboard | FelixBus</title>
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
                    <li><a href="rotas.php">Consultar Rotas</a></li>
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
