<?php
require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$username = $_POST['usern'];
$password = $_POST['password'];
$codice = $_POST['codice'];

$info = getInfoCodice($codice);

$errore = "";
$valore_codice = -1;

if (isset($info['id'])) {
    if (isset($info['expire'])) {
        if (getReward($username, $password, $codice, $info['id'], $info['expire'])) {
            $valore_codice = setRewardTaken($username, $password, $info['id'], $info['valore']);
        } else {
            $errore = 'already redeemed';
        }
    } elseif (getReward2($info['id'])) {
        $valore_codice = setRewardTaken($username, $password, $info['id'], $info['valore']);
    } else {
        $errore = 'already redeemed';
    }
} else {
    $errore = "invalid code";
}

echo json_encode(array('valore' => $valore_codice, 'errore' => $errore));

function getInfoCodice($codice)
{
    $query = "SELECT id, scadenza AS expire, valore FROM codici WHERE codici.sigla = ?";

    $output = $GLOBALS['utils']->query($query, array('codice' => $codice));
    return (count($output) > 0) ? $output[0] : $output;
}

function getReward($username, $password, $codice, $id_codice)
{
    $query = "SELECT id FROM codici_riscattati WHERE codici_riscattati.id_codice_fk = ? AND codici_riscattati.id_utente_fk = (SELECT id FROM utenti WHERE username = ? AND password = ?)";

    return (count($GLOBALS['utils']->query($query, array('id_codice' => $id_codice, 'username' => $username, 'password' => $password))) > 0) ? false : true;
}

function getReward2($id_codice)
{
    $query = "SELECT id FROM codici_riscattati WHERE codici_riscattati.id_codice_fk = ?";

    return (count($GLOBALS['utils']->query($query, array('id_codice' => $id_codice))) > 0) ? false : true;
}

function setRewardTaken($username, $password, $id_codice, $valore)
{
    $query = "INSERT INTO codici_riscattati (data_riscatto, id_codice_fk, id_utente_fk) VALUES (now(), ?, (SELECT id FROM utenti WHERE username = ? AND password = ?))";

    $GLOBALS['utils']->query($query, array('id_codice' => $id_codice, 'username' => $username, 'password' => $password), false);
    $GLOBALS['user']->addMoney($username, $password, (int)$valore);
    return $valore;
}
