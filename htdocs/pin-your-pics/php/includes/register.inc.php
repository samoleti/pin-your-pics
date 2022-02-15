<?php
session_start();

require_once ('connect.inc.php');

if (isset($_POST['register-submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $repeatedPassword = mysqli_real_escape_string($conn, $_POST['repeated-password']);

    if (empty($username)) {  header('location: ../register.php?error=Username is required'); exit(); }
    if (empty($email)) { header('location: ../register.php?error=Email is required'); exit(); }
    if (empty($password)) { header('location: ../register.php?error=Password is required'); exit(); }
    if ($password != $repeatedPassword) { header('location: ../register.php?error=Passwords do not match'); exit(); }

    $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($conn, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($user['username'] === $username) { header('location: ../register.php?error=Username already taken'); exit(); }
        if ($user['username'] === $username) { header('location: ../register.php?error=Email already in use'); exit(); }
    }

    $hashedPassword = md5($password);

    $query = "INSERT INTO users (username, email, password) VALUES('$username', '$email', '$hashedPassword')";
    mysqli_query($conn, $query);
    $_SESSION['username'] = $username;
    $_SESSION['success'] = "You are now logged in";
    header('location: ../index.php');
}
