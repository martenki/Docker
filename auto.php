<?php
include("config.php");
session_start();

// kui ID puudub, tagasi avalehele
if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$car_id = (int)$_GET["id"];
$message = "";

// auto andmed
$q = mysqli_prepare($yhendus, "SELECT * FROM cars WHERE id=?");
mysqli_stmt_bind_param($q, "i", $car_id);
mysqli_stmt_execute($q);
$res = mysqli_stmt_get_result($q);
$auto = mysqli_fetch_assoc($res);

if (!$auto) {
    die("Autot ei leitud");
}

// kui vorm saadetud
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $start_date = $_POST["start_date"] ?? "";
    $end_date   = $_POST["end_date"] ?? "";

    if ($start_date && $end_date) {

        $start = new DateTime($start_date);
        $end   = new DateTime($end_date);

        if ($start > $end) {
            $message = "<div class='alert alert-danger'>Kuupäevad on valed.</div>";
        } else {

            $days = $start->diff($end)->days + 1;
            $total = $days * $auto["price"];

            $user_id = $_SESSION["user_id"] ?? 1;

            // kontroll kas juba broneeritud
            $check = mysqli_prepare($yhendus,
                "SELECT id FROM reservations 
                 WHERE car_id=? 
                 AND status='active' 
                 AND (? <= end_date) 
                 AND (? >= start_date)"
            );

            mysqli_stmt_bind_param($check, "iss", $car_id, $end_date, $start_date);
            mysqli_stmt_execute($check);
            $r = mysqli_stmt_get_result($check);

            if (mysqli_num_rows($r) > 0) {
                $message = "<div class='alert alert-danger'>See aeg on juba kinni.</div>";
            } else {

                $ins = mysqli_prepare($yhendus,
                    "INSERT INTO reservations 
                     (user_id, car_id, start_date, end_date, total_price, status) 
                     VALUES (?, ?, ?, ?, ?, 'active')"
                );

                mysqli_stmt_bind_param($ins, "iissd", $user_id, $car_id, $start_date, $end_date, $total);

                if (mysqli_stmt_execute($ins)) {
                    $message = "<div class='alert alert-success mt-3'>
                        Broneering tehtud! Kokku: " . number_format($total, 2) . " €
                    </div>";
                } else {
                    $message = "<div class='alert alert-danger'>Midagi läks valesti.</div>";
                }
            }
        }
    }
}
?>

<!doctype html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title><?= $auto["mark"] . " " . $auto["model"]; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">

    <?= $message; ?>

    <div class="row mt-4">
        <div class="col-md-6">
            <img src="https://loremflickr.com/800/500/car,<?= $auto['mark']; ?>" class="img-fluid rounded">
        </div>

        <div class="col-md-6">
            <h2><?= $auto["mark"] . " " . $auto["model"]; ?></h2>

            <ul class="list-group mb-3">
                <li class="list-group-item"><b>Aasta:</b> <?= $auto["year"]; ?></li>
                <li class="list-group-item"><b>Mootor:</b> <?= $auto["motor"]; ?></li>
                <li class="list-group-item"><b>Käigukast:</b> <?= $auto["transmission"]; ?></li>
                <li class="list-group-item"><b>Kütus:</b> <?= $auto["fuel"]; ?></li>
                <li class="list-group-item"><b>Istmed:</b> <?= $auto["seats"]; ?></li>
                <li class="list-group-item"><b>Hind:</b> <?= $auto["price"]; ?> €/päev</li>
                <li class="list-group-item"><b>Info:</b> <?= $auto["description"]; ?></li>
            </ul>

            <form method="POST" class="border p-3 rounded bg-light">
                <div class="mb-2">
                    <label>Algus</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label>Lõpp</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>

                <button class="btn btn-success w-100 mt-2">Broneeri</button>
            </form>

            <a href="index.php" class="btn btn-secondary mt-2">Tagasi</a>
        </div>
    </div>
</div>

</body>
</html>