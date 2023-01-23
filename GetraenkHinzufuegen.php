<?php
use sommelierdb;
require_once('db_connection.php');
global $link;

session_start();
$anbieterID = mysqli_query($link, "select UID from `user` where email like '" . $_SESSION['email'] . "'");
$anbieterID = mysqli_fetch_row($anbieterID);

if(isset($_FILES["fileToUpload"]["name"])) {

    $target_dir = "img/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 2000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    $neueIhsID = []; //falls neuer Inhaltsstoff in Tabelle eingefügt wird, wird die ID hier zwischengespeichert.
    $foundID = 0;
    if ($uploadOk != 0) {
        $sql = "insert into getränke(`Name`,Produkt,Art,Bild,Herkunftsland,Alkoholgehalt,EAN) values('" . $_POST['Name'] . "','" . $_POST['Produkt'] . "','" . $_POST['Art'] . "','" . $_FILES["fileToUpload"]["name"] . "','" . $_POST['Herkunftsland'] . "','" . $_POST['Alkoholgehalt'] . "','" . $_POST['EAN'] . "')";
        mysqli_query($link, $sql);
        $postIhs = str_replace(' ', '', $_POST['Inhaltsstoffe']);
        $explode = explode(',', $postIhs);
        foreach($explode as $ihs1) {
            $found = false;
            $query = "Select `name`, IID from inhaltsstoffe";
            $result = mysqli_query($link, $query);
            while ($row = mysqli_fetch_row($result)) {
                echo '<br>' . $row[0] . ' == ' . $ihs1;
                if (strpos(strtolower($row[0]), strtolower($ihs1)) !== false) { //wenn Übergebener Inhaltsstoff nicht in Tabelle dann hinzufügen
                    $foundName = $row[0];
                    $found = true;
                } else {

                }
            }
            echo 'gefunden: ' . json_encode($found);
            if($found) {
                $resultIhs = mysqli_query($link, "Select IID from inhaltsstoffe Where `name` like '" . $foundName . "'"); //wenn schon existiert, ID in array schreiben
                array_push($neueIhsID, mysqli_fetch_row($resultIhs));
            } else {
                $insertInhaltsstoffe = "Insert into inhaltsstoffe(`name`) values('" . $ihs1 . "')";
                mysqli_query($link, $insertInhaltsstoffe); //Inhaltsstoff hinzufügen
                $resultIhs = mysqli_query($link, "Select IID from inhaltsstoffe Where `name` like '" . $ihs1 . "'"); //InhaltsstoffID in Array schreiben
                array_push($neueIhsID, mysqli_fetch_row($resultIhs));
            }
        }
        $resultGetraenkID = mysqli_query($link, "Select GID from getränke where `name` like '" . $_POST['Name'] . "'"); //Getränke ID herholen
        $getraenkID = mysqli_fetch_row($resultGetraenkID);
        mysqli_query($link, "Insert into anbieter_getränke(UID, GID) values (" . $anbieterID[0] . "," . $getraenkID[0] . ")");
        foreach($neueIhsID as $ihs) { //inhaltsstoffe zu dem Getränk in die Tabelle einfügen
            echo '<br>' . $ihs[0] . ' == ' . $getraenkID[0];
            mysqli_query($link,"Insert into getränk_hat_inhaltsstoffe(IID,GID) values(" . $ihs[0] . "," . $getraenkID[0] . ")");
        }
    }
}
?>
<html>
<head>
    <link rel="stylesheet" href="GetraenkHinzufuegen.css">
    <style>
        /* Full-width input fields */
        input[type=text], input[type=password], input[type=email] {
            width: 70%;
            padding: 7px 21px;
            margin: 4px 0;
            display: inline-block;
            border: 1px solid black;
            box-sizing: border-box;
            -webkit-box-shadow: 0 0 0px 1000px #ffffff inset !important;
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
            min-width: 100%; /* Full width */
            min-height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px;
        }
        .loginpopup2 {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow-y: scroll;
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        /* Modal Content/Box */
        .popup-content {
            background-color: #fefefe;
            margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
            border: 1px solid #888;
            width: 30%; /* Could be more or less, depending on screen size */
            height: 50%;
        }
        .popup-content2 {
            background-color: #fefefe;
            border: 1px solid #888;
            width: 11%; /* Could be more or less, depending on screen size */
            height: 15%;
        }

        #loginlabel{
            display: block;
            text-align: center;
            line-height: 150%;
            font-size: larger;
        }
        /* Add Zoom Animation */
        .animation {
            -webkit-animation: animatezoom 0.6s;
            animation: animatezoom 0.6s
        }
        .register-popup{
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 140%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 30px;
        }
        .register-form{
            background-color: #fefefe;
            margin: 5% auto 15% auto;
            border: 1px solid #888;
            width: 60%;
        }
        .register_error{
            font-weight: bold;
            color: red;
            text-align: center;
            font-size: large;
        }
        .register_erfolg{
            font-weight: bold;
            color: green;
            text-align: center;
            font-size: large;
        }
        .rolle-con{
            text-align: center;
        }

        #falschesLogin{
            color: red;
            font-size: small;
            line-height: normal;
        }

        #richtigesLogin{
            color: green;
            font-size: small;
            line-height: normal;
        }

        #neuesKontoLink{
            font-size: small;
        }

        a{
            text-decoration: none;
        }
        a:hover{
            text-decoration: underline;
        }

        /* Add Zoom Animation */
        .animate{
            -webkit-animation:  animatezoom 0.6s;
            animation: animatezoom 0.6s;
        }

        #anmelden_button {
            width: auto;
        }

        @-webkit-keyframes animatezoom {
            from {-webkit-transform: scale(0)}
            to {-webkit-transform: scale(1)}
        }

        @keyframes animatezoom {
            from {transform: scale(0)}
            to {transform: scale(1)}
        }
        #anmelden {
            display: grid;
            grid-template-columns: 50% 50%;
        }

        #jaja{
            border: 1px solid black;
            width: 70%;
            margin-bottom: 2%;
            margin-left: 15%;
        }

        #logo{
            width: 10%;
            float: right;
            margin-top: 1%;
            margin-right: 1%;
        }

        #benutzerbild{
            width:5%;
        }
        #empfehlungen > #tabelle {
            table-layout: auto;
            width: 100%;
            border: solid black 1px;
        }
        tr.border_bottom td {
            border-bottom: 1px solid black;
        }

        #meinkonto{
            font-size: larger;
            text-decoration: none;
        }
        #abmelden{
            font-size: larger;
            text-decoration: none;
        }
        .loggedin{
            display: grid;
            grid-template-columns: auto;
        }
        .loggedin1{
            padding: 16px;
            border-bottom: solid black 1px;
        }
        #passwortvegessenlabel{
            display: block;
            text-align: center;
            line-height: 150%;
            font-size: larger;
        }
        .passwortvergessenpopup {
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
        #registerInput{
            width: 50%;
        }
    </style>
