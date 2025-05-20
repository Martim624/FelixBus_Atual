<?php
session_start();
require_once("../basededados/basedados.h");

// Verifica autenticação e perfil (só admin/funcionário podem acessar)
if (!isset($_SESSION['id']) || !isset($_SESSION['idPerfil']) || ($_SESSION['idPerfil'] != 3 && $_SESSION['idPerfil'] != 4)) {
    header("Location: login.php");
    exit();
}

// Obter dados do usuário
$idUtilizador = $_SESSION['id'];
$idPerfil = $_SESSION['idPerfil'];
$nome = $_SESSION['username'];

// Parâmetros de filtro da URL
$filtroOperacao = $_GET['operacao'] ?? '';
$filtroDataInicio = $_GET['data_inicio'] ?? '';
$filtroDataFim = $_GET['data_fim'] ?? '';
$ordenacao = $_GET['ordenacao'] ?? 'dataOperacao DESC';

// Construir a query base
$query = "SELECT 
            a.id, 
            a.operacao, 
            a.valor, 
            a.idOrigem, 
            u1.username as origem_nome,
            a.idDestino, 
            u2.username as destino_nome,
            a.descricao,
            a.dataOperacao
          FROM auditoria a
          LEFT JOIN utilizador u1 ON a.idOrigem = u1.id
          LEFT JOIN utilizador u2 ON a.idDestino = u2.id";

// Adicionar filtros
if (!empty($filtroOperacao)) {
    $query .= " AND a.operacao = '" . mysqli_real_escape_string($ligacao, $filtroOperacao) . "'";
}

if (!empty($filtroDataInicio)) {
    $query .= " AND a.dataOperacao >= '" . mysqli_real_escape_string($ligacao, $filtroDataInicio) . " 00:00:00'";
}

if (!empty($filtroDataFim)) {
    $query .= " AND a.dataOperacao <= '" . mysqli_real_escape_string($ligacao, $filtroDataFim) . " 23:59:59'";
}

// Adicionar ordenação
$query .= " ORDER BY " . mysqli_real_escape_string($ligacao, $ordenacao);

// Executar query
$result = mysqli_query($ligacao, $query);

// Obter operações distintas para o filtro
$queryOperacoes = "SELECT DISTINCT operacao FROM auditoria ORDER BY operacao";
$resultOperacoes = mysqli_query($ligacao, $queryOperacoes);
$operacoes = [];
while ($row = mysqli_fetch_assoc($resultOperacoes)) {
    $operacoes[] = $row['operacao'];
}

// Funções auxiliares
function temPerfil($idPerfilRequerido) {
    return $_SESSION['idPerfil'] == $idPerfilRequerido;
}

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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Auditoria | FelixBus</title>
    <link rel="stylesheet" href="style.css" />

