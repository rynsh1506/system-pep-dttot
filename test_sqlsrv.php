<?php
require 'vendor/autoload.php';

$host = '10.27.19.12';
$user = 'sa';
$pass = 'Bintang7';
$db   = 'KRF';

echo "Testing sqlsrv extension...\n";
if (!extension_loaded('sqlsrv') && !extension_loaded('pdo_sqlsrv')) {
    echo "ERROR: sqlsrv or pdo_sqlsrv extension is NOT loaded!\n";
} else {
    echo "Extension is loaded.\n";
}

try {
    $conn = new PDO("sqlsrv:Server=$host;Database=$db;Encrypt=0;TrustServerCertificate=1", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully to SQL Server!\n";
    
    $stmt = $conn->query("SELECT TOP 5 * FROM Agreement");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
