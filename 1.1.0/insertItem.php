<?php
require_once('includes/config.php');

$username = $_POST['usern'];
$password = $_POST['password'];
$codice = $_POST['codice'];

if(!checkItemExistance($username, $password, $codice))
    setItemAcquired($username, $password, $codice);

function checkItemExistance($username, $password, $codice){  
    $query = "SELECT id FROM item_acquistati WHERE item_acquistati.id_utente_fk = (SELECT id FROM utenti WHERE username = ? AND password = ?) AND item_acquistati.id_item_fk = (SELECT id FROM items WHERE codice = ?)";
    
    if($stmt = $GLOBALS['connessione']->prepare($query))
    {
        $stmt->bind_param('sss', $username, $password, $codice);
        
        if($stmt->execute()){
            $stmt->store_result();
            $stmt->bind_result($id);
            $stmt->fetch();

            return (isset($id)) ? true : false;
        }
    }
}

function setItemAcquired($username, $password, $codice){
    $query = "INSERT INTO item_acquistati (data_acquisto, id_item_fk, id_utente_fk) VALUES (now(), (SELECT id FROM items WHERE codice = ?), (SELECT id FROM utenti WHERE username = ? AND password = ?))";
    if($stmt = $GLOBALS['connessione']->prepare($query))
    {
        $stmt->bind_param('sss', $codice, $username, $password);    
        $stmt->execute();
    }
}
?>