<?php
session_start();
const GET_GETRAENK = 'getraenk';

require_once ('db_connection.php');
global $link;

$sql = "SELECT g.name, g.art, g.bild, g.herkunftsland, g.alkoholgehalt, avg(b.Sterne) , count(b.Sterne) FROM getränke as g
                left join bewertung as b
                on g.GID = b.GID
                WHERE g.GID like '" . $_GET[GET_GETRAENK] . "'";
$sql2 = "SELECT a.UID, a.Name, a.Bild , avg(b.Sterne) v, count(b.Sterne)
                FROM anbieter as a join anbieter_getränke as ag on a.UID = ag.UID join getränke as g on g.GID = ag.GID
                left join bewertung_anbieter b on a.UID = b.AID
                WHERE g.GID like '" . $_GET[GET_GETRAENK] . "'
                group by a.Name
                order by v DESC";
$sql3 = "SELECT count(a.UID) as anz
                FROM anbieter as a join anbieter_getränke as ag on a.UID = ag.UID join getränke as g on g.GID = ag.GID
                WHERE g.GID like '" . $_GET[GET_GETRAENK] . "'";
$sql4 = "SELECT i.Name
                FROM inhaltsstoffe i join getränk_hat_inhaltsstoffe ghi on i.IID = ghi.IID join getränke g on g.GID = ghi.GID
                WHERE g.GID = " . $_GET[GET_GETRAENK];
$sql5 = "SELECT BewID, Sterne, Kommentar, Datum 
                FROM bewertung 
                WHERE GID = " . $_GET[GET_GETRAENK] . " 
                ORDER BY BewID DESC LIMIT 1";



$result = mysqli_query($link, $sql);
$result2 = mysqli_query($link, $sql2);
$result3 = mysqli_query($link, $sql3);
$result4 = mysqli_query($link, $sql4);
$reslut5 = mysqli_query($link, $sql5);

$row = mysqli_fetch_row($result);

$url = (isset($_SERVER['HTTPS'])?'https':'http').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

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
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $row[0];?></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="getraenk.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <?php echo '<meta property="og:image" content="https://cribsen.de/img/' . $row[2] . '" />' ?>
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
    <div class="getraenk">
        <?php echo '<img src="img/' . $row[2] . '" width="50">
                <br><br><div class="fb-share-button" data-href="' . $url . '" data-layout="button" data-size="small"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fcribsen.de%2Fgetraenk.php%3Fgetraenk%3D9&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Teilen</a></div>' ?>
    </div>
    <div>
        <?php echo '<b>' . $row[0] . '</b>
                <br>' . number_format($row[5], 1);
       if (isset($_SESSION['email'])){
            echo  '<button onclick="document.getElementById('."'bewertungall'".').style.display=\'block\'" id="star" class="stars" style="--rating:' . $row[5] . '";" aria-label="Rating of this product is 2.3 out of 5." ></button>';
        }
        if (!isset($_SESSION['email'])){
            echo  '<button onclick="document.getElementById('."'loginall'".').style.display=\'block\'" id="star" class="stars" style="--rating:' . $row[5] . '";" aria-label="Rating of this product is 2.3 out of 5." ></button>';
        }
                echo '<a href="bewertung_getraenk.php?getraenk=' . $_GET[GET_GETRAENK] . '"><sup id="anz_bew_getr">(' . $row[6] . ')</sup></a><!-- muss noch zur Bewertungsseite führen (u8)-->
                <br>' . $row[3] . '
                <br>' . $row[1] . '
                <br>' . $row[4] . '% vol.';?>
    </div>
    <div>
        Inahltsstoffe:
        <?php
        $inhalt_aufz = 0;
        while ($row3 = mysqli_fetch_row($result4)) {
            if ($inhalt_aufz > 0) {
                echo ', ';
            }
            echo $row3[0];
            $inhalt_aufz++;
        }
        ?>
    </div>
