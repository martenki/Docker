<?php
require_once __DIR__ . '/inc/bootstrap.php';

$carId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$carId || $carId < 1) {
    header('Location: index.php');
    exit;
}

$statement = $yhendus->prepare('SELECT * FROM cars WHERE id = ?');
$statement->bind_param('i', $carId);
$statement->execute();
$car = $statement->get_result()->fetch_assoc();
if (!$car) {
    http_response_code(404);
    $page_title = 'Autot ei leitud';
    require __DIR__ . '/inc/header.php';
    echo '<main class="container py-5"><h1>Autot ei leitud</h1><p>Valitud autot pole enam saadaval.</p><a href="index.php">Tagasi autode juurde</a></main>';
    require __DIR__ . '/inc/footer.php';
    exit;
}

$message = '';
$messageType = 'danger';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $message = 'Vorm aegus. Palun laadi leht uuesti ja proovi uuesti.';
    } elseif (!isset($_SESSION['user_id'])) {
        $message = 'Broneerimiseks logi esmalt sisse või loo kasutajakonto.';
    } elseif ($car['status'] !== 'vaba') {
        $message = 'See auto ei ole praegu renditav.';
    } else {
        $startDate = (string)($_POST['start_date'] ?? '');
        $endDate = (string)($_POST['end_date'] ?? '');
        $start = DateTimeImmutable::createFromFormat('!Y-m-d', $startDate);
        $end = DateTimeImmutable::createFromFormat('!Y-m-d', $endDate);
        $today = new DateTimeImmutable('today');
        $validStart = $start && $start->format('Y-m-d') === $startDate;
        $validEnd = $end && $end->format('Y-m-d') === $endDate;

        if (!$validStart || !$validEnd || $start < $today || $end < $start) {
            $message = 'Vali kehtiv kuupäevavahemik, mis algab täna või hiljem ja lõpeb alguskuupäeval või pärast seda.';
        } else {
            $days = (int)$start->diff($end)->days + 1;
            $total = $days * (float)$car['price'];
            try {
                $yhendus->begin_transaction();
                $lockCar = $yhendus->prepare('SELECT status FROM cars WHERE id = ? FOR UPDATE');
                $lockCar->bind_param('i', $carId);
                $lockCar->execute();
                $lockedCar = $lockCar->get_result()->fetch_assoc();

                if (!$lockedCar || $lockedCar['status'] !== 'vaba') {
                    $yhendus->rollback();
                    $message = 'See auto ei ole praegu renditav.';
                } else {
                    $overlap = $yhendus->prepare("SELECT id FROM reservations WHERE car_id = ? AND status = 'active' AND start_date <= ? AND end_date >= ? LIMIT 1 FOR UPDATE");
                    $overlap->bind_param('iss', $carId, $endDate, $startDate);
                    $overlap->execute();
                    if ($overlap->get_result()->num_rows > 0) {
                        $yhendus->rollback();
                        $message = 'Valitud kuupäevad kattuvad olemasoleva broneeringuga. Palun vali teine periood.';
                    } else {
                        $insert = $yhendus->prepare("INSERT INTO reservations (user_id, car_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, 'active')");
                        $userId = (int)$_SESSION['user_id'];
                        $insert->bind_param('iissd', $userId, $carId, $startDate, $endDate, $total);
                        $insert->execute();
                        $yhendus->commit();
                        $messageType = 'success';
                        $message = sprintf('Broneering on kinnitatud. %d päeva · kokku %.2f €.', $days, $total);
                    }
                }
            } catch (mysqli_sql_exception $exception) {
                $yhendus->rollback();
                error_log('Broneeringu salvestamine ebaõnnestus: ' . $exception->getMessage());
                $message = 'Broneeringut ei saanud salvestada. Palun proovi hiljem uuesti.';
            }
        }
    }
}

$page_title = $car['mark'] . ' ' . $car['model'] . ' - Autorent';
require __DIR__ . '/inc/header.php';
?>
<main class="container py-5">
  <a href="index.php#autod" class="link-secondary">← Tagasi autode juurde</a>
  <?php if ($message !== ''): ?>
    <div class="alert alert-<?= h($messageType) ?> mt-3" role="alert"><?= h($message) ?></div>
  <?php endif; ?>
  <div class="row g-4 mt-1">
    <div class="col-lg-7">
      <img src="<?= h(car_image($car)) ?>" class="img-fluid rounded-3 w-100" alt="<?= h($car['mark'] . ' ' . $car['model']) ?>">
    </div>
    <div class="col-lg-5">
      <h1><?= h($car['mark'] . ' ' . $car['model']) ?></h1>
      <p class="lead"><?= h($car['description'] ?: 'Sobib mugavaks ja turvaliseks sõiduks.') ?></p>
      <dl class="row">
        <dt class="col-6">Aasta</dt><dd class="col-6"><?= h($car['year']) ?></dd>
        <dt class="col-6">Mootor</dt><dd class="col-6"><?= h($car['engine']) ?></dd>
        <dt class="col-6">Käigukast</dt><dd class="col-6"><?= h($car['transmission']) ?></dd>
        <dt class="col-6">Kütus</dt><dd class="col-6"><?= h($car['fuel']) ?></dd>
        <dt class="col-6">Istekohti</dt><dd class="col-6"><?= h($car['seats']) ?></dd>
        <dt class="col-6">Hind päevas</dt><dd class="col-6 fw-bold"><?= number_format((float)$car['price'], 2, ',', ' ') ?> €</dd>
      </dl>
      <?php if ($car['status'] !== 'vaba'): ?>
        <div class="alert alert-secondary">See auto ei ole praegu renditav.</div>
      <?php elseif (!isset($_SESSION['user_id'])): ?>
        <div class="alert alert-info">Broneeringu tegemiseks <a href="login.php">logi sisse</a> või <a href="regamine.php">loo konto</a>.</div>
      <?php else: ?>
        <form method="post" class="card card-body bg-light">
          <?= csrf_field() ?>
          <h2 class="h5">Vali rendiperiood</h2>
          <label class="form-label" for="start_date">Alguskuupäev</label>
          <input class="form-control mb-3" id="start_date" type="date" name="start_date" min="<?= h(date('Y-m-d')) ?>" value="<?= h($_POST['start_date'] ?? '') ?>" required>
          <label class="form-label" for="end_date">Lõppkuupäev</label>
          <input class="form-control mb-3" id="end_date" type="date" name="end_date" min="<?= h(date('Y-m-d')) ?>" value="<?= h($_POST['end_date'] ?? '') ?>" required>
          <p class="small text-secondary">Algus- ja lõppkuupäev arvestatakse mõlemad rendipäevadena.</p>
          <button class="btn btn-dark" type="submit">Arvuta hind ja broneeri</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php require __DIR__ . '/inc/footer.php'; ?>
