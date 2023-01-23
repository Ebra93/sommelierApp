<?php
session_start();
require_once('db_connection.php');
global $link;

if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}

$sql2 = "SELECT g.name, g.GID
         FROM getränke g join anbieter_getränke ag on g.GID = ag.GID join user u on ag.UID = u.UID
         WHERE u.email = '" . $_SESSION['email'] . "'";
$result2 = mysqli_query($link, $sql2);

$GET_ANMELDUNG = 'anmeldung';

$sql = "SELECT g.name, g.art, g.bild, g.herkunftsland, g.alkoholgehalt, avg(b.Sterne), count(b.Sterne)
        FROM getränke as g left join bewertung as b on g.GID = b.GID
        group by g.name
        LIMIT 5";
$result = mysqli_query($link, $sql);

//
require_once("db_pdo.php");
global $mysql;

$stmt_uid = $mysql->prepare("SELECT * FROM user WHERE Email LIKE :email"); //Username überprüfen
$stmt_uid->bindParam(":email", $_SESSION['email']);
$stmt_uid->execute();

$UID = $stmt_uid->fetch();

$stmt = $mysql->prepare("SELECT * FROM anbieter WHERE UID = :uid"); //Username überprüfen
$stmt->bindParam(":uid", $UID['0']);
$stmt->execute();
$count = $stmt->rowCount();

if(!isset($_SESSION['email']) || $count != 1){
    header("Location: index.php");
}
if(isset($_SESSION['email']) || $count == 1){
    include('logged.php');
}

if(isset($_POST['hinzufuegen'])) {
    global $link;
    $anbieterID = mysqli_fetch_row(mysqli_query($link, "SELECT UID FROM user WHERE email = '" . $_SESSION['email'] . "'"));
    mysqli_query($link, "INSERT INTO anbieter_getränke(UID, GID) value (" . $anbieterID[0] . ", " . $_POST['hinzufuegen'] . ")");
    echo '<script type="text/javascript">document.getElementById(\'call\').style.display=\'block\'</script>';
}

$sql3 = "select * from anbieter where UID = '" . $UID['0'] . "'";
$result3 = mysqli_query($link,$sql3);
//$anbieterdb = mysqli_fetch_array($result3);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Sommelier</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="anbieter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="wrunner-default-theme.css">
    <script src="wrunner-native.js"></script>
</head>
<body>

<h1 id="anbieterTitle">Mein Geschäft</h1>

<form action="anbieter_check.php" method="post" enctype="multipart/form-data">

    <div class="grid-container1">
        <div class="grid-item">
            <div class="image-upload">
                <label for="file-input">
                    <img src="https://icon-library.net/images/upload-photo-icon/upload-photo-icon-21.jpg"/>
                </label>

                <input type="file" name="uploadFile" id="file-input">
            </div>
        </div>
        <div class="grid-container3">
            <div class="grid-item"><b id="anbieterText">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name:</b>
                <input type="text" placeholder="" name="name" value="<?php echo $anbieterdb[1] ?>" required><br></div>
            <div class="grid-item"></div>
            <div class="grid-item"><b id="anbieterText">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Adresse:</b>
                <input type="text" placeholder="" name="adresse" value="<?php echo $anbieterdb[5] ?>" required><br>
            </div>
            <div class="grid-item"></div>
            <div class="grid-item"><b id="anbieterText">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Webseite:</b>
                <input type="text" placeholder="" name="webseite" value="<?php echo $anbieterdb[3] ?>"><br>
                <div class="grid-container2">
                    <div class="grid-item"><b id="anbieterText2">
                            &nbsp;&nbsp;Öffnungszeiten:</b>
                        <input type="text" placeholder="" name="oeffnungszeiten" value="<?php echo $anbieterdb[6] ?>"><br>
                    </div>
                    <div class="grid-item"><b id="anbieterText2">
                        Telefonnummer:</b>
                        <input type="text" placeholder="" name="telefonnummer" value="<?php echo $anbieterdb[7] ?>"><br>
                    </div>
                    <div class="grid-item"><b id="anbieterText2">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Beschreibung:</b>
                        <input type="text" placeholder="" name="beschreibung" id="anbieterBeschreibung" maxlength="500" value="<?php echo $anbieterdb[2] ?>"><br><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    while ($row = mysqli_fetch_row($result2)) {
        echo '<div class="getraenk_liste">' . $row[0] . '<input type="checkbox" name="empfohlen[]" value="' . $row[1] . '"><label for="empfohlen">Empfohlen</label><input type="checkbox" name="entfernen[]" value="' . $row[1] . '"><label for="entfernen">Löschen</label></div>';
    }
    ?>

    <button type="submit" id="anbieterSpeichern">Speichern</button>
    <button type="button" id="anbieterAbbrechen" onclick="location.href='anbieter.php';">Abbrechen</button>
</form>

<div class="karte_hinzufugen">
    <button id="karte" onclick="document.getElementById('card').style.display='block'">+</button>
</div>

<div id="card" class="kartepopup">
    <div class="popup-content animation">
        <div class="popup-container">
            <form action="karte_hinzufuegen.php" method="post">
                <h1 id="getraenk_h1">Getränke meiner Karte hinzufügen</h1>
                <div class="f">
                    <input type="text" name="suchen" placeholder="suchen" id="suchen"><br>
                    <span class="left"><input type="submit" name="search" value="Suchen"></span>
                    <span class="right"><input type="button" name="EAN" value="EAN Scan" onclick="document.getElementById('ean').style.display='block'"></span>
                </div>
            </form>
            <hr>
            <form action="csvUpload.php" enctype="multipart/form-data" method="post">
                <h1 style="text-align: center">CSV hochladen</h1>
                <div class="f">
                    <span class="left"><input type="file" accept="csv" name="file" value="Datei auswählen"></span>
                    <span class="right"><input type="submit" id="upload" value="Datei hochladen"></span>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="call" class="register-popup">

    <div class="popup-content3 animation2">
        <div class="popup-container2" id="farbe">
            Ihre Auswahl wurde erfolgreich übernommen
        </div>
    </div>
</div>

<div id="ean" class="register-popup">

    <div class="popup-content3 animation2">
        <div class="popup-container2">
            Die EAN Scan Funktion erscheint erst in einer späteren Version.
        </div>
    </div>
</div>
<div id="call" class="register-popup">
    <?php
    if(isset($_POST['hinzufuegen'])) {
        echo '<script>document.getElementById("call").style.display="block"</script>';
    }
    ?>
</div>
<script>
    // Get the modal
    var modal = document.getElementById('card');

    window.addEventListener("click", function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
            window.location.href = 'anbieter.php';
        }
    });

    var modal5 = document.getElementById('call');

    window.addEventListener("click", function(event) {
        if (event.target === modal5) {
            modal5.style.display = "none";
            window.location.href = 'anbieter.php';
        }
    });

    var ean = document.getElementById('ean');

    window.addEventListener("click", function(event) {
        if (event.target === ean) {
            ean.style.display = "none";
        }
    });
</script>

</body>
</html>