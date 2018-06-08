<?php
ob_start();
session_start();

//set timezone
date_default_timezone_set('Europe/London');

//database credentials
define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','RetMyuz1');
define('DBNAME','fallroyale');


// Create connection
$connessione =  mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
$connessione->set_charset('utf8mb4');
// Check connection
if (!$connessione) {
    die("Connection failed: " . mysqli_connect_error());
		mysqli_close($connessione);
}

try {

	//create PDO connection
	$db = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
	//show error
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
    exit;
}

?>
