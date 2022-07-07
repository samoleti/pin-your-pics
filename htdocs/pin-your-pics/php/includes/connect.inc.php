<?php

$dbHost = '*****';
$dbUsername = '*****';
$dbPassword = '*****';
$dbName = '*****';

$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName)
OR die('Could not connect to MySQL');
