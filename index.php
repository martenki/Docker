<?php include("config.php"); 
session_start();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Autorent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
      <style>
      .hero {
        height: 300px;
         background-image: url("#");
        background-size: cover;
        background-position: center; 
      }
    </style>
  </head>
  <body>

  <nav class="navbar navbar-expand-lg bg-body-tertiary  border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Autorent</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Avaleht</a>
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

      <?php
        $paring = "SELECT * FROM cars";
        $valjund = mysqli_query($yhendus, $paring);
        $rida = mysqli_fetch_row($valjund);
        // var_dump($rida);
      ?>
      
      </ul>
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Otsi..." aria-label="Search" name="search">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
      </form>
      <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
          <a href="admin/logout.php" class="btn btn-dark btn-sm ms-2">Logout</a>
      <?php else: ?>
          <a href="admin/login.php" class="btn btn-dark btn-sm ms-2">Logi sisse</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<!-- /menüü -->

 <div class="container py-4">
      <div class="hero bg-body-tertiary p-4">
        <div class="row  d-flex">
          <div class="col-sm-6">
            <h1 class="fw-bold">Rendi<br>auto<br>soodsalt!</h1>
            <p class="text-secondary">Lai valik autosid igaks olukorraks</p>
            <button class="btn btn-dark">Vaata autosid</button>
          </div>
          <div class="col-sm-6">
            <img class="image-fluid" src="https://loremflickr.com/600/250/mustang" alt="autopilt">
          </div>
        </div>
      </div>
    </div>

<?php

$paring = 'SELECT * FROM cars'; 

if (isset($_GET["search"])) {
  $otsi = $_GET["search"];
  $paring .= ' WHERE mark LIKE "%'.$otsi.'%"';
}

$paring .= ' LIMIT 8';
$valjund = mysqli_query($yhendus, $paring);
// var_dump($valjund);

?>

<!-- autode kaardid -->
 <div class="container">

  <?php
  // kui ei ole leiud
    if ($result=mysqli_query($yhendus,$paring)){
      $rowcount=mysqli_num_rows($result);
      if ($rowcount == 0) {
       echo '
       <div class="alert alert-danger alert-dismissible fade show" role="alert">
        Otsitud autot ei leitud
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
       ';
      }
    }
  ?>
  
  <div class="row">
    <?php
while($rida = mysqli_fetch_row($valjund)){ 
    ?>
    <!-- kaart --> 
    <div class="col-sm-3">
      <div class="card my-2" style="width: 19rem;">
        <img src="https://loremflickr.com/600/350/<?php echo $rida[1]; ?>" class="card-img-top" alt="auto">
        <div class="card-body">
          <div class="row">
            <div class="col"><h5 class="card-title"><?php echo $rida[1]; ?></h5></div>
            <div class="col text-end"><i class="bi bi-heart"></i></div>
          </div>
          
          <p class="card-text text-secondary"><?php echo $rida[2]; ?></p>
          <p class="card-text">
          Mootor: <?php echo $rida[8]; ?><br>
          Kütus: <?php echo $rida[9]; ?><br>
          Hind: <?php echo $rida[10]; ?>€/päev</p>
          <a href="auto.php?id=<?php echo $rida[0]; ?>" class="btn btn-dark w-100">Rendi</a>
        </div>
      </div>
    </div>
   <?php } ?>

 </div>
<!-- leheküljenumbrid -->
    <nav>
      <ul class="pagination py-4 justify-content-center">
        <li class="page-item disabled"> <span class="page-link link-dark border-secondary">Eelmine</span> </li>
        <li class="page-item active"> <span class="page-link bg-dark text-white border-dark">1</span></li>
        <li class="page-item"> <a class="page-link link-dark border-secondary" href="#">2</a> </li>
        <li class="page-item"> <a class="page-link link-dark border-secondary" href="#">3</a> </li>
        <li class="page-item"> <a class="page-link link-dark border-secondary" href="#">Järgmine</a> </li>
      </ul>
    </nav>
 </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>