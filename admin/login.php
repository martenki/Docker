<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width:500px;">
<h2 class="mb-3">Admin login</h2>

<form method="POST">

<div class="mb-3">
<label>Kasutajanimi</label>
<input type="text" name="username" class="form-control">
</div>

<div class="mb-3">
<label>Parool</label>
<input type="password" name="password" class="form-control">
</div>

<button class="btn btn-dark w-100">Logi sisse</button>

</form>

<?php

if(isset($_POST['username']) && isset($_POST['password'])){

    if($_POST['username'] == "admin" && $_POST['password'] == "admin"){

        $_SESSION['is_admin'] = true;

        header("Location: index.php"); // admnini index
        exit();

    } else {

        echo "Vale kasutajanimi või parool";

    }

}
?>

</div>

</body>
</html>