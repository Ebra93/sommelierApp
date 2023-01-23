<?php
session_start();

require_once ("db_connection.php");
global $link;

require_once("db_pdo.php");
global $mysql;

$stmt_uid = $mysql->prepare("SELECT * FROM user WHERE Email LIKE :email"); //Username überprüfen
$stmt_uid->bindParam(":email", $_SESSION['email']);
$stmt_uid->execute();

$UID = $stmt_uid->fetch();

$stmt_anbieter = $mysql->prepare("SELECT * FROM anbieter WHERE UID LIKE :uid"); //Username überprüfen
$stmt_anbieter->bindParam(":uid", $UID['0']);
$stmt_anbieter->execute();

$anbieter = $stmt_anbieter->fetch();

$name = $_POST['name'];
$adresse = $_POST['adresse'];
$webseite = $_POST['webseite'];
$oeffnungszeiten = $_POST['oeffnungszeiten'];
$telefonnummer = $_POST['telefonnummer'];
$beschreibung = $_POST['beschreibung'];

if($_POST['webseite'] != "") {
    $webseite = $_POST['webseite'];
}
else{
    $webseite = $anbieter['3'];
}

if($_POST['oeffnungszeiten'] != "") {
    $oeffnungszeiten = $_POST['oeffnungszeiten'];
}
else{
    $oeffnungszeiten = $anbieter['6'];
}

if($_POST['telefonnummer'] != "") {
    $telefonnummer = $_POST['telefonnummer'];
}
else{
    $telefonnummer = $anbieter['7'];
}

if($_POST['beschreibung'] != "") {
    $beschreibung = $_POST['beschreibung'];
}
else{
    $beschreibung = $anbieter['2'];
}


//

$target_dir = "img/";
$target_file = $target_dir . basename($_FILES["uploadFile"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["uploadFile"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}

// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}

// Check file size
if ($_FILES["uploadFile"]["size"] > 2000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $target_file)) {
        echo "The file ". htmlspecialchars( basename( $_FILES["uploadFile"]["name"])). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

//
if($uploadOk == 1) {
    $bild = htmlspecialchars( basename( $_FILES["uploadFile"]["name"]));
}
else{
    $bild = $anbieter['4'];
}

$stmt = $mysql->prepare("UPDATE anbieter SET Name = :name, Beschreibung = :beschreibung, Webseite = :webseite, Bild = :bild, Adresse = :adresse, Öffnungszeiten = :off, Tel = :tel WHERE UID = :uid");
$stmt->bindParam(":uid", $UID['0']);
$stmt->bindParam(":name", $name);
$stmt->bindParam(":beschreibung", $beschreibung);
$stmt->bindParam(":webseite", $webseite);
$stmt->bindParam(":bild", $bild);
$stmt->bindParam(":adresse", $adresse);
$stmt->bindParam(":off", $oeffnungszeiten);
$stmt->bindParam(":tel", $telefonnummer);
$stmt->execute();

$anbieterID = mysqli_query($link, "select UID from `user` where email like '" . $_SESSION['email'] . "'");
$anbieterID = mysqli_fetch_row($anbieterID);


foreach($_POST['entfernen'] as $item) {
    mysqli_query($link, "DELETE FROM anbieter_getränke WHERE UID like " . $anbieterID[0] . " and GID like " . $item);
}
echo 'count: ' . count($_POST['empfohlen']);
if(count($_POST['empfohlen']) > 3) {
    header("Location: anbieter.php");
    die();
}

mysqli_query($link, "update anbieter_getränke set empfohlen = false where UID like " . $anbieterID[0]);

foreach($_POST['empfohlen'] as $item) {
    mysqli_query($link, "update anbieter_getränke set empfohlen = true where gid like " . $item . " and uid like " . $anbieterID[0]);
}

header("Location: anbieter.php");
die();