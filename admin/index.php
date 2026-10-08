<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.php");
    exit();
}

include("../config.php"); 

/* KUSTUTAMINE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = mysqli_prepare($yhendus, "DELETE FROM cars WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
    exit();
}

/* UUENDAMINE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {

    $id = intval($_POST['update_id']);
    $mark = $_POST['mark'];
    $model = $_POST['model'];
    $motor = $_POST['motor'];
    $fuel = $_POST['fuel'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $stmt = mysqli_prepare($yhendus,
        "UPDATE cars SET mark=?, model=?, motor=?, fuel=?, price=?, description=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssisi",
        $mark, $model, $motor, $fuel, $price, $description, $id);
    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    exit();
}

$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$result = mysqli_query($yhendus, "SELECT * FROM cars ORDER BY id ASC");
?>

<!doctype html>
<html lang="et">
<head>
<meta charset="utf-8">
<title>Autorent admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

<!-- Ülemine osake -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Autorent admin</h4>
        <small class="text-muted">Autode haldus</small>
    </div>
    <div class="d-flex">
        <a href="add_car.php" class="btn btn-dark btn-sm px-3 me-2">
            + Lisa auto
        </a>
        <a href="logout.php" class="btn btn-outline-secondary btn-sm">
            Logout
        </a>
    </div>
</div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Pilt</th>
                            <th>Auto</th>
                            <th>Mootor</th>
                            <th>Kütus</th>
                            <th>Hind</th>
                            <th>Kirjeldus</th>
                            <th class="text-end">Tegevused</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($auto = mysqli_fetch_assoc($result)): ?>

                    <?php if ($edit_id === (int)$auto['id']): ?>
                    <tr class="table-warning">
                    <form method="POST">

                        <td style="width:120px;">
                            <img src="https://loremflickr.com/600/350/<?php echo $rida[1]; ?>" class="card-img-top" alt="auto"
                                 class="img-fluid rounded-3 shadow-sm"
                                 style="max-height:70px;">
                        </td>

                        <td>
                            <input type="text" name="mark" class="form-control form-control-sm mb-1"
                                   value="<?php echo htmlspecialchars($auto['mark']); ?>">
                            <input type="text" name="model" class="form-control form-control-sm"
                                   value="<?php echo htmlspecialchars($auto['model']); ?>">
                        </td>

                        <td>
                            <input type="text" name="motor" class="form-control form-control-sm"
                                   value="<?php echo htmlspecialchars($auto['motor']); ?>">
                        </td>

                        <td>
                            <input type="text" name="fuel" class="form-control form-control-sm"
                                   value="<?php echo htmlspecialchars($auto['fuel']); ?>">
                        </td>

                        <td>
                            <input type="number" name="price" class="form-control form-control-sm"
                                   value="<?php echo htmlspecialchars($auto['price']); ?>">
                        </td>

                        <td>
                            <input type="text" name="description" class="form-control form-control-sm"
                                   value="<?php echo htmlspecialchars($auto['description']); ?>">
                        </td>

                        <td class="text-end">
                            <input type="hidden" name="update_id" value="<?php echo $auto['id']; ?>">
                            <button class="btn btn-success btn-sm">Salvesta</button>
                            <a href="index.php" class="btn btn-outline-secondary btn-sm">Tühista</a>
                        </td>

                    </form>
                    </tr>

                    <?php else: ?>
                    <tr>

                        <td style="width:200px;">
                            <img src="https://loremflickr.com/200/200/<?php echo $auto['mark']; ?>" class="card-img-top" alt="auto">
                        </td>

                        <td>
                            <?php echo htmlspecialchars($auto['mark'].' '.$auto['model']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($auto['motor']); ?>

                        </td>

                        <td>
                            <?php echo htmlspecialchars($auto['fuel']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($auto['price']); ?> €
                            <small>/päev</small>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($auto['description']); ?>
                        </td>

                        <td class="text-end">
                          <div class="btn-group" role="group" aria-label="Basic example">
                              <a href="index.php?edit_id=<?php echo $auto['id']; ?>"
                               class="btn btn-outline-primary btn-sm me-1">
                                Muuda
                              </a>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="delete_id" value="<?php echo $auto['id']; ?>">
                                <button type="submit"
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Kas oled kindel, et soovid kustutada?')">
                                    Kustuta
                                </button>
                            </form>
                          </div>
                        </td>

                    </tr>
                    <?php endif; ?>

                    <?php endwhile; ?>

                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

</body>
</html>