<?php
session_start();
include_once ("db_connection.php");
global $link;

$anbieter = $_POST['anbieter'];
$sterne = $_POST['bewertung'];
$kommentar = $_POST['review'];

require_once("db_pdo.php");
global $mysql;

$stmt_uid = $mysql->prepare("SELECT * FROM user WHERE Email LIKE :email"); //Username überprüfen
$stmt_uid->bindParam(":email", $_SESSION['email']);
$stmt_uid->execute();

$UID = $stmt_uid->fetch();

$stmt= "insert into bewertung_anbieter(Sterne,Kommentar,AID,UID,Datum) values($sterne,'$kommentar',$anbieter,'$UID[0]', CURRENT_DATE())";
mysqli_query($link,$stmt);
header("Location: geschaeft.php?anbieter=$anbieter");