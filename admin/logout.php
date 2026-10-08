<?php
require_once __DIR__ . '/../inc/bootstrap.php';

unset($_SESSION['is_admin']);
header('Location: login.php');
exit;
