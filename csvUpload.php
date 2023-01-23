<?php
ob_start();
require_once ('db_connection.php');
global $link;

session_start();
$anbieterID = mysqli_query($link, "select UID from `user` where email like '" . $_SESSION['email'] . "'");
$anbieterID = mysqli_fetch_row($anbieterID);
echo $_SESSION['email'];
$getränkesql = "Select `name`, Ean, GID from getränke";
$foundG = false;
$foundIhs = false;

$file = $_FILES['file']['tmp_name'];
echo $file;
if(empty($file)) {
    header('Location: anbieter.php');
}

$arrResult  = array();
$handle     = fopen($file, "r");
if(empty($handle) === false) {
    while(($data = fgetcsv($handle, 1000, ";")) !== FALSE){
        $arrResult[] = $data;
    }
    fclose($handle);
}

foreach($arrResult as $itemcsv) {
    $foundG = false;
    $results = mysqli_query($link, $getränkesql);
    while($row = mysqli_fetch_row($results)) {
        if(strpos(strtolower($row[0]),strtolower($itemcsv[0])) !== false || strpos(strtolower($row[1]),strtolower($itemcsv[1])) !== false) { //wenn selbe EAN oder name in DB gefunden dann füge es nur beim anbieter hinzu
            $foundG = true;
        }
        else { //getränk existiert nicht#

        }
    }
    if($foundG == true) {
        $resultGetraenkID = mysqli_query($link, "Select GID from getränke where `name` like '" . $row[0] . "'"); //Getränke ID herholen
        $getraenkID = mysqli_fetch_row($resultGetraenkID);
        mysqli_query($link, "Insert into anbieter_getränke(UID, GID) values (" . $anbieterID[0] . "," . $getraenkID[0] . ")");
    } else {
        mysqli_query($link, "insert into getränke(`Name`,Produkt,Art, Herkunftsland,Alkoholgehalt,EAN) values('" . $itemcsv[0] . "','" . $itemcsv[2] . "','" . $itemcsv[3] . "','" . $itemcsv[4] . "','" . $itemcsv[6] . "','" . $itemcsv[1] . "')");
        $resultGetraenkID = mysqli_query($link, "Select GID from getränke where `name` like '" . $itemcsv[0] . "'"); //Getränke ID herholen
        $getraenkID = mysqli_fetch_row($resultGetraenkID);
        mysqli_query($link, "Insert into anbieter_getränke(UID, GID) values (" . $anbieterID[0] . "," . $getraenkID[0] . ")");
    }

    $neueIhsID = []; //falls neuer Inhaltsstoff in Tabelle eingefügt wird, wird die ID hier zwischengespeichert.
    $foundID = 0;
    $explode = explode(',', $itemcsv[5]);
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
        if ($found) {
            $resultIhs = mysqli_query($link, "Select IID from inhaltsstoffe Where `name` like '" . $foundName . "'"); //wenn schon existiert, ID in array schreiben
            array_push($neueIhsID, mysqli_fetch_row($resultIhs));
        } else {
            $insertInhaltsstoffe = "Insert into inhaltsstoffe(`name`) values('" . $ihs1 . "')";
            mysqli_query($link, $insertInhaltsstoffe); //Inhaltsstoff hinzufügen
            $resultIhs = mysqli_query($link, "Select IID from inhaltsstoffe Where `name` like '" . $ihs1 . "'"); //InhaltsstoffID in Array schreiben
            array_push($neueIhsID, mysqli_fetch_row($resultIhs));
        }
    }
    $resultGetraenkID = mysqli_query($link, "Select GID from getränke where `name` like '" . $itemcsv[0] . "'"); //Getränke ID herholen
    $getraenkID = mysqli_fetch_row($resultGetraenkID);
    mysqli_query($link, "Insert into anbieter_getränke(UID, GID) values (" . $anbieterID[0] . "," . $getraenkID[0] . ")");
    foreach($neueIhsID as $ihs) { //inhaltsstoffe zu dem Getränk in die Tabelle einfügen
        echo '<br>' . $ihs[0] . ' == ' . $getraenkID[0];
        mysqli_query($link,"Insert into getränk_hat_inhaltsstoffe(IID,GID) values(" . $ihs[0] . "," . $getraenkID[0] . ")");
    }
}
header("Location: anbieter.php");
die();

?>