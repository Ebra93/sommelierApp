<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Passwort zurücksetzen</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<div id="passwortvergessen" class="passwortvergessenpopup">

    <script>
        document.getElementById('passwortvergessen').style.display='block'
    </script>

    <form class="popup-content animation" action="passwortvergessen.php" method="post">

        <div class="popup-container">
            <br><label for="login" id="passwortvegessenlabel"><b>Passwort zurücksetzen</b><br><br><br>
                <div>
                    Um Ihr Passwort zurückzusetzen, senden <br>Sie eine E-Mail an
                </div>

                <a href="mailto:support@cyber-sommelier.de"> support@cyber-sommelier.de</a><br>

               <!-- <button type="button" class="btn cancel" onclick="location.href='index.php';">Zurück</button><br>-->
            </label>
    </form>

</div>

</body>
</html>