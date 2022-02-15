<?php
session_start(); 

require_once('utils.inc.php');

if (!isset($_SESSION['username'])) {
    header('location: ../login.php?error=You must be logged in to upload');
    exit();
}

if (isset($_POST["submit"])) {
    $tmpPath = $_FILES['uploaded-pic']['tmp_name'];
    $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
    $detectedType = exif_imagetype($tmpPath);

    if (!in_array($detectedType, $allowedTypes)) {
        header('location: ../index.php');
        exit();   
    }

    $gpsDataFromPhoto = readGpsLocationFromPhoto($tmpPath);
    $gpsDataFromCookies = readGpsLocationFromCookiesAndClear();
    $gpsData = $gpsDataFromPhoto ? $gpsDataFromPhoto : $gpsDataFromCookies;

    var_dump($gpsDataFromPhoto);
    var_dump($gpsDataFromCookies);
    var_dump($gpsData);

    $user = $_SESSION['username'];

    $destinationFolder = '../../uploads/' . $user . '/';
    if (!file_exists($destinationFolder)) {
        mkdir($destinationFolder, 0777, true);
    }
    
    $fileName = countFiles($destinationFolder) + 1;

    var_dump($destinationFolder);
    var_dump($fileName);

    $destinationPath = $destinationFolder . $fileName;
    move_uploaded_file($tmpPath, $destinationPath);

    require('dbimage.inc.php');

    $isPrivate = isset($_POST['private-pic']) ? 1 : 0;

    saveMetadata($fileName, $gpsData['lat'], $gpsData['lng'], $user, $isPrivate);

    header('location: ../index.php');
}