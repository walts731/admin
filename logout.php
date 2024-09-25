<?php
// Start a session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the visitor homepage page
header("Location: login_admin.php");
exit();
?>
