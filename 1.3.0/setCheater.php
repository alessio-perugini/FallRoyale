<?php 
require_once('includes/config.php');

$username = $_POST['usern'];
$password = $_POST['password'];
$punteggio = $_POST['punteggio'];

insertCheater($username, $password, $punteggio);

function insertCheater($username, $password, $punteggio)
{
    $query = "INSERT INTO cheater(utente_fk, data, punteggio) VALUES ( (SELECT id FROM utenti WHERE username = ? AND password = ?), now(), ?)";

    $GLOBALS['utils']->query($query, array('username' => $username, 'password' => $password, 'punteggio' => $punteggio), false);
}
