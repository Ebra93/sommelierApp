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

$sql2 = "select avg(Sterne)  ";

$sql3 = "  select Sterne,Kommentar,Datum from bewertung_anbieter
                                         where AID like '". $_GET[GET_ANBIETER] ."'
                                         order by Sterne DESC
                                         LIMIT 1 ";
$result3 = mysqli_query($link,$sql3);

$sql4 = "select g.GID, g.Bild, g.Name, g.Art, g.Alkoholgehalt, avg(b.Sterne) as v, count(b.Sterne)
from getränke g join anbieter_getränke ag on g.GID = ag.GID
join bewertung b on g.GID = b.GID
where ag.UID = '". $_GET[GET_ANBIETER] ."' and ag.empfohlen = 1
group by Name
order by v DESC
LIMIT 3
";
$result4 = mysqli_query($link,$sql4);

$sql5 = "select g.GID, g.Bild, g.Name, g.Art, g.Alkoholgehalt, avg(b.Sterne) as v, count(b.Sterne)
from getränke g join anbieter_getränke ag on g.GID = ag.GID
join bewertung b on g.GID = b.GID
where ag.UID = '". $_GET[GET_ANBIETER] ."'
group by Name";
$result5 = mysqli_query($link,$sql5);

$address = $anbieter_daten['Adresse'];
$address = str_replace(" ", "+", $address);

$row = mysqli_fetch_row($result);

const POST_PARAM_LÖSUNG = 'lösung';
global $row2;

$smallquery = "select count(bild) from captcha";
$results2 = mysqli_query($link, $smallquery);
$numrows = mysqli_fetch_row($results2);

$query = "SELECT * FROM captcha group by bild limit 1 OFFSET " . rand() % $numrows[0];
$results = mysqli_query($link, $query);
$row2 = mysqli_fetch_row($results);

function randomcaptcha() {
    global $row2;
    echo '<img src="img/' . $row2[1] . '" width="300" id="bild">';
}
?>

<html>
<head>
    <title>Geschäft</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="geschaeft.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <?php echo '<meta property="og:image" content="https://cribsen.de/img/' . $row[4] . '" />' ?>
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
        <div class="fb-share-button" data-href="https://cribsen.de/geschaeft.php?anbieter=<?php echo $_GET[GET_ANBIETER] ?>" data-layout="button" data-size="large" style="padding-left: 60px"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Flocalhost%3A63342%2Fdb_connection.php%2Fgeschaeft.php%3Fanbieter%3D4&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Teilen</a></div>
    </div>
    <div class="right">
        <?php echo '<b>'. $anbieter_daten["Name"] .'</b> <br>'
            . number_format($anbieter_bewertungen["sAVG"], 1);
        if (isset($_SESSION['email'])){
            echo  '<button onclick="document.getElementById('."'bewertungall'".').style.display=\'block\'" id="star" class="stars" style="--rating:' . $anbieter_bewertungen["sAVG"] . '";" aria-label="Rating of this product is 2.3 out of 5." ></button>';
        }
        if (!isset($_SESSION['email'])){
            echo  '<button onclick="document.getElementById('."'loginall'".').style.display=\'block\'" id="star" class="stars" style="--rating:' . $anbieter_bewertungen["sAVG"] . '";" aria-label="Rating of this product is 2.3 out of 5." ></button>';
        }
                echo '<a href="bewertung_anbieter.php?anbieter=' . $_GET[GET_ANBIETER] . '"><small><sup>(' . $anbieter_bewertungen['sCOUNT'] . ')</sup></small></a>'
                   . '<br>' . '<i style="font-size:24px" class="fa fa-map-marker"></i>'.' '. '<a href="https://maps.google.com/maps?q=' . $address .'" target="_blank">' . $anbieter_daten["Adresse"] . '</a>'
                   . '<br>' .'<i style="font-size:24px" class="fa">&#xf017;</i>'.' '. $anbieter_daten["Öffnungszeiten"];        ?>
    </div>
</div>
   <br> <?php echo $anbieter_daten["Beschreibung"] ?>
</div>
<br>
<?php