</head>
<body>
    <div class="background-blur"></div>
    <h1>FelixBus - Auditoria</h1>

    <div class="dashboard" role="main" aria-label="Página de auditoria">
        <aside class="sidebar" aria-label="Menu lateral de navegação">
            <h2>Menu</h2>
            <p>Bem-vindo, <strong><?= htmlspecialchars($nome) ?></strong></p>
            <p>Perfil: <strong><?= obterNomePerfil($idPerfil) ?></strong></p>

            <?php if (temPerfil(2) || temPerfil(3) || temPerfil(4)): ?>
                <?php
                $querySaldo = "SELECT saldo FROM carteira WHERE idUtilizador = ?";
                $stmtSaldo = mysqli_prepare($ligacao, $querySaldo);
                mysqli_stmt_bind_param($stmtSaldo, "i", $idUtilizador);
                mysqli_stmt_execute($stmtSaldo);
                $resultSaldo = mysqli_stmt_get_result($stmtSaldo);
                $carteira = mysqli_fetch_assoc($resultSaldo);
                $saldo = $carteira['saldo'] ?? 0.00;
                ?>
                <p class="saldo">Saldo: <?= number_format($saldo, 2, ',', '.') ?> €</p>
            <?php endif; ?>

            <hr>

            <ul>
                <li><a href="meus_dados.php">Meus Dados</a></li>
                <li><a href="logout.php">Terminar Sessão</a></li>
            </ul>

            <?php if (temPerfil(2)): ?>
                <h3>Cliente</h3>
                <ul>
                    <li><a href="rotas.php">Consultar Rotas</a></li>
                    <li><a href="meus_bilhetes.php">Meus Bilhetes</a></li>
                    <li><a href="carteira.php">Gerir Carteira</a></li>
                </ul>
            <?php endif; ?>

            <?php if (temPerfil(3)): ?>
                <h3>Funcionário</h3>
                <ul>
                    <li><a href="gerir_bilhetes.php">Gerir Bilhetes</a></li>
                    <li><a href="consultar_clientes.php">Consultar Clientes</a></li>
                    <li><a href="validar_bilhetes.php">Validar Bilhetes</a></li>
                    <li><a href="auditoria.php" class="active">Auditoria</a></li>
                </ul>
            <?php endif; ?>

            <?php if (temPerfil(4)): ?>
                <h3>Administrador</h3>
                <ul>
                    <li><a href="gerir_utilizadores.php">Gerir Utilizadores</a></li>
                    <li><a href="gerir_rotas.php">Gerir Rotas</a></li>
                    <li><a href="gerir_alertas.php">Gerir Alertas</a></li>
                    <li><a href="relatorios.php">Relatórios</a></li>
                    <li><a href="auditoria.php" class="active">Auditoria</a></li>
                </ul>
            <?php endif; ?>
        </aside>

        <section class="content">
            <h2>Registos de Auditoria</h2>
            
            <!-- Formulário de Filtros -->
            <div class="filtros">
                <form method="get" action="auditoria.php">
                    <div class="form-group">
                        <label for="operacao">Operação:</label>
                        <select name="operacao" id="operacao">
                            <option value="">Todas</option>
                            <?php foreach ($operacoes as $op): ?>
                                <option value="<?= htmlspecialchars($op) ?>" <?= $filtroOperacao == $op ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($op) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_inicio">Data Início:</label>
                        <input type="date" name="data_inicio" id="data_inicio" value="<?= htmlspecialchars($filtroDataInicio) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="data_fim">Data Fim:</label>
                        <input type="date" name="data_fim" id="data_fim" value="<?= htmlspecialchars($filtroDataFim) ?>">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit">Aplicar Filtros</button>
                        <a href="auditoria.php" class="btn-limpar">Limpar Filtros</a>
                    </div>
                </form>
            </div>
            
            <!-- Tabela de Resultados -->
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <table class="tabela-auditoria" aria-label="Tabela de registos de auditoria">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>
                                <a href="?operacao=<?= $filtroOperacao ?>&data_inicio=<?= $filtroDataInicio ?>&data_fim=<?= $filtroDataFim ?>&ordenacao=operacao <?= strpos($ordenacao, 'operacao ASC') !== false ? 'DESC' : 'ASC' ?>" 
                                   class="link-ordenacao">
                                   Operação <?= strpos($ordenacao, 'operacao') !== false ? (strpos($ordenacao, 'ASC') !== false ? '↑' : '↓') : '' ?>
                                </a>
                            </th>
                            <th>
                                <a href="?operacao=<?= $filtroOperacao ?>&data_inicio=<?= $filtroDataInicio ?>&data_fim=<?= $filtroDataFim ?>&ordenacao=valor <?= strpos($ordenacao, 'valor ASC') !== false ? 'DESC' : 'ASC' ?>" 
                                   class="link-ordenacao">
                                   Valor <?= strpos($ordenacao, 'valor') !== false ? (strpos($ordenacao, 'ASC') !== false ? '↑' : '↓') : '' ?>
                                </a>
                            </th>
                            <th>Origem</th>
                            <th>Destino</th>
                            <th>Descrição</th>
                            <th>
                                <a href="?operacao=<?= $filtroOperacao ?>&data_inicio=<?= $filtroDataInicio ?>&data_fim=<?= $filtroDataFim ?>&ordenacao=dataOperacao <?= strpos($ordenacao, 'dataOperacao ASC') !== false ? 'DESC' : 'ASC' ?>" 
                                   class="link-ordenacao">
                                   Data/Hora <?= strpos($ordenacao, 'dataOperacao') !== false ? (strpos($ordenacao, 'ASC') !== false ? '↑' : '↓') : '' ?>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['operacao']) ?></td>
                                <td><?= $row['valor'] ? number_format($row['valor'], 2, ',', '.') . ' €' : '-' ?></td>
                                <td>
                                    <?= $row['origem_nome'] ? htmlspecialchars($row['origem_nome']) . ' (#' . $row['idOrigem'] . ')' : ($row['idOrigem'] ? '#' . $row['idOrigem'] : '-') ?>
                                </td>
                                <td>
                                    <?= $row['destino_nome'] ? htmlspecialchars($row['destino_nome']) . ' (#' . $row['idDestino'] . ')' : ($row['idDestino'] ? '#' . $row['idDestino'] : '-') ?>
                                </td>
                                <td><?= htmlspecialchars($row['descricao']) ?></td>
                                <td><?= htmlspecialchars($row['dataOperacao']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum registo de auditoria encontrado com os filtros aplicados.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>