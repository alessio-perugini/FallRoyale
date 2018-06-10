<?php
require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");
//da migliorare codice di merda
$username = $_POST['usern'];
$password = $_POST['password'];
$codice = $_POST['codice'];

$info = getInfoCodice($codice);

$errore = "";
$valore_codice = array('valore' => -1, 'skin' => '', 'errore' => '');

if (isset($info['id'])) {
    if (isset($info['expire'])) {
        if (getReward($username, $password, $codice, $info['id'], $info['expire'])) {
            $valore_codice = setRewardTaken($username, $password, $info['id'], $info['valore'], $info['skin']);
        } else {
            $errore = 'already redeemed';
        }
    } elseif (getReward2($info['id'])) {
        $valore_codice = setRewardTaken($username, $password, $info['id'], $info['valore'], $info['skin']);
    } else {
        $errore = 'already redeemed';
    }
} else {
    $errore = "invalid code";
}

echo json_encode(array('valore' => $valore_codice['valore'], 'skin' => getSkinCode($valore_codice['skin']), 'errore' => (($valore_codice['errore'] != '') ? $valore_codice['errore'] : $errore)));

function getInfoCodice($codice)
{
    $query = "SELECT id, scadenza AS expire, valore, skin_fk as skin FROM codici WHERE codici.sigla = ?";

    $output = $GLOBALS['utils']->query($query, array('codice' => $codice));
    return (count($output) > 0) ? $output[0] : null;
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

function setRewardTaken($username, $password, $id_codice, $valore, $skin)
{
    $query = "INSERT INTO codici_riscattati (data_riscatto, id_codice_fk, id_utente_fk) VALUES (now(), ?, (SELECT id FROM utenti WHERE username = ? AND password = ?))";

    $GLOBALS['utils']->query($query, array('id_codice' => $id_codice, 'username' => $username, 'password' => $password), false);

    $errore = null;
    if (isset($skin)) {
        if (!checkItemExistance($username, $password, $skin)) {
            setItemAcquired($username, $password, $skin);
        } else {
            $errore = "You already have this skin";
        }
    } else {
        $GLOBALS['user']->addMoney($username, $password, (int)$valore);
    }

    return array('valore' => (!isset($valore)) ? -1 : $valore, 'skin' => (!isset($skin)) ? -1 : $skin, 'errore' => $errore);
}

function getSkinCode($id_skin)
{
    $query = "SELECT items.codice as codice FROM items WHERE id = ?";

    $output = $GLOBALS['utils']->query($query, array('id' => $id_skin));

    return (isset($output[0])) ? $output[0]['codice'] : '';
}

function checkItemExistance($username, $password, $codice)
{
    $query = "SELECT id FROM item_acquistati WHERE item_acquistati.id_utente_fk = (SELECT id FROM utenti WHERE username = ? AND password = ?) AND item_acquistati.id_item_fk = ?";
    return (isset($GLOBALS['utils']->query($query, array('username' => $username, 'password' => $password, 'codice' => $codice))[0])) ? true : false;
}

function setItemAcquired($username, $password, $codice)
{
    $query = "INSERT INTO item_acquistati (data_acquisto, id_item_fk, id_utente_fk) VALUES (now(), ?, (SELECT id FROM utenti WHERE username = ? AND password = ?))";
    $GLOBALS['utils']->query($query, array('codice' => $codice, 'username' => $username, 'password' => $password), false);
}
