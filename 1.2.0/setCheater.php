<?php 
require_once('includes/config.php');

$username = $_POST['usern'];
$password = $_POST['password'];
$punteggio = $_POST['punteggio'];

insertCheater($username, $password, $punteggio);

function insertCheater($username, $password, $punteggio){
	$queryInsert = "INSERT INTO cheater(utente_fk, data, punteggio) VALUES ( (SELECT id FROM utenti WHERE username = ? AND password = ?), now(), ?)";
			
	if($stmt = $GLOBALS['connessione']->prepare($queryInsert))
	{
		$stmt->bind_param('ssi', $username, $password, $punteggio);
        $stmt->execute();
	}
}

?>