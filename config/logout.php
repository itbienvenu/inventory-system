<?php

// Logout controller
session_start();
// Unset all session variables
session_unset();
// Destroy the session
session_destroy();
header("Location: ../index.php");

