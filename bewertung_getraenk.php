<?php
session_start();
const GET_GETRAENK = 'getraenk';

require_once ('db_connection.php');
global $link;

$sql = "SELECT min(g.name), min(g.art), min(g.bild), min(g.herkunftsland), min(g.alkoholgehalt), avg(b.Sterne), count(b.Sterne), min(g.GID) FROM getränke as g
                                                                                                               left join bewertung as b
                                                                                                                         on g.GID = b.GID like '" . $_GET[GET_GETRAENK] . "'";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_row($result);

$sql_bewertungen= "select Sterne,Kommentar, Datum from bewertung where GID = '" . $_GET[GET_GETRAENK] . "'";
$result3 = mysqli_query($link,$sql_bewertungen);

$url = (isset($_SERVER['HTTPS'])?'https':'http').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

const POST_PARAM_LÖSUNG = 'lösung';
global $row;

global $link;
$smallquery = "select count(bild) from captcha";
$results2 = mysqli_query($link, $smallquery);
$numrows = mysqli_fetch_row($results2);

$query_login = "SELECT min(BID),min(Bild),min(Lösung) FROM captcha group by bild limit 1 OFFSET " . rand() % $numrows[0];
$results_login = mysqli_query($link, $query_login);
$row_login = mysqli_fetch_row($results_login);

function randomcaptcha() {
    global $row;
    echo '<img src="img/' . $row[1] . '" width="300" id="bild">';
}
?>
<html>
<head>
    <title><?php echo $row[0];?></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="getraenk.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <?php echo '<meta property="og:image" content="https://cribsen.de/img/' . $row[2] . '" />' ?>
    <style>
        body{
            font-size: 30px;
        }
    </style>
</head>
<body>

<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/de_DE/sdk.js#xfbml=1&version=v9.0" nonce="3b8FBKbX"></script>


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
<div id="getraenk_daten">
    <div>
        <?php echo '<img src="img/' . $row[2] . '" width="150">
                <br><br><div class="fb-share-button" data-href="' . $url . '" data-layout="button" data-size="small"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fcribsen.de%2Fgetraenk.php%3Fgetraenk%3D9&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Teilen</a></div>' ?>
    </div>
    <div>
        <?php echo '<b>' . $row[0] . '</b>
                <br>' . number_format($row[5], 1) . '<sterne id="star" class="stars" style="--rating:' . $row[5] . '";" aria-label="Rating of this product is 2.3 out of 5."><a href="bewertung_getraenk.php?getraenk='. $row[7] .'"> <small>(' . $row[6] . ')</small></a></sterne>
                <br>' . $row[3] . '
                <br>' . $row[1] . '
                <br>' . $row[4] . '% vol.'?>
    </div>
</div>
<div>
    <i>Alle Bewertungen:</i>
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