</div><br>
<?php
$row2 = mysqli_fetch_assoc($result3);
if ($row2['anz'] > 3) {
    echo '<div>
            <i>Dieses Getränk finden sie hier:</i>
        </div>';
    if (!isset($_POST['anzeige_getraenk'])) {//default
        $count_anbieter = 0;
        while ($row = mysqli_fetch_row($result2)) {
            if ($count_anbieter < 3) {
                echo '<div class="anbieter_daten">
                    <div><img src="img/' . $row[2] . '" width="50"></div>
                    <div><a href="geschaeft.php?anbieter='. $row[0] .'">' . $row[1] . '</a><br>Standort</div>
                   
                    <div>'. number_format($row[3], 1) .'
                  <sterne id="star" class="stars" style="--rating:' . $row[3] . '";" aria-label="Rating of this product is 2.3 out of 5."> <a href="bewertung_anbieter.php?anbieter='.$row[0] .'"> <small>(' . $row[4] . ')</small></a></sterne></div>
                </div>';
                $count_anbieter++;
            }
        }
    }
    else {//wenn mehr geklickt wurde
        $count_anbieter = 0;
        while ($row = mysqli_fetch_row($result2)) {
            if ($count_anbieter < 20) {
                echo '<div class="anbieter_daten">
                    <div><img src="img/' . $row[2] . '" width="50"></div>
                    <div><a href="geschaeft.php?anbieter='.$row[0].'">' . $row[1] . '</a><!-- name muss zu Geschäftsseite führen (u9) --><br>Standort</div>
                     <div>'. number_format($row[3], 1) .'
                  <sterne id="star" class="stars" style="--rating:' . $row[3] . '";" aria-label="Rating of this product is 2.3 out of 5."> <a href="bewertung_anbieter.php?anbieter='.$row[0] .'"> <small>(' . $row[4] . ')</small></a></sterne></div>
                </div>
                </div>';
                $count_anbieter++;
            }
        }
    }
    if (!isset($_POST['anzeige_getraenk'])) {
        echo '<form action="" method="post">
                <input type="hidden" name="anzeige_getraenk" value="1">
                <input type="submit" name="mehr" value="Mehr">
            </form>';
    }
}

elseif ($row2['anz'] <= 3 && $row2['anz'] > 0) {
    echo '<div>
            <i>Dieses Getränk finden sie hier:</i>
        </div>';
    while ($row = mysqli_fetch_row($result2)) {
        echo '<div class="anbieter_daten">
                <div><img src="img/' . $row[2] . '" width="50"></div>
                <div><a href="geschaeft.php?anbieter='.$row[0].'">' . $row[1] . '</a><br>Standort</div>
                <div>'. number_format($row[3], 1) .'
                 <sterne id="star" class="stars" style="--rating:' . $row[3] . '";" aria-label="Rating of this product is 2.3 out of 5."> <a href="bewertung_anbieter.php?anbieter='.$row[0] .'"> <small>(' . $row[4] . ')</small></a></sterne></div>
                </div>
            </div>';
    }
    if (!isset($_POST['anzeige_getraenk'])) {
        echo '<form action="" method="post">
                <input type="hidden" name="anzeige_getraenk" value="1">
                <input type="submit" name="mehr" value="Mehr">
            </form>';
    }
}

else {

}
?>
<br>
<div>
    Bewertungen
    <?php
        if (isset($_SESSION['email'])){
            echo  '<button onclick="document.getElementById('."'bewertungall'".').style.display=\'block\'" style="height: 40px; font-size: 20px">Bewertung schreiben</button>';
        }
    if (!isset($_SESSION['email'])){
        echo  '<button onclick="document.getElementById('."'loginall'".').style.display=\'block\'" style="height: 40px; font-size: 20px">Bewertung schreiben</button>';
    }
    ?>
</div>
<?php
$row5 = mysqli_fetch_row($reslut5);
echo '<sterne id="star" class="stars" style="--rating:' . $row5[1] . '";" aria-label="Rating of this product is 2.3 out of 5."></sterne>' . $row5[2] . '<br>' . $row5[3];
?>

<div id="bewertungall" class="bewertungpopup">

    <form class="popup-content animation" action="bewertung_db.php" method="post">
        <div>
            <div class="popup-container">
                <br><label id="bew_titel"><b>Bewertung schreiben</b></label>
                <br> Bewerten Sie das Produkt:
                <br>
                <div class="stern"><p class="sternebewertung">
                        <input type="radio" id="stern5" name="bewertung" value="5" required><label for="stern5" title="5 Sterne">5 Sterne</label>
                        <input type="radio" id="stern4" name="bewertung" value="4" required><label for="stern4" title="4 Sterne">4 Sterne</label>
                        <input type="radio" id="stern3" name="bewertung" value="3" required><label for="stern3" title="3 Sterne">3 Sterne</label>
                        <input type="radio" id="stern2" name="bewertung" value="2" required><label for="stern2" title="2 Sterne">2 Sterne</label>
                        <input type="radio" id="stern1" name="bewertung" value="1" required><label for="stern1" title="1 Stern">1 Stern</label>
                    </p></div>
                <br><br><br><br><br>
                Was denken Sie über das Produkt?
                <br><textarea id="review" name="review" rows="6" cols="80"> </textarea>
                <br><button type="submit" name="absenden" id="absenden">Absenden</button><br>
                <input type="hidden" id="getraenk" name="getraenk" value="<?php echo $_GET[GET_GETRAENK] ?>">
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





