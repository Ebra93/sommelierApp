<?php
session_start();
require_once('db_pdo.php');
global $mysql;

$name = $_POST['name'];
$email = $_POST['email'];
$geburtsdatum = $_POST['geburtsdatum'];

$stmt = $mysql->prepare("SELECT * FROM user WHERE Email = :email"); //Username überprüfen
$stmt->bindParam(":email", $_SESSION['email']);
$stmt->execute();

$UID = $stmt->fetch();
echo $UID['0'];

if(isset($name) && !(trim($name) == ''))
{
    $stmt = $mysql->prepare("UPDATE kunde SET Anzeigename = '$name' WHERE UID = :uid"); //Username überprüfen
    $stmt->bindParam(":uid", $UID['0']);
    $stmt->execute();
}

if(isset($email) && !(trim($email) == ''))
{
    $stmt = $mysql->prepare("UPDATE user SET Email = '$email' WHERE UID = :uid"); //Username überprüfen
    $stmt->bindParam(":uid", $UID['0']);
    $stmt->execute();
    session_destroy();
    session_start();
    $_SESSION['email'] = $email;
}

if(isset($geburtsdatum) && !(trim($geburtsdatum) == ''))
{
    $stmt = $mysql->prepare("UPDATE kunde SET Geburtsdatum = '$geburtsdatum' WHERE UID = :uid"); //Username überprüfen
    $stmt->bindParam(":uid", $UID['0']);
    $stmt->execute();
}

header('Location: meinkonto.php');