<?php
include("config.php");
session_start();

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = "user";

    $firstname = $_POST['firstname'];
    $lastname  = $_POST['name'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $password  = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // kas email on olemas juba
    $checkEmailStmt = $yhendus->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        $message = "Email ID already exists";
        $toastClass = "#007bff";
    } else {
  
        $stmt = $yhendus->prepare(
            "INSERT INTO users (first_name, last_name, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $firstname, $lastname, $email, $phone, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Account created successfully";
            $toastClass = "#28a745";
        } else {
            $message = "Error: " . $stmt->error;
            $toastClass = "#dc3545";
        }

        $stmt->close();
    }

    $checkEmailStmt->close();

    $yhendus->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href=
"https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href=
"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link rel="shortcut icon" href=
"https://cdn-icons-png.flaticon.com/512/295/295128.png">
    <script src=
"https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <title>Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Autorent</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Avaleht</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Autod</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Hinnad</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Kontakt</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="regamine.php">Konto</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<body class="bg-light">
    <div class="container p-5 d-flex flex-column align-items-center">
        <?php if ($message): ?>
            <div class="toast align-items-center text-white border-0" 
          role="alert" aria-live="assertive" aria-atomic="true"
                style="background-color: <?php echo $toastClass; ?>;">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close
                    btn-close-white me-2 m-auto" 
                          data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <form method="post" class="form-control mt-5 p-4"
            style="height:auto; width:380px;
            box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px,
            rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">
            <div class="row text-center">
                <h5 class="p-3" style="font-weight: 700;">Tehke enda konto</h5>
            </div>
            <div class="mb-2">
                <label for="username"><i 
                  class="fa fa-user"></i> Firstname</label>
                <input type="text" name="firstname" id="firstname"
                  class="form-control" required>
            </div>

             <div class="mb-2">
                <label for="username"><i 
                  class="fa fa-user"></i> Lastname</label>
                <input type="text" name="name" id="lastname"
                  class="form-control" required>
            </div>

            <div class="mb-2 mt-2">
                <label for="email"><i 
                  class="fa fa-envelope"></i> Email</label>
                <input type="text" name="email" id="email"
                  class="form-control" required>
            </div>

             <div class="mb-2">
                <label for="username"><i 
                  class="fa fa-user"></i> Phone</label>
                <input type="text" name="phone" id="phone"
                  class="form-control" required>
            </div>

            <div class="mb-2 mt-2">
                <label for="password"><i 
                  class="fa fa-lock"></i> Password</label>
                <input type="password" name="password" id="password"
                  class="form-control" required>
            </div>

            <div class="mb-2 mt-3">
                <button type="submit" 
                  class="btn btn-success
                bg-secondary" style="font-weight: 600;">Create
                    Account</button>
            </div>
        </form>
    </div>
</body>

</html>