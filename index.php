<?php
require_once __DIR__ . '/inc/bootstrap.php';

$query = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 8;

if ($query === '') {
    $countResult = $yhendus->query("SELECT COUNT(*) AS total FROM cars WHERE status = 'vaba'");
    $totalCars = (int)$countResult->fetch_assoc()['total'];
    $statement = $yhendus->prepare("SELECT * FROM cars WHERE status = 'vaba' ORDER BY id DESC LIMIT ? OFFSET ?");
    $offset = ($page - 1) * $perPage;
    $statement->bind_param('ii', $perPage, $offset);
} else {
    $like = '%' . $query . '%';
    $countStatement = $yhendus->prepare("SELECT COUNT(*) AS total FROM cars WHERE status = 'vaba' AND (mark LIKE ? OR model LIKE ?)");
    $countStatement->bind_param('ss', $like, $like);
    $countStatement->execute();
    $totalCars = (int)$countStatement->get_result()->fetch_assoc()['total'];

    $statement = $yhendus->prepare("SELECT * FROM cars WHERE status = 'vaba' AND (mark LIKE ? OR model LIKE ?) ORDER BY id DESC LIMIT ? OFFSET ?");
    $offset = ($page - 1) * $perPage;
    $statement->bind_param('ssii', $like, $like, $perPage, $offset);
}
$totalPages = max(1, (int)ceil($totalCars / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
    if ($query === '') {
        $statement->bind_param('ii', $perPage, $offset);
    } else {
        $statement->bind_param('ssii', $like, $like, $perPage, $offset);
    }
}
$statement->execute();
$cars = $statement->get_result();

$page_title = 'Autorent - leia endale sobiv auto';
require __DIR__ . '/inc/header.php';
?>

<main>
  <section class="container py-4">
    <div class="rounded-4 bg-dark text-white p-4 p-lg-5">
      <div class="row align-items-center g-4">
        <div class="col-lg-5">
          <p class="text-uppercase small fw-semibold text-warning mb-2">Sinu järgmine sõit algab siit</p>
          <h1 class="display-5 fw-bold">Rendi auto lihtsalt ja soodsalt</h1>
          <p class="lead text-white-50">Vali sobiv sõiduk ning broneeri endale vajalik aeg.</p>
          <a class="btn btn-warning btn-lg" href="#autod">Vaata autosid</a>
        </div>
        <div class="col-lg-7">
          <img class="img-fluid rounded-3 w-100" src="https://loremflickr.com/1100/550/cars" alt="Autorendi autod">
        </div>
      </div>
    </div>
  </section>

  <section class="container py-4" id="autod">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
      <div>
        <h2 class="h3 mb-1">Saadaolevad autod</h2>
        <p class="text-secondary mb-0"><?= $totalCars ?> autot</p>
      </div>
      <form class="d-flex gap-2" method="get" action="index.php" role="search">
        <label class="visually-hidden" for="car-search">Otsi automarki või mudelit</label>
        <input class="form-control" id="car-search" type="search" name="q" value="<?= h($query) ?>" placeholder="Mark või mudel">
        <button class="btn btn-outline-dark" type="submit">Otsi</button>
      </form>
    </div>

    <?php if ($totalCars === 0): ?>
      <div class="alert alert-info">Sobivaid autosid ei leitud. Proovi teist otsingusõna.</div>
    <?php else: ?>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
        <?php while ($car = $cars->fetch_assoc()): ?>
          <div class="col">
            <article class="card h-100 shadow-sm">
              <img src="<?= h(car_image($car)) ?>" class="card-img-top object-fit-cover" style="height:190px" alt="<?= h($car['mark'] . ' ' . $car['model']) ?>">
              <div class="card-body d-flex flex-column">
                <h3 class="h5 card-title"><?= h($car['mark'] . ' ' . $car['model']) ?></h3>
                <p class="text-secondary small mb-2"><?= h($car['year']) ?> · <?= h($car['fuel']) ?> · <?= h($car['transmission']) ?></p>
                <p class="mb-3"><?= h($car['price']) ?> € / päev</p>
                <a href="auto.php?id=<?= (int)$car['id'] ?>" class="btn btn-dark mt-auto">Vaata ja broneeri</a>
              </div>
            </article>
          </div>
        <?php endwhile; ?>
      </div>
      <?php if ($totalPages > 1): ?>
        <?php
        $pageGroupSize = 10;
        $firstPageInGroup = (int)(floor(($page - 1) / $pageGroupSize) * $pageGroupSize) + 1;
        $lastPageInGroup = min($firstPageInGroup + $pageGroupSize - 1, $totalPages);
        ?>
        <nav class="mt-4" aria-label="Autode leheküljed">
          <ul class="pagination justify-content-center">
            <li class="page-item <?= $firstPageInGroup === 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(['q' => $query, 'page' => max(1, $firstPageInGroup - 1)]) ?>" aria-label="Eelmised 10 lehekülge">Eelmised</a>
            </li>
            <?php for ($number = $firstPageInGroup; $number <= $lastPageInGroup; $number++): ?>
              <li class="page-item <?= $number === $page ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(['q' => $query, 'page' => $number]) ?>"><?= $number ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= $lastPageInGroup === $totalPages ? 'disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(['q' => $query, 'page' => min($totalPages, $lastPageInGroup + 1)]) ?>" aria-label="Järgmised 10 lehekülge">Järgmised</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </section>

  <section class="container py-4" id="hinnad">
    <h2 class="h3">Selged hinnad</h2>
    <p>Hind on märgitud auto juures ühe rendipäeva kohta. Broneeringu koguhind arvutatakse valitud päevade järgi.</p>
  </section>
  <section class="container py-4" id="kontakt">
    <h2 class="h3">Kontakt</h2>
    <p>Küsimuste korral kirjuta meile: <a href="mailto:info@autorent.ee">info@autorent.ee</a>.</p>
  </section>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
