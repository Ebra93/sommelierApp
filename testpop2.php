<?php

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {font-family: Arial, Helvetica, sans-serif;}

        /* Full-width input fields */
        input[type=text], input[type=password] {
            width: 70%;
            padding: 7px 21px;
            margin: 4px 0;
            display: inline-block;
            border: 1px solid black;
            box-sizing: border-box;;
        }

        /* Set a style for all buttons */
        button {
            background-color: white;
            color: black;
            padding: 7px 20px;
            margin: 8px 0;
            border: solid black 1px;
            cursor: pointer;
            width: 40%;
        }

        button:hover {
            opacity: 0.8;
        }

        /* Center the image and position the close button */

        .popup-container {
            padding: 16px;
        }

        /* The Modal (background) */
        .loginpopup {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px;
        }

        /* Modal Content/Box */
        .popup-content {
            background-color: #fefefe;
            margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
            border: 1px solid #888;
            width: 30%; /* Could be more or less, depending on screen size */
            height: 50%;
        }

        #loginlabel{
            display: block;
            text-align: center;
            line-height: 150%;
            font-size: .85em;
        }

        /* Add Zoom Animation */
        .animation {
            -webkit-animation: animatezoom 0.6s;
            animation: animatezoom 0.6s
        }

    </style>
</head>
<body>

<img src="/img/benutzerbild.png" onclick="document.getElementById('id01').style.display='block'" style="width:5%;">

<div id="id01" class="loginpopup">

    <form class="popup-content animation" action="anmeldung.php" method="post">

        <div class="popup-container">
            <label for="login" id="loginlabel"><h2>Login</h2><br>
            <input type="text" placeholder="E-Mail" name="email" required><br>

            <input type="password" placeholder="Passwort" name="psw" required><br><br><br>

            <button type="submit">Anmelden</button><br>
            <span><a href="#">Neues Konto erstellen</a></span>
            </label>
    </form>
</div>

<script>
    // Get the modal
    var modal = document.getElementById('id01');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
