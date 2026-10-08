<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db_server = 'db';
$db_andmebaas = 'autorent_db';
$db_kasutaja = 'appuser';
$db_salasona = 'appuserpass';

try {
    $yhendus = new mysqli($db_server, $db_kasutaja, $db_salasona, $db_andmebaas);
    $yhendus->set_charset('utf8mb4');
} catch (Exception $e) {
    die('Ei saa ühendust andmebaasiga: ' . $e->getMessage());
}

?>