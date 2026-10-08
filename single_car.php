<?php
$query = isset($_GET['id']) ? '?id=' . rawurlencode((string)$_GET['id']) : '';
header('Location: auto.php' . $query, true, 301);
exit;