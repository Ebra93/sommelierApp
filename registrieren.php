<?php
require_once('db_connection.php');

const POST_PARAM_LÖSUNG = 'lösung';
global $row;

global $link;
$smallquery = "select count(bild) from captcha";
$results2 = mysqli_query($link, $smallquery);
$numrows = mysqli_fetch_row($results2);

$query = "SELECT * FROM captcha group by bild limit 1 OFFSET " . rand() % $numrows[0];
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        #bild {
            float: top;
            width: 50%;
        }
    </style>
</head>
<body>

<div id="loginall" class="register-popup">

    <script>
        document.getElementById('loginall').style.display='block'
    </script>

    <form class="popup-content animation" action="registrieren_check.php" method="post">

        <div class="popup-container">
            <br><label for="login" id="registerlabel"><b>Registrierung</b><br>
                <?php
                if ($_GET['registrierung'] == 'femail')
                {
                    echo '<p align="center" id="falschesLogin">Fehler bei der
                    Registrierung. <br> Bitte versuchen Sie es erneut</p>';
                }
                else{
                    echo '<br>';
                }
                ?>
                <div>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;E-Mail:
                    <input type="email" placeholder="" name="email" id="registerInput" required><br>

                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Passwort:
                    <input type="password" placeholder="" name="psw" id="registerInput" required><br>

                    Wiederholen:
                    <input type="password" placeholder="" name="psw2" id="registerInput" required>

                    <br><br>Bitte wählen Sie Ihre Rolle aus:<br>
                    <select name="rolle">
                        <option value="Kunde">Kunde</option>
                        <option value="Anbieter">Anbieter</option>
                    </select>
                    <br>
                    <?php randomcaptcha(); ?><br>
                    <input type="number" placeholder="Lösung" id="eingabe" name="eingabe">
                    <input type="hidden" name="loesung" value="<?php echo $row[2] ?>">
                </div>
                <!--<input type="text" placeholder="E-Mail" name="email" required><br>
                <input type="password" placeholder="Passwort" name="psw" required>-->
                <button type="submit" name="submit">Registrieren</button>
                <button type="button" class="btn cancel" onclick="location.href='index.php';">Zurück</button><br>
            </label>
    </form>

</div>

<script>
    // Get the modal
    var modal = document.getElementById('loginall');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
            window.location.href = "index.php";
        }
    }
</script>


</body>
</html>