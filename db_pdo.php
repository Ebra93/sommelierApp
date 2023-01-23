<?php
Global $mysql;
$host = 'localhost';
$name = 'sommelierdb';
$user = 'root';
$passwort = '1234';
$mysql = new PDO("mysql:host=$host;dbname=$name", $user, $passwort);