<?php
// SSO: Login is handled by PEP's unified login.php
session_name('pep');
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
// Not logged in — redirect to PEP login
header("Location: ../login.php");
exit;