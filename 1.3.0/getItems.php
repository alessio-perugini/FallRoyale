<?php
require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");
$username = $_POST['usern'];
$password = $_POST['password'];

echo json_encode(array('item' => getItemsAcquired($username, $password)));

function getItemsAcquired($username, $password)
{
    $query = "SELECT codice as codice FROM item_acquistati, items WHERE item_acquistati.id_utente_fk = (SELECT id FROM utenti WHERE username = ? AND password = ?) AND items.id = item_acquistati.id_item_fk";
    
    return $GLOBALS['utils']->query($query, array('username' => $username, 'password' => $password));
}
