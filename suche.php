<?php

session_start();

const GET_SUCHANFRAGE = 'suchanfrage';
const GET_HERKUNFT = 'herkunft';
const GET_PRODUKT = 'produkte';
const GET_BEWERTUNG = 'bewertung';
const GET_ALLERGENE = 'allergene';
const GET_ALKOHOLHEHALT_MIN = 'slidervalueMin';
const GET_ALKOHOLHEHALT_MAX = 'slidervalueMax';

require_once('db_connection.php');
global $link;
$erfolg = false;
global $row;

$art = "";
$suchanfrage = $_GET[GET_SUCHANFRAGE];
$herkunft = $_GET[GET_HERKUNFT] ?? '%';
$produkt = $_GET[GET_PRODUKT] ?? "%";
$bewertung = $_GET[GET_BEWERTUNG] ?? 0;
$allergene =  $_GET[GET_ALLERGENE] ?? 1;
$alkoholgehaltMin = $_GET[GET_ALKOHOLHEHALT_MIN] ?? 0;
$alkoholgehaltMax = $_GET[GET_ALKOHOLHEHALT_MAX] ?? 100;
if($herkunft == '') {
    $herkunft = '%';
}
if($produkt == 'Bier') {
    $art = $_GET['artBier'];
} else if($produkt == 'Wein') {
    $art = $_GET['artWein'];
} else {
    $art = '%';
}
if($produkt == '') {
    $produkt = '%';
}
if($alkoholgehaltMin == '') {
    $alkoholgehaltMin = 0;
}
if($alkoholgehaltMax == '') {
    $alkoholgehaltMax = 100;
}
if(isset($_GET['allergene']) && $_GET['allergene'] == "on") {
    $allergene = " and i.allergen IS NULL";
} else {
    $allergene = "";
}

if($bewertung > 0) {
    $bewertung = 'having bewertung>=' . $bewertung;
} else {
    $bewertung = '';
}


//debugging
//echo 'herkunft: ' . $herkunft . ', art: ' . $art . ', produkt: ' . $produkt . ', bewertung: ' . $bewertung . ', allergene: ' . $allergene . ', Alkoholgehalt_minimum: ' . $alkoholgehaltMin . ', Alkoholgehalt_maximum: ' . $alkoholgehaltMax;




function table() {
    global $link, $herkunft, $art, $produkt, $bewertung, $allergene, $alkoholgehaltMin, $alkoholgehaltMax, $suchanfrage;
    if (strlen($_GET[GET_SUCHANFRAGE]) <= 2) {
        header("Location: index.php?suche=falsch");
    } else {
        $query = "SELECT g.*, avg(b.Sterne) as bewertung, count(distinct b.sterne), i.allergen FROM getränke as g
                left outer join bewertung as b on g.GID = b.GID
                left outer join getränk_hat_inhaltsstoffe ghi on g.GID = ghi.GID
                left outer join inhaltsstoffe as i on ghi.IID = i.IID
                where g.name like '%" . $suchanfrage . "%' and g.Art like '" . $art . "' and g.Herkunftsland like '" . $herkunft . "' and g.Alkoholgehalt between '" . $alkoholgehaltMin . "' and '" . $alkoholgehaltMax . "' '" .  $allergene . "'
                group by g.name " . $bewertung; //insert query

        $result = mysqli_query($link, $query);
        if(mysqli_num_rows($result) == 0) {
            header("Location: index.php?suche=falsch");
        }
        include('nav_suche.php');
        echo '<div id="ergebnis"><table id="tabelle">';
        while($row = mysqli_fetch_row($result)) {
            echo '<tr><td><img src="./img/' . $row[4] . '" height="100"></td><td><a href="getraenk.php?getraenk='. $row[0] .'">' . $row[1] . '</a><br><small id="smalltext">' . $row[3] . ' | ' . $row[6] . '%' . '</small></td><td><div id="star" class="stars" style="--rating:' . $row[8] . '";" aria-label="Rating of this product is 2.3 out of 5."></div></td><td><sub>(' . $row[9] . ')</sub></td></tr>';
        }
        echo '</table></div>';
    }
}


?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="suche.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Suche</title>
</head>
<body>

<?php
if(!isset($_SESSION['email'])){
    echo '<img src="./img/benutzerbild.png" onclick="document.getElementById(\'loginall\').style.display=\'block\'" id="benutzerbild">
<a href="index.php"><img src="./img/logo.png" alt="Logo nicht gefunden" id="logo"></a>
';
}
if(isset($_SESSION['email'])){
    include('logged.php');
}
?>

<?php table(); ?>

</body>
</html>
