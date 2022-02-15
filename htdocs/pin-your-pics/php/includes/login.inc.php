<?php
session_start();

require_once ('connect.inc.php');

if (isset($_POST['login-submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
  
    if (empty($username)) {
        header('location: ../login.php?error=Username is required');
        exit();
    }
    if (empty($password)) {
        header('location: ../login.php?error=Password is required');
        exit();
    }
  
    $password = md5($password);
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $results = mysqli_query($conn, $query);
    if (mysqli_num_rows($results) != 1) {
        header('location: ../login.php?error=Wrong username/password combination');
        exit(); 
    }
    $_SESSION['username'] = $username;
    $_SESSION['success'] = "You are now logged in";
    header('location: ../index.php');
}