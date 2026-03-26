<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userid'])) {
    header("Location: ../../pages/auth/login.php"); 
    exit();
}

if (isset($required_role) && intval($_SESSION['roleid']) !== intval($required_role)) {
    header("HTTP/1.1 403 Forbidden");
    echo "Access denied.";
    exit();
}
?>