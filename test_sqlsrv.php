<?php
require 'vendor/autoload.php';

$serverName = "10.27.19.12, 1433";
$connectionOptions = [
    "Database" => "KRF",
    "Uid" => "sa",
    "PWD" => "Bintang7",
    "TrustServerCertificate" => true
];

echo "Testing SQLSRV extension...\n";
if (!function_exists('sqlsrv_connect')) {
    echo "ERROR: sqlsrv_connect function does not exist. Is the extension installed?\n";
    exit(1);
}

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo "ERROR: Could not connect.\n";
    print_r(sqlsrv_errors());
    exit(1);
}

echo "SUCCESS: Connected to SQL Server!\n";
$sql = "SELECT TOP 1 * FROM Branch";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    echo "ERROR: Query failed.\n";
    print_r(sqlsrv_errors());
} else {
    echo "SUCCESS: Query executed. Rows:\n";
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        print_r($row);
    }
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
