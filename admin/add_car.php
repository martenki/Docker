<?php
require_once __DIR__ . '/../inc/bootstrap.php';
require_admin();

$error = '';
$fields = ['mark' => '', 'model' => '', 'engine' => '', 'fuel' => '', 'year' => (string)date('Y'), 'transmission' => 'Manuaal', 'seats' => '5', 'price' => '', 'image' => '', 'description' => '', 'status' => 'vaba'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $key => $_) {
        $fields[$key] = trim((string)($_POST[$key] ?? ''));
    }
    $year = filter_var($fields['year'], FILTER_VALIDATE_INT);
    $seats = filter_var($fields['seats'], FILTER_VALIDATE_INT);
    $price = filter_var($fields['price'], FILTER_VALIDATE_FLOAT);
    if (!verify_csrf()) {
        $error = 'Vorm aegus. Laadi leht uuesti ja proovi uuesti.';
    } elseif ($fields['mark'] === '' || $fields['model'] === '' || !$year || $year < 1900 || $year > 2100 || !$seats || $seats > 99 || $price === false || $price < 0 || !in_array($fields['status'], ['vaba', 'rendidud', 'hoolduses'], true)) {
        $error = 'Kontrolli kohustuslikke välju, aastat, istekohtade arvu, hinda ja olekut.';
    } else {
        $statement = $yhendus->prepare('INSERT INTO cars (mark, model, engine, fuel, `year`, transmission, seats, price, image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $statement->bind_param('ssssisidsss', $fields['mark'], $fields['model'], $fields['engine'], $fields['fuel'], $year, $fields['transmission'], $seats, $price, $fields['image'], $fields['description'], $fields['status']);
        $statement->execute();
        $_SESSION['admin_notice'] = 'Uus auto lisati.';
        header('Location: index.php');
        exit;
    }
}

$page_title = 'Lisa auto - Autorent';
require __DIR__ . '/../inc/header.php';
?>
<main class="container py-5" style="max-width:900px">
  <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="h2">Lisa auto</h1><a class="btn btn-outline-secondary" href="index.php">Tagasi haldusesse</a></div>
  <?php if ($error !== ''): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
  <form method="post" class="card card-body shadow-sm">
    <?= csrf_field() ?>
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Mark</label><input class="form-control" name="mark" value="<?= h($fields['mark']) ?>" required></div>
      <div class="col-md-6"><label class="form-label">Mudel</label><input class="form-control" name="model" value="<?= h($fields['model']) ?>" required></div>
      <div class="col-md-6"><label class="form-label">Mootor</label><input class="form-control" name="engine" value="<?= h($fields['engine']) ?>" placeholder="nt V6"></div>
      <div class="col-md-6"><label class="form-label">Kütus</label><input class="form-control" name="fuel" value="<?= h($fields['fuel']) ?>" placeholder="Bensiin, diisel, hübriid"></div>
      <div class="col-md-3"><label class="form-label">Aasta</label><input class="form-control" type="number" min="1900" max="2100" name="year" value="<?= h($fields['year']) ?>" required></div>
      <div class="col-md-3"><label class="form-label">Käigukast</label><input class="form-control" name="transmission" value="<?= h($fields['transmission']) ?>" required></div>
      <div class="col-md-3"><label class="form-label">Istekohti</label><input class="form-control" type="number" min="1" max="99" name="seats" value="<?= h($fields['seats']) ?>" required></div>
      <div class="col-md-3"><label class="form-label">Hind €/päev</label><input class="form-control" type="number" min="0" step="0.01" name="price" value="<?= h($fields['price']) ?>" required></div>
      <div class="col-12"><label class="form-label">Pildi URL (valikuline)</label><input class="form-control" type="url" name="image" value="<?= h($fields['image']) ?>" placeholder="https://..."></div>
      <div class="col-12"><label class="form-label">Kirjeldus</label><textarea class="form-control" name="description" rows="3"><?= h($fields['description']) ?></textarea></div>
      <div class="col-md-4"><label class="form-label">Olek</label><select class="form-select" name="status"><?php foreach (['vaba', 'rendidud', 'hoolduses'] as $status): ?><option value="<?= h($status) ?>" <?= $fields['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option><?php endforeach; ?></select></div>
      <div class="col-12"><button class="btn btn-dark" type="submit">Salvesta auto</button></div>
    </div>
  </form>
</main>
<?php require __DIR__ . '/../inc/footer.php'; ?>
