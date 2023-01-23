<?php
session_start();

if(!isset($_SESSION['email'])){
    header("Location: index.php");
}
if(isset($_SESSION['email'])){
    include('logged.php');
}

include_once("db_pdo.php");
global $mysql;

$stmt_uid = $mysql->prepare("SELECT * FROM user WHERE Email LIKE :email"); //Username überprüfen
$stmt_uid->bindParam(":email", $_SESSION['email']);
$stmt_uid->execute();

$UID = $stmt_uid->fetch();

$stmt = $mysql->prepare("SELECT * FROM anbieter WHERE UID = :uid"); //Username überprüfen
$stmt->bindParam(":uid", $UID['0']);
$stmt->execute();
$count = $stmt->rowCount();

require_once ('db_connection.php');
global $link;
$sql = "select  g.GID, g.Bild, g.Name, g.Art, g.Alkoholgehalt, b.Sterne, b.Kommentar, b.Datum from bewertung b
join getränke g on b.GID = g.GID
where b.UID = '". $UID['0'] ."'";
$result = mysqli_query($link,$sql);

$sql1 = "select a.UID, a.Bild, a.Name, a.Adresse, b.Sterne, b.Kommentar, b.Datum from bewertung_anbieter b
join anbieter a on b.AID = a.UID
where b.UID =  '". $UID['0'] ."'";
$result1 = mysqli_query($link,$sql1);


$sql2 = "select k.Anzeigename, u.Email, k.Geburtsdatum from kunde k
join user u on k.UID = u.UID
where k.UID = '". $UID['0'] ."' ";
$result2 = mysqli_query($link,$sql2);
$kunde = mysqli_fetch_array($result2);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Sommelier</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="meinkonto.css">
</head>
<body>



<h1 id="meinKontoTitel">Mein Konto</h1>
<p id="kontaktText">Kontaktinformationen</p>

<form action="meinkonto_check.php" method="post">
<div class="grid-container">
    <div class="Name"><b id="kontoText">Name:</b></div>
    <div class="E-Mail"><b id="kontoText">E-Mail:</b></div>
    <div class="Geburtsdatum"><b id="kontoText">Geburtsdatum:</b></div>
    <div class="name-field"><label>
            <input type="text" name="name" value="<?php echo $kunde[0] ?>">
        </label></div>
    <div class="email-field"><label>
            <input type="email" name="email" value="<?php echo $kunde[1] ?>">
        </label></div>
    <div class="geburt-field"><label>
            <input type="date" name="geburtsdatum" value="<?php echo $kunde[2] ?>">
        </label></div>
    <?php
    if ($count == 1) {
        echo '<div class="anbieterseite"><a href="anbieter.php"><button type="button" id="anbieterSeite">Mein Geschäft</button></a></div>';
    }
    ?>
    <div class="speichern"><button type="submit" id="meinkontoSpeichern">Speichern</button></div>
    <div class="pass-ändern"><button type="button" id="passwortAendern" onclick="document.getElementById('passwortChange').style.display='block'">Passwort ändern</button></div>
</div>
</form>

<div>

    <?php
        echo ' <div class="meine_bewertungen">Meine Bewertungen</div>';
        echo '<div class="review">';
        while ($gbew = mysqli_fetch_row($result)) {
            echo '<div><img src="img/' . $gbew[1] . '" width="50"></div>
        <div><a href="getraenk.php?getraenk=' . $gbew[0] . '">' . $gbew[2] . '</a>
            <br>' . $gbew[3] . ' | ' . $gbew[4] . ' %</div>
        <div>
            <sterne id="star" class="stars" style="--rating:' . $gbew[5] . '";" aria-label="Rating of this product is 2.3 out of 5."></sterne>
                ' . $gbew[6] . '<br>
                ' . $gbew[7] . '
              </div>';
        }
        while ($abew = mysqli_fetch_row($result1)) {
            echo '<div><img src="img/' . $abew[1] . '" width="50"></div>
        <div><a href="geschaeft.php?anbieter=' . $abew[0] . '">' . $abew[2] . '</a>
            <br>' . $abew[3] . ' </div>
        <div>
            <sterne id="star" class="stars" style="--rating:' . $abew[4] . '";" aria-label="Rating of this product is 2.3 out of 5."></sterne>
                ' . $abew[5] . '<br>
                ' . $abew[6] . '
              </div>';
        }
        echo ' </div>';
    ?>
</div>

<div id="passwortChange" class="registrierenPopup">
    <form class="registrierenContent" action="passwort_aendern.php" method="post">
        Passwort ändern <br>
        Aktuelles Passwort: <input type="text" name="aktuellesPass"><br>
        Neues Passwort: <input type="text" name="neuesPass"><br>
        Passwort wiederholen: <input type="text" name="wiederholenPass"><br>
        <button type="submit" id="bestätigen">Bestätigen</button>
    </form>
</div>
<div id="password" class="registrierenPopup">
    <?php
    if ($_GET['psw'] == "r"){
        echo '<script>document.getElementById("password").style.display="block"</script>';
        echo '<form class="registrierenContent"><p class="psw_r"> Ihr Passwort wurde erfolgreich geändert   </p></form>';
    } elseif ($_GET['psw'] == "f"){
        echo '<script>document.getElementById("password").style.display="block"</script>';
        echo '<form class="registrierenContent"><p class="psw_f"> Ihr Passwort wurde nicht erfolgreich geändert   </p></form>';

    }elseif ($_GET['psw'] == "af"){
        echo '<script>document.getElementById("password").style.display="block"</script>';
        echo '<form class="registrierenContent"><p class="psw_f"> Das aktuelle Passwort ist falsch   </p></form>';
    }
    ?>
</div>

<script>
    // Get the modal
    var modal = document.getElementById('passwortChange');

    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener("click", function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Get the modal
    var modal1 = document.getElementById('password');

    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener("click", function(event) {
        if (event.target === modal1) {
            modal1.style.display = "none";
        }
    });
</script>

</body>
</html>