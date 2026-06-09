<?php
// SSO: Delegate logout to PEP's shared session destroyer
session_name('pep');
session_start();
session_destroy();
header("Location: ../login.php");
exit;
