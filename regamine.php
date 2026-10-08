<?php
require_once __DIR__ . '/inc/bootstrap.php';

$errors = [];
$values = ['first_name' => '', 'last_name' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $_) {
        $values[$key] = trim((string)($_POST[$key] ?? ''));
    }
    $password = (string)($_POST['password'] ?? '');

    if (!verify_csrf()) {
        $errors[] = 'Vorm aegus. Palun proovi uuesti.';
    } elseif ($values['first_name'] === '' || $values['last_name'] === '' || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Sisesta ees- ja perekonnanimi ning korrektne e-posti aadress.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Parool peab olema vähemalt 8 märki pikk.';
    } else {
        $statement = $yhendus->prepare('INSERT INTO users (first_name, last_name, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)');
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $statement->bind_param('sssss', $values['first_name'], $values['last_name'], $values['email'], $values['phone'], $passwordHash);
        try {
            $statement->execute();
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$yhendus->insert_id;
            $_SESSION['user_name'] = $values['first_name'];
            header('Location: index.php');
            exit;
        } catch (mysqli_sql_exception $exception) {
            if ($exception->getCode() === 1062) {
                $errors[] = 'Selle e-posti aadressiga konto on juba olemas.';
            } else {
                error_log('Konto loomine ebaõnnestus: ' . $exception->getMessage());
                $errors[] = 'Konto loomine ebaõnnestus. Palun proovi hiljem uuesti.';
            }
        }
    }
}

$page_title = 'Loo kasutajakonto - Autorent';
require __DIR__ . '/inc/header.php';
?>
<main class="container py-5" style="max-width:640px">
  <h1 class="h2 mb-4">Loo kasutajakonto</h1>
  <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endforeach; ?>
  <form method="post" class="card card-body shadow-sm">
    <?= csrf_field() ?>
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label" for="first_name">Eesnimi</label><input class="form-control" id="first_name" name="first_name" value="<?= h($values['first_name']) ?>" autocomplete="given-name" required></div>
      <div class="col-md-6"><label class="form-label" for="last_name">Perekonnanimi</label><input class="form-control" id="last_name" name="last_name" value="<?= h($values['last_name']) ?>" autocomplete="family-name" required></div>
      <div class="col-12"><label class="form-label" for="email">E-post</label><input class="form-control" id="email" name="email" type="email" value="<?= h($values['email']) ?>" autocomplete="email" required></div>
      <div class="col-12"><label class="form-label" for="phone">Telefon</label><input class="form-control" id="phone" name="phone" type="tel" value="<?= h($values['phone']) ?>" autocomplete="tel"></div>
      <div class="col-12"><label class="form-label" for="password">Parool (vähemalt 8 märki)</label><input class="form-control" id="password" name="password" type="password" minlength="8" autocomplete="new-password" required></div>
      <div class="col-12"><button class="btn btn-dark" type="submit">Loo konto</button> <a href="login.php">Mul on konto juba olemas</a></div>
    </div>
  </form>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
