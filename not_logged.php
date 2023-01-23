<img src="./img/benutzerbild.png" onclick="document.getElementById('loginall').style.display='block'" id="benutzerbild">
<a href="index.php"><img src="./img/logo.png" alt="Logo nicht gefunden" id="logo"></a>

<div id="loginall" class="loginpopup">

    <form class="popup-content animation" action="index.php" method="post">

        <div class="popup-container">
            <br><label for="login" id="loginlabel"><b>Login</b>
                <?php

                if ($_GET['anmeldung'] == 'falsch')
                {
                    echo '<p align="center" id="falschesLogin">Die E-Mail und oder das Passwort
            stimmen nicht. Bitte <br> überprüfen Sie Ihre Eingaben</p>';

                    echo '<script>
                document.getElementById("loginall").style.display="block"
            </script>';
                }
                elseif ($_GET['registrierung'] == 'r'){
                 echo '<p align="center" id="richtigesLogin">Ihr Konto wurde erfolgreich erstellt!</p>';

                        echo '<script>
                document.getElementById("loginall").style.display="block"
            </script>';
                }
                else{
                    echo '<br>';
                }

                if ($_GET['registrierung'] == 'femail' || $_GET['registrierung'] == 'fpass')
                {
                    echo '<p align="center" id="falschesLogin">Fehler bei der
                Registrierung. <br> Bitte versuchen Sie es erneut</p>';

                    echo '<script>
                document.getElementById("loginall").style.display="block"
            </script>';
                }
                else{
                    echo '<br>';
                }

                ?>
                <div id="jaja"> <!-- Namenskonvention bitte einhalten, danke. -->
                    <i class="fa fa-envelope" aria-hidden="true"></i>

                    <input type="text" placeholder="E-Mail" name="email" id="email" required style="border: none;"><br>
                </div>
                <div id="jaja">
                    <i class="fa fa-lock" aria-hidden="true"></i>

                    <input type="password" placeholder="Passwort" name="psw" id="password" required style="border: none;">
                </div>
                <!--<input type="text" placeholder="E-Mail" name="email" required><br>
                <input type="password" placeholder="Passwort" name="psw" required>-->
                <?php
                if ($_GET['anmeldung'] == 'falsch')
                {
                    echo '<span id="falschesLogin"><a onclick="document.getElementById(\'passwortvergessen\').style.display=\'block\'">Passwort vergessen?</a></span>';
                    echo '<br>';
                }
                else{
                    echo '<br>';
                }
                ?>
                <br><button type="submit" name="submit" id="anmelden1">Anmelden</button><br>
                <span><a onclick="document.getElementById('registerall').style.display='block'" id="neuesKontoLink">Neues Konto erstellen</a></span>
            </label>
    </form>
</div>

<div id="registerall" class="register-popup">

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
            </label></div>
    </form>
</div>
</div>


<div id="passwortvergessen" class="passwortvergessenpopup">

    <form class="popup-content animation" action="index.php" method="post">

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

<script>
    // Get the modal
    var modal3 = document.getElementById('loginall');

    window.addEventListener("click", function(event) {
        if (event.target === modal3) {
            modal3.style.display = "none";
        }
    });
</script>

<script>
    // Get the modal
    var modal4 = document.getElementById('registerall');

    window.addEventListener("click", function(event) {
        if (event.target === modal4) {
            modal4.style.display = "none";
        }
    });
</script>

<script>
    // Get the modal
    var modalre = document.getElementById('passwortvergessen');

    window.addEventListener("click", function(event) {
        if (event.target === modalre) {
            modalre.style.display = "none";
        }
    });
</script>
</div>

