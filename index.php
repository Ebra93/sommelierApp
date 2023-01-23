<?php

session_start();

require_once('db_connection.php');
global $link;

if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}

$GET_ANMELDUNG = 'anmeldung';

$sql = "
SELECT  g.name, min(g.art), min(g.bild), min(g.herkunftsland), min(g.alkoholgehalt), avg(b.Sterne), count(b.Sterne), min(g.GID)
FROM getränke as g left join bewertung as b on g.GID = b.GID
group by g.name
LIMIT 30";
$result = mysqli_query($link, $sql);

if(isset($_POST["submit"])){
    require("db_connection.php");
    require_once("db_pdo.php");
    global $mysql;
    $stmt = $mysql->prepare("SELECT * FROM user WHERE Email = :email"); //Username überprüfen
    $stmt->bindParam(":email", $_POST["email"]);
    $stmt->execute();
    $count = $stmt->rowCount();

    $sql1 ="select  UID from user where Email like '". $_POST['email'] ."' ";
    $result1= mysqli_query($link,$sql1);
    $data = mysqli_fetch_array($result1);
    $sql2 = "select * from anbieter where UID = '". $data['0'] ."'";
    $result = mysqli_query($link,$sql2);
    $anbieter = mysqli_num_rows($result);


    if($count == 1){
        //Username ist frei
        $row = $stmt->fetch();
        if(password_verify($_POST["psw"], $row['passwort'])){
            session_start();
            $_SESSION['email'] = $_POST['email'];
            if($anbieter == 0) {
                header("Location: index.php?");
            }else{
                header("Location: meinkonto.php");
        }}else {
            header("Location: index.php?anmeldung=falsch");
        }
    } else {
        header("Location: index.php?anmeldung=falsch");
    }
}

const POST_PARAM_LÖSUNG = 'lösung';
global $row;

global $link;
$smallquery = "select count(bild) from captcha";
$results2 = mysqli_query($link, $smallquery);
$numrows = mysqli_fetch_row($results2);

$query = "SELECT min(BID),min(Bild),min(Lösung) FROM captcha group by bild limit 1 OFFSET " . rand() % $numrows[0];
$results = mysqli_query($link, $query);
$row = mysqli_fetch_row($results);

function randomcaptcha() {
    global $row;
    echo '<img src="img/' . $row[1] . '" width="300" id="bild">';
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Sommelier</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="nouislider.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<script src="nouislider.js"></script>

<?php
if(!isset($_SESSION['email'])){
    include('not_logged.php');
}
if(isset($_SESSION['email'])){
    include('logged.php');
}
?>

<?php include('filter.php'); ?>
    <p id="suchetext">
    <?php
    if(isset($_GET['suche'])) {
        if ($_GET['suche'] == "falsch") {
            echo "Kein Getränk gefunden";
        }
    }
    ?>
    </p>

<div id="überschrift2">
    Empfehlungen des Sommeliers:
</div>
<div id="empfehlungen">
    <table id="tabelle">
        <?php
        while($row = mysqli_fetch_row($result)) {

            echo '<tr class="border_bottom">
                    <td><img src="img/' . $row[2] . '" height="150"></td>
                    <td><a href="getraenk.php?getraenk=' . $row[7] .'">' . $row[0] . '</a><br><small id="smalltext">' . $row[1] . ' | ' . $row[4] . '%' . '</small></td>
                    <td><div id="star" class="stars" style="--rating:' . $row[5] . '";" aria-label="Rating of this product is 2.3 out of 5."></div></td>
                    <td><a href="bewertung_getraenk.php?getraenk=' . $row[7] . '"> <sub>(' . $row[6] . ')</sub> </a></td>
                  </tr>';
        }
        ?>
    </table>
</div>
</body>
</html>