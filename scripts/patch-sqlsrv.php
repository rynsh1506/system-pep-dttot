<?php

/**
 * Patch CodeIgniter 4 SQLSRV Connection Driver to support TrustServerCertificate for ODBC 18
 */

$file = __DIR__ . '/../vendor/codeigniter4/framework/system/Database/SQLSRV/Connection.php';

if (file_exists($file)) {
    $content = file_get_contents($file);

    if (strpos($content, "'TrustServerCertificate'") === false) {
        $content = str_replace(
            "'ReturnDatesAsStrings' => 1,",
            "'ReturnDatesAsStrings' => 1,\n            'TrustServerCertificate' => 1,",
            $content
        );
        file_put_contents($file, $content);
        echo "Patched SQLSRV Connection.php with TrustServerCertificate=1\n";
    } else {
        echo "SQLSRV Connection.php is already patched\n";
    }
} else {
    echo "SQLSRV Connection.php not found. Skipping patch.\n";
}
