<?php

function escape($conn, $var) {
    return mysqli_real_escape_string($conn, $var);
}

function saveMetadata($unescapedFileName, $unescapedLat, $unescapedLng, $unescapedUser, $unescapedIsPrivate) {
    require('connect.inc.php');
    $fileName = escape($conn, $unescapedFileName);
    $lat = escape($conn, $unescapedLat);
    $lng = escape($conn, $unescapedLng);
    $user = escape($conn, $unescapedUser);
    $isPrivate = escape($conn, $unescapedIsPrivate);

    $query = "INSERT INTO pics (file_name, lat, lng, owner, is_private) VALUES('$fileName', '$lat', '$lng', '$user', '$isPrivate')";
    mysqli_query($conn, $query);
}

function getPublicPhotos() {
    require('connect.inc.php');
    $query = "SELECT file_name, lat, lng, owner FROM pics WHERE is_private = FALSE";
    $result = mysqli_query($conn, $query);
    return $result;
}

function getPublicPhotosAndPrivatePhotosForUser($unescapedUser) {
    require('connect.inc.php');
    $user = escape($conn, $unescapedUser);
    $query = "SELECT file_name, lat, lng, owner FROM pics WHERE is_private = FALSE OR owner = '$user'";
    $result = mysqli_query($conn, $query);
    return $result;
}