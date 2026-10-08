<!doctype html>
<html lang="et">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($page_title ?? 'Autorent') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Autorent</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navigation" aria-controls="main-navigation" aria-expanded="false" aria-label="Ava menüü">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="main-navigation">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Avaleht</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#autod">Autod</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#hinnad">Hinnad</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#kontakt">Kontakt</a></li>
      </ul>
      <div class="d-flex gap-2 align-items-center">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="text-secondary small"><?= h($_SESSION['user_name'] ?? 'Kasutaja') ?></span>
          <a class="btn btn-outline-secondary btn-sm" href="logout.php">Logi välja</a>
        <?php else: ?>
          <a class="btn btn-outline-dark btn-sm" href="regamine.php">Loo konto</a>
          <a class="btn btn-dark btn-sm" href="login.php">Logi sisse</a>
        <?php endif; ?>
        <?php if (($_SESSION['is_admin'] ?? false) === true): ?>
          <a class="btn btn-warning btn-sm" href="admin/index.php">Haldus</a>
        <?php else: ?>
          <a class="btn btn-outline-secondary btn-sm" href="admin/login.php">Admin</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
