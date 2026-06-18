<?php
$db = \Config\Database::connect('default');
$query = $db->query('SELECT * FROM pengajuan_dtot WHERE id = 2029');
$row = $query->getRow();
echo json_encode($row, JSON_PRETTY_PRINT);