if(!isset($_POST["getraenkekarte"])) // default
{
    echo '<div>
    Empfohlene Getränke:
</div>';
    while($getraenke= mysqli_fetch_row($result4)){
        echo '<div class="getraenke_daten">
                  <div><img src="img/' . $getraenke[1] . '" width="50"></div>
                  <div><a href="getraenk.php?getraenk='.$getraenke[0].'">' . $getraenke[2] . '</a>
                  <br>'. $getraenke[3] .' | '. $getraenke[4] .' %</div>
                  <div>'. number_format($getraenke[5], 1) .'
                  <sterne id="star" class="stars" style="--rating:' . $getraenke[5] . '";" aria-label="Rating of this product is 2.3 out of 5."><a href="bewertung_getraenk.php?getraenk='. $getraenke[0] .'"> <small>(' . $getraenke[6] . ')</small></a></sterne></div>
                 </div>';
    }
    echo '<div style="text-align: center"><form action="" method="post">
                <input class="gkarte" type="submit" name="getraenkekarte" value="Getränkekarte">
            </form></div>';
}
else{
    echo '<div>
    GetränkeKarte:
</div>';
    while($getraenke2= mysqli_fetch_row($result5)){
        echo '<div class="getraenke_daten">
                  <div><img src="img/' . $getraenke2[1] . '" width="50"></div>   
                  <div><a href="getraenk.php?getraenk='.$getraenke2[0].'">' . $getraenke2[2] . '</a>
                  <br>'. $getraenke2[3] .' | '. $getraenke2[4] .' %</div>
                  <div>'. number_format($getraenke2[5], 1) .'
                  <sterne id="star" class="stars" style="--rating:' . $getraenke2[5] . '";" aria-label="Rating of this product is 2.3 out of 5."><small>(' . $getraenke2[6] . ')</small></sterne></div>
                 </div>';
    }
}
?>
<br>
<?php
if (!isset($_POST["getraenkekarte"])) { //default
    if (isset($_SESSION['email'])){
        echo  '<div><b>Bewertung</b><button onclick="document.getElementById('."'bewertungall'".').style.display=\'block\'" style="height: 40px; font-size: 20px">Bewertung schreiben</button></div>';
    }
    if (!isset($_SESSION['email'])){
        echo  '<div><b>Bewertung</b> <button onclick="document.getElementById('."'loginall'".').style.display=\'block\'" style="height: 40px; font-size: 20px">Bewertung schreiben</button></div>';
    }

    while ($bewertungen = mysqli_fetch_row($result3)) {
        echo '<div><sterne id="star" class="stars bew" style="--rating:' . $bewertungen[0] . '";" aria-label="Rating of this product is 2.3 out of 5."> </sterne>
                ' . $bewertungen[1] . '<br>
                ' . $bewertungen[2] . '
              </div>';
    }
}
    ?>

<div id="bewertungall" class="bewertungpopup">

    <form class="popup-content animation" action="anbieter_bewertung_db.php" method="post">
        <div>
            <div class="popup-container">
                <br><label id="bew_titel"><b>Bewertung schreiben</b></label>
                <br> Bewerten Sie das Geschäft:
                <br>
                <div class="stern"><p class="sternebewertung">
                        <input type="radio" id="stern5" name="bewertung" value="5" required><label for="stern5" title="5 Sterne">5 Sterne</label>
                        <input type="radio" id="stern4" name="bewertung" value="4" required><label for="stern4" title="4 Sterne">4 Sterne</label>
                        <input type="radio" id="stern3" name="bewertung" value="3" required><label for="stern3" title="3 Sterne">3 Sterne</label>
                        <input type="radio" id="stern2" name="bewertung" value="2" required><label for="stern2" title="2 Sterne">2 Sterne</label>
                        <input type="radio" id="stern1" name="bewertung" value="1" required><label for="stern1" title="1 Stern">1 Stern</label>
                    </p></div>
                <br><br><br><br><br>
                Was denken Sie über das Geschäft?
                <br><textarea id="review" name="review" rows="6" cols="80"> </textarea>
                <br><button type="submit" name="absenden" id="absenden">Absenden</button><br>
                <input type="hidden" id="anbieter" name="anbieter" value="<?php echo $_GET[GET_ANBIETER] ?>">
            </div>
    </form>
</div>



<script>
    // Get the modal
    var modal = document.getElementById('bewertungall');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>
</body>
</html>
