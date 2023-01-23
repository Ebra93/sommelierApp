<?php
session_start();
include_once ("db_connection.php");
global $link;

$getraenk = $_POST['getraenk'];
$sterne = $_POST['bewertung'];
$kommentar = $_POST['review'];

require_once("db_pdo.php");
global $mysql;

$stmt_uid = $mysql->prepare("SELECT * FROM user WHERE Email LIKE :email"); //Username überprüfen
$stmt_uid->bindParam(":email", $_SESSION['email']);
$stmt_uid->execute();

$UID = $stmt_uid->fetch();

$stmt= "insert into bewertung(Sterne,Kommentar,GID,UID,Datum) values($sterne,'$kommentar',$getraenk,'$UID[0]', CURRENT_DATE())";
mysqli_query($link,$stmt);
header("Location: getraenk.php?getraenk=$getraenk");