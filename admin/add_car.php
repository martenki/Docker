<?php

session_start();

include("../config.php");

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $mark = $_POST['mark'];
    $model = $_POST['model'];
    $motor = $_POST['motor'];
    $fuel = $_POST['fuel'];
    $price = $_POST['price'];

    $sql = "INSERT INTO cars (mark, model, motor, fuel, price)
            VALUES ('$mark', '$model', '$motor', '$fuel', '$price')";

if (mysqli_query($yhendus, $sql)) {
    $message = "Auto salvestatud!";
} else {
    $message = "Viga: " . mysqli_error($yhendus);
}
    }

?>

<!doctype html>
<html lang="et">
<head>
    <meta charset="utf-8">
    <title>Autorent admin - Lisa auto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Autorent admin</h4>
        <a href="../logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Lisa auto</h5>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Tagasi</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">       
            <form method="POST">
                <div class="row g-3">

                    <!-- Mark -->
                    <div class="col-md-6">
                        <label class="form-label">Mark</label>
                        <input type="text" name="mark" class="form-control" required>
                    </div>

                    <!-- Mudel -->
                    <div class="col-md-6">
                        <label class="form-label">Mudel</label>
                        <input type="text" name="model" class="form-control" required>
                    </div>

                    <!-- Mootor -->
                    <div class="col-md-6">
                        <label class="form-label">Mootor</label>
                        <input type="text" name="motor" class="form-control" placeholder="nt V6">
                    </div>

                    <!-- Kütus -->
                    <div class="col-md-6">
                        <label class="form-label">Kütus</label>
                        <select name="fuel" class="form-select" required>
                            <option value="">Vali</option>
                            <option value="Bensiin">Bensiin</option>
                            <option value="Diisel">Diisel</option>
                            <option value="Hübriid">Hübriid</option>
                            <option value="Elektriline">Elektriline</option>
                        </select>
                    </div>

                    <!-- Hind -->
                    <div class="col-md-6">
                        <label class="form-label">Hind (€ / päev)</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>

                    <!-- Pilt -->
                    <div class="col-md-6">
                        <label class="form-label">Auto pilt</label>
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Lubatud formaadid: JPG, PNG, WEBP.</small>
                    </div>

                </div>

                <hr class="my-4">

                <button type="submit" class="btn btn-dark">Salvesta</button>
                <a href="index.php" class="btn btn-outline-secondary">Tühista</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>