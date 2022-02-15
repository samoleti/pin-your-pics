<?php

$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'pin-your-pics';

$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName)
OR die('Could not connect to MySQL');
