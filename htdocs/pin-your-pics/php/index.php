<?php   
    require_once '../../vendor/autoload.php';

    use Aws\S3\S3Client;  
    use Aws\Exception\AwsException;

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    session_start();
    
    require('includes/dbimage.inc.php');
    require('includes/utils.inc.php');
    $pics = array();
    if(!isset($_SESSION['username'])) {
        $pics = getPublicPhotos();
    } else $pics = getPublicPhotosAndPrivatePhotosForUser($_SESSION['username']);
    $bucket = '*****';
    // $IAM_KEY = ******;
    // $IAM_SECRET = ******;
    $s3Client = new S3Client([
        'region' => 'us-east-1',
        'version' => 'latest'
        // 'credentials' => array(
        //     'key' => $IAM_KEY,
        //     'secret' => $IAM_SECRET
        // )
    ]);
    echo "<div class='loadPics'>";
    while ($temp = mysqli_fetch_array($pics)) {
        echo "<div class='current-pic'>";
        $filePath = 'uploads/' . $temp['owner'] . '/' . $temp['file_name'];
        $uri = getUriForPicture($s3Client, $bucket, $filePath);
        echo "<img src='$uri' class='pin-img'>";
        echo "<div class='lat'>".$temp['lat']."</div>";
        echo "<div class='lng'>".$temp['lng']."</div>";
        echo "</div>";
    }
    echo "</div>";
?>

<html>
<head>
    <title>Pin your pics</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
    <link rel="stylesheet" href="../css/styles.css" />
</head>

<body>
    <div id="mySidenav" class="sidenav">    
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div class="containerSidenav">
        <form action="includes/upload.inc.php" method="post" enctype="multipart/form-data" class="form-upload-pic">
            <h2 style="color:white">Upload picture</h2>
            <ol style="line-height:180%">
                <li>
                    <label for="uploaded-pic">Choose picture from your device:</label>
                    <input type="file" id="uploaded-pic" class="inputfile" name="uploaded-pic" accept="image/*" onchange="loadFile(event, 'output-a')">
                    <img id="output-a" class="sidenav-image"/>
                </li>
                <li>
                    <div>Check the box below if you want to resize your picture in Instagram format.</div>
                    <input type="checkbox" id="instagram-pic" name="instagram-pic">
                    <label for="instagram-pic">Resize to Instagram format</label>
                </li>
                <li>
                    <div>Check the box below if you want your picture to be private. Uploaded pictures are public by default. </div>
                    <input type="checkbox" id="private-pic" name="private-pic">
                    <label for="private-pic">Private</label>
                </li>
            </ol>
            <input type="submit" value="Submit" name="submit">
            <br>
            <div><em>* You can also upload pics by clicking once on the map. This way you choose the exact location of the pic.</em></div>
        </form>
        </div>
    </div>
    <div id="mySidenavMap" class="sidenav">    
        <a href="javascript:void(0)" class="closebtn" onclick="closeNavMap()">&times;</a>
        <div class="containerSidenav">
        <form action="includes/upload.inc.php" method="post" enctype="multipart/form-data" class="form-upload-pic">
            <h2 style="color:white">Upload picture with coordinates from map</h2>
            <ol style="line-height:180%">
                <li>
                    <label for="uploaded-pic">Choose picture from your device:</label>
                    <input type="file" id="uploaded-pic" class="inputfile" name="uploaded-pic" accept="image/*" onchange="loadFile(event, 'output-b')">
                    <img id="output-b" class="sidenav-image"/>
                </li>
                <li>
                    <div>Check this box if you want to resize your picture in Instagram format.</div>
                    <input type="checkbox" id="instagram-pic" name="instagram-pic">
                    <label for="instagram-pic">Resize to Instagram format</label>
                </li>
                <li>
                    <div>Check this box if you want your picture to be private. Uploaded pictures are public by default. </div>
                    <input type="checkbox" id="private-pic" name="private-pic">
                    <label for="private-pic">Private</label>
                </li>
            </ol>
            <input type="submit" value="Submit" name="submit">
            <br>
            <div><em>* If your pic already has details about exact location, they will be taken into account.</em></div>

        </form>
        </div>
    </div>
    <div id="main">
    <div class="topnav">
        <?php if (!isset($_SESSION['username'])) { ?>
            <a href="login.php">Login</a>
        <?php } else { ?>
            <span style="cursor:pointer" onclick="openNav()"> Upload pic</span>
            <form action="includes/logout.inc.php" method="post">
                <input type="submit" value="Logout" name="logout-submit" id="login-logout-button" style="cursor:pointer">
            </form>
        <?php } ?>
    </div>
    <div id="map"></div>
    <div id="navigation">
        <!-- <form action="upload.php" method="post" enctype="multipart/form-data">
            <label for="uploaded-pic">Upload your photo</label>
            <input type="file" id="uploaded-pic" name="uploaded-pic">
            <input type="submit" value="Submit" name="submit">
        </form> -->
    </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script src="../js/upload-pic.js"></script>
    <script src="../js/map.js"></script>
</body>

</html>