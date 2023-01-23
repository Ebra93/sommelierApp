<?php

require_once('db_connection.php');
global $link;

$email = $_POST['email'];
$passwort = $_POST['psw'];

    $query_user = "SELECT email, passwort FROM user WHERE email='$email' AND passwort='$passwort'";
    $query_anbieter = "SELECT email, passwort FROM anbieter_user WHERE email='$email' AND passwort='$passwort'";

    $result_user = mysqli_query($link, $query_user);
    $result_anbieter = mysqli_query($link, $query_anbieter);

    if(mysqli_num_rows($result_user) == 0) {
        if (mysqli_num_rows($result_anbieter) == 0) {
            header("Location: index.php?anmeldung=falsch");
        }
        else{
            header("Location: anbieter.php");
        }

    }
    else{
        header("Location: index.php?anmeldung=richtig");
    }


echo $email . $passwort;