<?php
require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$username = $_POST['usern'];
$password = $_POST['password'];
$codice = $_POST['codice'];

$info = getInfoCodice($codice);

$errore = "";
$valore_codice = -1;

if(isset($info['id'])){
    if(isset($info['expire']))
        if (getReward($username, $password, $codice, $info['id'], $info['expire']))
            $valore_codice = setRewardTaken($username, $password, $info['id'], $info['valore']);
        else
            $errore = 'already redeemed';
    else
        if(getReward2($info['id']))
            $valore_codice = setRewardTaken($username, $password, $info['id'], $info['valore']);
        else
            $errore = 'already redeemed';
}else
    $errore = "invalid code";

echo json_encode(array('valore' => $valore_codice, 'errore' => $errore));

function getInfoCodice($codice){  
    $query = "SELECT id, scadenza, valore FROM codici WHERE codici.sigla = ?";
    
    if($stmt = $GLOBALS['connessione']->prepare($query))
    {
        $stmt->bind_param('s', $codice);
        
        if($stmt->execute()){
            $stmt->store_result();
            $stmt->bind_result($id_code, $scadenza,$valore);
            $stmt->fetch();
            $output = array('id' => $id_code, 'expire' => $scadenza, 'valore' => $valore);
            return $output;
        }
    }
}

function getReward($username, $password, $codice, $id_codice){
    $query = "SELECT id FROM codici_riscattati WHERE codici_riscattati.id_codice_fk = ? AND codici_riscattati.id_utente_fk = (SELECT id FROM utenti WHERE username = ? AND password = ?)";

    if($stmt = $GLOBALS['connessione']->prepare($query))
    {
        $stmt->bind_param('sss', $id_codice, $username, $password);
        
        if($stmt->execute()){
            $stmt->store_result();
            $stmt->bind_result($trovato);
            $stmt->fetch();
            
            return (!isset($trovato)) ? true : false;
        }
    }
}

function getReward2($id_codice){
    $query = "SELECT id FROM codici_riscattati WHERE codici_riscattati.id_codice_fk = ?";

    if($stmt = $GLOBALS['connessione']->prepare($query))
    {
        $stmt->bind_param('s', $id_codice);
        
        if($stmt->execute()){
            $stmt->store_result();
            $stmt->bind_result($trovato);
            $stmt->fetch();
            
            return (!isset($trovato)) ? true : false;
        }
    }
}

function setRewardTaken($username, $password, $id_codice, $valore ){
    $query = "INSERT INTO codici_riscattati (data_riscatto, id_codice_fk, id_utente_fk) VALUES (now(), ?, (SELECT id FROM utenti WHERE username = ? AND password = ?))";
    if($stmt = $GLOBALS['connessione']->prepare($query))
    {
        $stmt->bind_param('sss', $id_codice, $username, $password);
        
        if($stmt->execute()){

            $GLOBALS['user']->addMoney($username, $password, (int)$valore);
            return $valore;
        }
    }
}
?>