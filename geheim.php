<?php
session_start();
if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit;
}
echo $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>
<h1>Top Secret</h1>
<a href="logout.php">Abmelden</a>
</body>
</html>