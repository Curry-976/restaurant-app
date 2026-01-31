<?php
require "config/db.php";

$stmt = $pdo->query("SELECT 'Connexion PostgreSQL OK' AS test");
$result = $stmt->fetch();

echo $result["test"];
