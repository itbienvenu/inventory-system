<?php
// This the file to controll the auth

session_start();

if(!isset($_SESSION['role']) && !isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}