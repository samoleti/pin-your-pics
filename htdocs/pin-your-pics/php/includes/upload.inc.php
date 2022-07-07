<?php
session_start(); 

require_once('utils.inc.php');
require '../../../vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

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

    $user = $_SESSION['username'];

    $destinationFolder = 'uploads/' . $user . '/';

    $bucket = '******';

    // $IAM_KEY = ******;
    // $IAM_SECRET = ******;
    try {
        $s3Client = new S3Client([
            'region' => 'us-east-1',
            'version' => 'latest'
            // 'credentials' => array(
            //     'key' => $IAM_KEY,
            //     'secret' => $IAM_SECRET
            // )
        ]);
        $fileName = countFiles($s3Client, $bucket, $destinationFolder) + 1;
        $destinationPath = $destinationFolder . $fileName;
        $result = $s3Client->putObject([
            'Bucket' => $bucket,
            'Key' => $destinationPath,
            'SourceFile' => $tmpPath,
        ]);
    } catch (S3Exception $e) {
        echo $e->getMessage() . "\n";
    }

    require('dbimage.inc.php');

    $isPrivate = isset($_POST['private-pic']) ? 1 : 0;

    saveMetadata($fileName, $gpsData['lat'], $gpsData['lng'], $user, $isPrivate);

    header('location: ../index.php');
}