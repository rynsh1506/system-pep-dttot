<?php
require 'config/db_cadeb.php';
try {
    $pdo_cadeb->exec("ALTER TABLE candidates ADD COLUMN kategori VARCHAR(50) NOT NULL DEFAULT 'Cadeb'");
    echo "Migration successful\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column already exists\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>