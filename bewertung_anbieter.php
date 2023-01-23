<?php
session_start();
const GET_ANBIETER = 'anbieter';

require_once ('db_connection.php');
global $link;

$sql = "select * from anbieter where UID like '". $_GET[GET_ANBIETER] ."'";
$result = mysqli_query($link, $sql);
$anbieter_daten = mysqli_fetch_array($result);

$sql1 = "select avg(Sterne) as sAVG, count(Sterne) as sCOUNT from bewertung_anbieter where AID like '". $_GET[GET_ANBIETER] ."'";
$result1 = mysqli_query($link,$sql1);
$anbieter_bewertungen = mysqli_fetch_array($result1);



$sql3 = "  select Sterne,Kommentar,Datum from bewertung_anbieter
                                         where AID like '". $_GET[GET_ANBIETER] ."' ";
$result3 = mysqli_query($link,$sql3);


$address = $anbieter_daten['Adresse'];
$address = str_replace(" ", "+", $address);

const POST_PARAM_LÖSUNG = 'lösung';
global $row;

global $link;
$smallquery = "select count(bild) from captcha";
$results2 = mysqli_query($link, $smallquery);
$numrows = mysqli_fetch_row($results2);

$query_login = "SELECT * FROM captcha group by bild limit 1 OFFSET " . rand() % $numrows[0];
$results_login = mysqli_query($link, $query_login);
$row_login = mysqli_fetch_row($results_login);

function randomcaptcha() {
    global $row;
    echo '<img src="img/' . $row[1] . '" width="300" id="bild">';
}
?>

<html>
<head>
    <title>Geschäft</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="geschaeft.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v9.0" nonce="2v8B5JlY"></script>
<div>
    <?php
    if(!isset($_SESSION['email'])){
        include('not_logged.php');
    }
    if(isset($_SESSION['email'])){
        include('logged.php');
    }
    ?>
</div>
<div>
    <div class="anbieterdaten">
        <div class="left">
            <?php echo '<img src="./img/'. $anbieter_daten["Bild"] .'" height="150" width="200" id="anbieterbild"> <br> ' ?>
            <div class="fb-share-button" data-href="http://localhost:63342/db_connection.php/geschaeft.php?anbieter=<?php echo $_GET[GET_ANBIETER] ?>" data-layout="button" data-size="large" style="padding-left: 60px"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Flocalhost%3A63342%2Fdb_connection.php%2Fgeschaeft.php%3Fanbieter%3D4&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div>
        </div>
        <div class="right">
            <?php echo '<b>'. $anbieter_daten["Name"] .'</b> <br>'
                . number_format($anbieter_bewertungen["sAVG"], 1)
                . '<sterne id="star" class="stars" style="--rating:' . $anbieter_bewertungen["sAVG"] . '";" aria-label="Rating of this product is 2.3 out of 5."><a href="bewertung_anbieter.php?anbieter='.$_GET[GET_ANBIETER] .' " <small>(' . $anbieter_bewertungen["sCOUNT"] . ')</small></sterne>'
                . '<br>' . '<i style="font-size:24px" class="fa fa-map-marker"></i>'.' '. '<a href="https://maps.google.com/maps?q=' . $address .'" target="_blank">' . $anbieter_daten["Adresse"] . '</a>'
                . '<br>' .'<i style="font-size:24px" class="fa">&#xf017;</i>'.' '. $anbieter_daten["Öffnungszeiten"]        ?>
        </div>
    </div>
    <br> <?php echo $anbieter_daten["Beschreibung"] ?>
</div>


<div>
    <b>Alle Bewertungen </b>
</div>
<?php
while($bewertungen= mysqli_fetch_row($result3)){
    echo '<div><sterne id="star" class="stars bew" style="--rating:' . $bewertungen[0] . '";" aria-label="Rating of this product is 2.3 out of 5."> </sterne>
                '  . $bewertungen[1] .'<br>
                ' . $bewertungen[2] . '
              </div>';
}
?>




</body>
</html>
