<?php
require_once __DIR__ . '/inc/bootstrap.php';

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if (!verify_csrf()) {
        $error = 'Vorm aegus. Palun proovi uuesti.';
    } else {
        $statement = $yhendus->prepare('SELECT id, first_name, password_hash FROM users WHERE email = ?');
        $statement->bind_param('s', $email);
        $statement->execute();
        $user = $statement->get_result()->fetch_assoc();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['first_name'];
            header('Location: index.php');
            exit;
        }
        $error = 'E-posti aadress või parool ei ole õige.';
    }
}

$page_title = 'Logi sisse - Autorent';
require __DIR__ . '/inc/header.php';
?>
<main class="container py-5" style="max-width:520px">
  <h1 class="h2 mb-4">Logi sisse</h1>
  <?php if ($error !== ''): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
  <form method="post" class="card card-body shadow-sm">
    <?= csrf_field() ?>
    <label class="form-label" for="email">E-post</label>
    <input class="form-control mb-3" id="email" name="email" type="email" value="<?= h($email) ?>" autocomplete="email" required>
    <label class="form-label" for="password">Parool</label>
    <input class="form-control mb-3" id="password" name="password" type="password" autocomplete="current-password" required>
    <button class="btn btn-dark" type="submit">Logi sisse</button>
  </form>
  <p class="mt-3">Uus kasutaja? <a href="regamine.php">Loo konto</a>.</p>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
