<?php
require_once __DIR__ . '/../inc/bootstrap.php';

if (($_SESSION['is_admin'] ?? false) === true) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $expectedUser = getenv('ADMIN_USERNAME') ?: 'admin';
    $passwordHash = getenv('ADMIN_PASSWORD_HASH') ?: '$2y$10$D85pk2NArXaMo1P8v6eUGODkvLhTs2aiaY.NnIHpvivh14Xo0V47i';
    if (!verify_csrf()) {
        $error = 'Vorm aegus. Palun proovi uuesti.';
    } elseif (hash_equals($expectedUser, $username) && password_verify($password, $passwordHash)) {
        session_regenerate_id(true);
        $_SESSION['is_admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Kasutajanimi või parool on vale.';
    }
}

$page_title = 'Admini sisselogimine - Autorent';
require __DIR__ . '/../inc/header.php';
?>
<main class="container py-5" style="max-width:520px">
  <h1 class="h2 mb-4">Admini sisselogimine</h1>
  <?php if ($error !== ''): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
  <form method="post" class="card card-body shadow-sm">
    <?= csrf_field() ?>
    <label class="form-label" for="username">Kasutajanimi</label>
    <input class="form-control mb-3" id="username" name="username" autocomplete="username" required>
    <label class="form-label" for="password">Parool</label>
    <input class="form-control mb-3" id="password" name="password" type="password" autocomplete="current-password" required>
    <button class="btn btn-dark" type="submit">Logi sisse</button>
  </form>
  <p class="small text-secondary mt-3">Kohaliku õppetöö vaikekonto: admin / admin. Muuda see enne avalikku kasutamist.</p>
</main>
<?php require __DIR__ . '/../inc/footer.php'; ?>
