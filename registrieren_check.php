<?php

const POST_PARAM_EINGABE = 'eingabe';
const POST_PARAM_LOESUNG = 'loesung';

//echo $_POST['loesung'];

require_once('db_connection.php');
global $link;

$email = $_POST['email'];
$passwort = $_POST['psw'];
$passwort2 = $_POST['f'];

$checked = false;

if($_POST[POST_PARAM_LOESUNG] == $_POST[POST_PARAM_EINGABE]) {
    $checked = true;
} else {
    $checked = false;
}

if($checked){
    require_once("db_pdo.php");
    global $mysql;
    $stmt = $mysql->prepare("SELECT * FROM user WHERE Email = :email"); //Username überprüfen
    $stmt->bindParam(":email", $_POST["email"]);
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($count == 0) {
        if($_POST["psw"] == $_POST["psw2"]){
            //User anlegen
            $stmt = $mysql->prepare("INSERT INTO user (Email, passwort) VALUES (:email, :psw)");
            $stmt->bindParam(":email", $_POST["email"]);
            $hash = password_hash($_POST["psw"], PASSWORD_BCRYPT);
            $stmt->bindParam(":psw", $hash);
            $stmt->execute();

            $stmt_uid = $mysql->prepare("SELECT * FROM user WHERE Email LIKE :email"); //Username überprüfen
            $stmt_uid->bindParam(":email", $email);
            $stmt_uid->execute();

            $UID = $stmt_uid->fetch();

            $stmt2 = "INSERT INTO kunde (UID, Anzeigename, Geburtsdatum) VALUES ('$UID[0]','', DATE '0-0-0')";
            mysqli_query($link, $stmt2);

            if ($_POST['rolle'] == 'Anbieter'){
                $stmt3 = "INSERT INTO anbieter (UID, Name, Beschreibung, Webseite, Bild, Adresse, Öffnungszeiten, Tel) VALUES ('$UID[0]','','','','','','', '')";
                mysqli_query($link, $stmt3);
            }
            header("Location: index.php?registrierung=r");
        } else {
            header("Location: index.php?registrierung=fpass");
        }
    } else {
        header("Location: index.php?registrierung=femail");
    }
}else{
    header("Location: index.php?registrierung=femail");
}
