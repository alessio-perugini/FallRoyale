<?php
ob_start();
session_start();

//set timezone
date_default_timezone_set('Europe/London');

//database credentials
define('DBHOST', 'localhost');
define('DBUSER', 'root');
define('DBPASS', '');
define('DBNAME', 'hektor_test');


// Create connection
$connessione =  mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
$connessione->set_charset('utf8mb4');
// Check connection
if (!$connessione) {
    die("Connection failed: " . mysqli_connect_error());
    mysqli_close($connessione);
}

require_once('classi/user.php');
$user = new user();
require_once('classi/classifica.php');
$classifica = new classifica($connessione);
require_once('classi/utils.php');
$utils = new utils($connessione);
