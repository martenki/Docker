<?php
require_once __DIR__ . '/../inc/bootstrap.php';
require_admin();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Vorm aegus. Laadi leht uuesti ja proovi uuesti.';
    } elseif (isset($_POST['delete_id'])) {
        $id = filter_var($_POST['delete_id'], FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            $error = 'Valitud auto ID ei ole korrektne.';
        } else {
            try {
                $statement = $yhendus->prepare('DELETE FROM cars WHERE id = ?');
                $statement->bind_param('i', $id);
                $statement->execute();
                $_SESSION['admin_notice'] = $statement->affected_rows ? 'Auto kustutati.' : 'Autot ei leitud.';
                header('Location: index.php');
                exit;
            } catch (mysqli_sql_exception $exception) {
                if ($exception->getCode() === 1451) {
                    $error = 'Autol on broneeringuid, seega ei saa seda kustutada.';
                } else {
                    error_log('Auto kustutamine ebaõnnestus: ' . $exception->getMessage());
                    $error = 'Auto kustutamine ebaõnnestus.';
                }
            }
        }
    } elseif (isset($_POST['update_id'])) {
        $id = filter_var($_POST['update_id'], FILTER_VALIDATE_INT);
        $mark = trim((string)($_POST['mark'] ?? ''));
        $model = trim((string)($_POST['model'] ?? ''));
        $engine = trim((string)($_POST['engine'] ?? ''));
        $fuel = trim((string)($_POST['fuel'] ?? ''));
        $year = filter_var($_POST['year'] ?? null, FILTER_VALIDATE_INT);
        $transmission = trim((string)($_POST['transmission'] ?? ''));
        $seats = filter_var($_POST['seats'] ?? null, FILTER_VALIDATE_INT);
        $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
        $image = trim((string)($_POST['image'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $status = (string)($_POST['status'] ?? '');

        if (!$id || $mark === '' || $model === '' || $year < 1900 || $year > 2100 || $seats < 1 || $seats > 99 || $price === false || $price < 0 || !in_array($status, ['vaba', 'rendidud', 'hoolduses'], true)) {
            $error = 'Kontrolli kohustuslikke välju, hinda ja auto olekut.';
        } else {
            $statement = $yhendus->prepare('UPDATE cars SET mark=?, model=?, engine=?, fuel=?, `year`=?, transmission=?, seats=?, price=?, image=?, description=?, status=? WHERE id=?');
            $statement->bind_param('ssssisidsssi', $mark, $model, $engine, $fuel, $year, $transmission, $seats, $price, $image, $description, $status, $id);
            $statement->execute();
            $_SESSION['admin_notice'] = 'Auto andmed salvestati.';
            header('Location: index.php');
            exit;
        }
    }
}

$notice = (string)($_SESSION['admin_notice'] ?? '');
unset($_SESSION['admin_notice']);
$cars = $yhendus->query('SELECT * FROM cars ORDER BY id DESC');
$editId = (int)($_GET['edit_id'] ?? 0);
$page_title = 'Autode haldus - Autorent';
require __DIR__ . '/../inc/header.php';
?>
<main class="container py-5">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><h1 class="h2 mb-1">Autode haldus</h1><p class="text-secondary mb-0">Lisa, muuda ja halda autorendi sõidukeid.</p></div>
    <div class="d-flex gap-2"><a class="btn btn-dark" href="add_car.php">+ Lisa auto</a><a class="btn btn-outline-secondary" href="logout.php">Administ välja</a></div>
  </div>
  <?php if ($notice !== ''): ?><div class="alert alert-success"><?= h($notice) ?></div><?php endif; ?>
  <?php if ($error !== ''): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
  <div class="table-responsive card shadow-sm">
    <table class="table table-striped align-middle mb-0">
      <thead><tr><th>Auto</th><th>Aasta</th><th>Mootor / kütus</th><th>Hind päevas</th><th>Olek</th><th>Tegevused</th></tr></thead>
      <tbody>
      <?php while ($car = $cars->fetch_assoc()): ?>
        <?php if ($editId === (int)$car['id']): ?>
          <tr><td colspan="6">
            <form method="post" class="row g-2 align-items-end">
              <?= csrf_field() ?>
              <input type="hidden" name="update_id" value="<?= (int)$car['id'] ?>">
              <div class="col-md-3"><label class="form-label">Mark</label><input class="form-control" name="mark" value="<?= h($car['mark']) ?>" required></div>
              <div class="col-md-3"><label class="form-label">Mudel</label><input class="form-control" name="model" value="<?= h($car['model']) ?>" required></div>
              <div class="col-md-2"><label class="form-label">Mootor</label><input class="form-control" name="engine" value="<?= h($car['engine']) ?>"></div>
              <div class="col-md-2"><label class="form-label">Kütus</label><input class="form-control" name="fuel" value="<?= h($car['fuel']) ?>"></div>
              <div class="col-md-2"><label class="form-label">Aasta</label><input class="form-control" name="year" type="number" min="1900" max="2100" value="<?= h($car['year']) ?>" required></div>
              <div class="col-md-2"><label class="form-label">Käigukast</label><input class="form-control" name="transmission" value="<?= h($car['transmission']) ?>" required></div>
              <div class="col-md-2"><label class="form-label">Istekohti</label><input class="form-control" name="seats" type="number" min="1" max="99" value="<?= h($car['seats']) ?>" required></div>
              <div class="col-md-2"><label class="form-label">Hind €/päev</label><input class="form-control" name="price" type="number" min="0" step="0.01" value="<?= h($car['price']) ?>" required></div>
              <div class="col-md-6"><label class="form-label">Pildi URL</label><input class="form-control" name="image" value="<?= h($car['image']) ?>"></div>
              <div class="col-md-8"><label class="form-label">Kirjeldus</label><input class="form-control" name="description" value="<?= h($car['description']) ?>"></div>
              <div class="col-md-2"><label class="form-label">Olek</label><select class="form-select" name="status"><?php foreach (['vaba', 'rendidud', 'hoolduses'] as $status): ?><option value="<?= h($status) ?>" <?= $car['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option><?php endforeach; ?></select></div>
              <div class="col-md-2"><button class="btn btn-success" type="submit">Salvesta</button> <a class="btn btn-outline-secondary" href="index.php">Tühista</a></div>
            </form>
          </td></tr>
        <?php else: ?>
          <tr>
            <td><div class="d-flex align-items-center gap-2"><img src="<?= h(car_image($car)) ?>" alt="" width="72" height="48" class="rounded object-fit-cover"><span><?= h($car['mark'] . ' ' . $car['model']) ?></span></div></td>
            <td><?= h($car['year']) ?></td><td><?= h($car['engine'] . ' · ' . $car['fuel']) ?></td>
            <td><?= number_format((float)$car['price'], 2, ',', ' ') ?> €</td><td><?= h($car['status']) ?></td>
            <td><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="?edit_id=<?= (int)$car['id'] ?>">Muuda</a>
              <form method="post" onsubmit="return confirm('Kas kustutad selle auto?')"><?= csrf_field() ?><input type="hidden" name="delete_id" value="<?= (int)$car['id'] ?>"><button class="btn btn-sm btn-outline-danger" type="submit">Kustuta</button></form>
            </div></td>
          </tr>
        <?php endif; ?>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>
<?php require __DIR__ . '/../inc/footer.php'; ?>
