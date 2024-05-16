<?php
try {
    $dbh = new PDO('mysql:host=localhost;dbname=pv113', "root", "21436587Bn");
} catch (PDOException $e) {
    echo "Проблема підключення до БД ". $e;
    exit();
}