<?php
include("../basededados/basedados.h");
session_start();

// Check if user is logged in and is administrator
if (!isset($_SESSION['id']) || !isset($_SESSION['idPerfil']) || $_SESSION['idPerfil'] != 4) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;
$errors = [];
$success = '';

// Function to get profile name by id
function obterNomePerfil($idPerfil) {
    switch($idPerfil) {
        case 1: return 'Visitante';
        case 2: return 'Cliente';
        case 3: return 'Funcionário';
        case 4: return 'Administrador';
        default: return 'Desconhecido';
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $idPerfil = $_POST['idPerfil'] ?? '';
    $idUser = $_POST['idUser'] ?? null;

    // Validate inputs
    if ($username === '') $errors[] = "Nome de utilizador é obrigatório.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
    if (!in_array($idPerfil, ['1','2','3','4'])) $errors[] = "Perfil inválido.";
    if ($action === 'edit' && $idUser === null) $errors[] = "ID do utilizador inválido.";

    if (empty($errors)) {
        if ($action === 'add') {
            // Check if username or email already exists
            $stmt = mysqli_prepare($ligacao, "SELECT id FROM utilizador WHERE username=? OR email=?");
            mysqli_stmt_bind_param($stmt, "ss", $username, $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors[] = "Nome de utilizador ou email já existe.";
            }
            mysqli_stmt_close($stmt);

            if (empty($errors)) {
                // Insert new user without password
                $stmt = mysqli_prepare($ligacao, "INSERT INTO utilizador (username, email, idPerfil) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $idPerfil);
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Utilizador adicionado com sucesso.";
                    $action = '';
                } else {
                    $errors[] = "Erro ao adicionar utilizador: " . mysqli_error($ligacao);
                }
                mysqli_stmt_close($stmt);
            }
        } elseif ($action === 'edit') {
            // Update existing user without password
            $stmt = mysqli_prepare($ligacao, "UPDATE utilizador SET username=?, email=?, idPerfil=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssii", $username, $email, $idPerfil, $idUser);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Utilizador atualizado com sucesso.";
                $action = '';
            } else {
                $errors[] = "Erro ao atualizar utilizador: " . mysqli_error($ligacao);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle delete action
if ($action === 'delete' && $id) {
    $stmt = mysqli_prepare($ligacao, "DELETE FROM utilizador WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Utilizador eliminado com sucesso.";
        $action = '';
    } else {
        $errors[] = "Erro ao eliminar utilizador: " . mysqli_error($ligacao);
    }
    mysqli_stmt_close($stmt);
}

// Fetch users for listing or for editing form
if ($action === 'edit' && $id) {
    $stmt = mysqli_prepare($ligacao, "SELECT * FROM utilizador WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    $user = null;
}

$sql = "SELECT id, username, email, idPerfil FROM utilizador ORDER BY username";
$resultado = mysqli_query($ligacao, $sql);
$users = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerir Utilizadores - FelixBus</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="image/png" href="logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="body-secondary">
<nav>
    <a href="index.php" class="logo">FelixBus</a>
    <div class="menu">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="gerir_utilizadores.php" class="active"><i class="fas fa-users"></i> Gerir Utilizadores</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Terminar Sessão</a>
    </div>
</nav>

<div class="container">
    <h1>Gerir Utilizadores</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $erro): ?>
                    <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($action === 'add' || $action === 'edit'): ?>
        <form method="post" action="gerir_utilizadores.php?action=<?= $action ?><?= $action === 'edit' ? '&id=' . htmlspecialchars($id) : '' ?>">
            <input type="hidden" name="idUser" value="<?= $user['id'] ?? '' ?>">

            <label for="username">Nome de Utilizador:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

            <label for="idPerfil">Perfil:</label>
            <select id="idPerfil" name="idPerfil" required>
                <option value="1" <?= (isset($user['idPerfil']) && $user['idPerfil'] == 1) ? 'selected' : '' ?>>Visitante</option>
                <option value="2" <?= (isset($user['idPerfil']) && $user['idPerfil'] == 2) ? 'selected' : '' ?>>Cliente</option>
                <option value="3" <?= (isset($user['idPerfil']) && $user['idPerfil'] == 3) ? 'selected' : '' ?>>Funcionário</option>
                <option value="4" <?= (isset($user['idPerfil']) && $user['idPerfil'] == 4) ? 'selected' : '' ?>>Administrador</option>
            </select>

            <button type="submit" class="btn btn-primary"><?= $action === 'add' ? 'Adicionar' : 'Atualizar' ?> Utilizador</button>
            <a href="gerir_utilizadores.php" class="btn btn-secondary">Cancelar</a>
        </form>
    <?php else: ?>
        <a href="gerir_utilizadores.php?action=add" class="btn btn-primary">Adicionar Novo Utilizador</a>

        <table class="rotas-table">
            <thead>
                <tr>
                    <th>Nome de Utilizador</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= obterNomePerfil($u['idPerfil']) ?></td>
                        <td>
                            <a href="gerir_utilizadores.php?action=edit&id=<?= $u['id'] ?>" class="btn btn-secondary">Editar</a>
                            <a href="gerir_utilizadores.php?action=delete&id=<?= $u['id'] ?>" class="btn btn-danger" onclick="return confirm('Tem a certeza que deseja eliminar este utilizador?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<footer class="footer-transparent">
    &copy; <?= date("Y") ?> FelixBus. Todos os direitos reservados.
</footer>

<!-- FontAwesome for icons -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
