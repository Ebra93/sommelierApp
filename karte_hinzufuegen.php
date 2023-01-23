<?php

require_once('db_connection.php');
global $link;

session_start();

$name = $_POST['eingabe'];

$result = mysqli_query($link, "select `name`, bild, art, alkoholgehalt, GID from getränke where `name` like '%" . $name . "%'");

?>

<html>
<head>
    <link rel="stylesheet" href="karte_hinzufuegen.css">
    <link rel="stylesheet" href="index.css">
    <style>
        img{
            min-width: 150px;
            min-height: 300px;
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
    <div id="top">
        Getränke meiner Karte hinzufügen
        <form action="karte_hinzufuegen.php" method="post">
            <input type="text" name="eingabe" value="<?php echo $_POST['suchen']; ?>">
            <input type="submit" name="suchen" value="suchen">
        </form>
        <button onclick="document.getElementById('eancall').style.display='block'">EAN Scan</button>
    </div>
    <?php
        if (isset($_POST['suchen']) && !is_null($_POST['suchen'])) {
            while ($row = mysqli_fetch_row($result)) {
                echo '<div class="ergebniss">
                        <div>
                            <img src="/img/' . $row[1] . '" alt="bild">
                        </div>
                        <div>
                            <u>' . $row[0] . '</u><br>
                            ' . $row[2] . ' | ' . $row[3] . '% Vol.
                        </div>
                        <div>
                        <form method="post" action="anbieter.php">
                            <input type="submit" name="hinzufuegen" value="' . $row[4] . '">plus</input>
                        </form>
                        </div>
                    </div>';
            }
        }
    ?>
    <div id="bot">
        <a href="GetraenkHinzufuegen.php">plus</a>
    </div>
    <div id="eancall" class="eanpopup">

        <div class="popup-content animation">
            <b class="popup-container">
                <b id="eantext">Die Ean Scan Funktion erscheint erst in einer späteren Version.</b>
            </div>
        </div>
    </div>



    <script>
        // Get the modal
        var modal = document.getElementById('eancall');

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
