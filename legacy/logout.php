<?php
session_name('pep');
session_start();
session_destroy();
header('Location: login.php');
exit;
?>