<?php

// require_once '/var/www/html/pin-your-pics/htdocs/pin-your-pics/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

function listObjectsForUserFromS3Bucket($s3Client, $bucket, $directory) {
    $iterator = $s3Client->getIterator('ListObjects', array(
        'Bucket' => $bucket,
        'Prefix' => $directory
    ));
    return $iterator;
}

function getObjectForUserFromS3Bucket($s3Client, $bucket, $key) {
    return $s3Client->getObject(array(
        'Bucket' => $bucket,
        'Key' => $key
    ));
}

function getUriForPicture($s3Client, $bucket, $path) {
    $cmd = $s3Client->getCommand('GetObject', [
        'Bucket' => $bucket,
        'Key'    => $path
    ]);
    
    //The period of availability
    $request = $s3Client->createPresignedRequest($cmd, '+10 minutes');
    
    //Get the pre-signed URL
    $signedUrl = (string) $request->getUri();
    return $signedUrl;
}

function countFiles($s3Client, $bucket, $directory) {
    $iterator = listObjectsForUserFromS3Bucket($s3Client, $bucket, $directory);
    $count = 0;
    foreach ($iterator as $object) {
        $count++;
    }
    return $count;
}

function clearLatLngCookies() {
    unset($_COOKIE['lat']); 
    setcookie('lat', null, -1, '/'); 
    unset($_COOKIE['lng']); 
    setcookie('lng', null, -1, '/'); 

}

function readGpsLocationFromCookiesAndClear() {
    if (isset($_COOKIE['lat']) && isset($_COOKIE['lng'])) {
        $lat = $_COOKIE['lat'];
        $lng = $_COOKIE['lng'];
        
        clearLatLngCookies();
        return array(
            'lat' => $lat,
            'lng' => $lng
        );
    }

    clearLatLngCookies();
    return false;
}

function readGpsLocationFromPhoto($file) {
    if (is_file($file)) {
        $info = exif_read_data($file);
        if (isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
            isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
            in_array($info['GPSLatitudeRef'], array('E','W','N','S')) && in_array($info['GPSLongitudeRef'], array('E','W','N','S'))) {

            $GPSLatitudeRef  = strtolower(trim($info['GPSLatitudeRef']));
            $GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

            $lat_degrees_a = explode('/',$info['GPSLatitude'][0]);
            $lat_minutes_a = explode('/',$info['GPSLatitude'][1]);
            $lat_seconds_a = explode('/',$info['GPSLatitude'][2]);
            $lng_degrees_a = explode('/',$info['GPSLongitude'][0]);
            $lng_minutes_a = explode('/',$info['GPSLongitude'][1]);
            $lng_seconds_a = explode('/',$info['GPSLongitude'][2]);

            $lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
            $lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
            $lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
            $lng_degrees = $lng_degrees_a[0] / $lng_degrees_a[1];
            $lng_minutes = $lng_minutes_a[0] / $lng_minutes_a[1];
            $lng_seconds = $lng_seconds_a[0] / $lng_seconds_a[1];

            $lat = (float) $lat_degrees+((($lat_minutes*60)+($lat_seconds))/3600);
            $lng = (float) $lng_degrees+((($lng_minutes*60)+($lng_seconds))/3600);

            //If the latitude is South, make it negative. 
            //If the longitude is west, make it negative
            $GPSLatitudeRef  == 's' ? $lat *= -1 : '';
            $GPSLongitudeRef == 'w' ? $lng *= -1 : '';

            return array(
                'lat' => $lat,
                'lng' => $lng
            );
        }           
    }
    return false;
}