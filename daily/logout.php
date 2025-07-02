<?php
// Ensure session is started to access session variables
session_start();

include_once (__DIR__."/config/auth.php"); // Adjust path if config is in a different location relative to logout.php
include_once (__DIR__."/config/config.php"); // Adjust path if config is in a different location relative to logout.php
include_once __DIR__. "/includes/logger.php"; // Adjust path if includes is in a different location relative to logout.php

log_user_action("Logged Out", "User successfully logged out");

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page (or homepage)
header("Location: login.php"); // Assuming your login page is named login.php and is in the root
exit;
?>
