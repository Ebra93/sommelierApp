<?php
session_start();
require_once('db_pdo.php');
global $mysql;

$aktuellesPass = $_POST['aktuellesPass'];
$neuesPass = $_POST['neuesPass'];
$wiederholenPass = $_POST['wiederholenPass'];

$stmt = $mysql->prepare("SELECT * FROM user WHERE Email = :email"); //Username überprüfen
$stmt->bindParam(":email", $_SESSION['email']);
$stmt->execute();

$UID = $stmt->fetch();
echo $UID['0'];

if (password_verify($aktuellesPass, $UID['2'])) {
    if ($neuesPass == $wiederholenPass) {
        $stmt2 = $mysql->prepare("UPDATE user SET passwort = :psw WHERE UID = :uid");
        $stmt2->bindParam(":uid", $UID['0']);
        $hash = password_hash($_POST["neuesPass"], PASSWORD_BCRYPT);
        $stmt2->bindParam(":psw", $hash);
        $stmt2->execute();

        header("Location: meinkonto.php?psw=r");
    }
    else{
        header("Location: meinkonto.php?psw=f");
    }
}
else{
    header("Location: meinkonto.php?psw=af");
}