<?php 

session_start(); 

if (!isset($_SESSION['username'])) {
    header('location: ../index.php');
}

if (isset($_POST['logout-submit'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: ../index.php");
}