</head>
<body>
<?php
if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit;
}
if(isset($_SESSION['email'])){
    include('logged.php');
}
?>

    <h1>Getränk der Datenbank hinzufügen</h1>
    <form method="post" action="GetraenkHinzufuegen.php" enctype="multipart/form-data">
        <t id="nameLabel">Name: </t><input type="text" id="Name" name="Name" required>
        <t id="EANLabel">EAN: </t><input type="text" id="EAN" name="EAN" required>
        <t id="ProduktLabel">Produkt: </t><select id="Produkt" name="Produkt" required>
            <option value="Bier">Bier</option>
            <option value="Wein">Wein</option>
        </select>
        <t id="ArtLabel">Art: </t><input type="text" id="Art" name="Art" required>
        <t id="HerkunftslandLabel">Herkunftsland: </t><input type="text" id="Herkunftsland" name="Herkunftsland" placeholder="" required>
        <t id="InhaltsstoffeLabel">Inhaltsstoffe: </t><input type="text" id="Inhaltsstoffe" name="Inhaltsstoffe" placeholder="" required>
        <t id="AlkoholgehaltLabel">Alkoholgehalt: </t><input type="text" id="Alkoholgehalt" name="Alkoholgehalt" placeholder="" required>
        <div class="upload">
            <label for="fileToUpload">
                <img src="https://icon-library.net/images/upload-photo-icon/upload-photo-icon-21.jpg"/>
            </label>
            <input type="file" name="fileToUpload" id="fileToUpload" required>
        </div>
        <input type="submit" value="Hinzufügen" name="Hinzufügen" id="Hinzufügen">
        <input type="button" value="Zurück" name="zurück" id="zurück" onclick="location.href='anbieter.php';">
    </form>
</body>
</html>