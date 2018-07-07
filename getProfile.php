<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$nome_profilo = $_POST['profilo'];

$infogen = getSoldiAndCurrentSkin($nome_profilo);

//TODO CREARE UNA QUERY UNICA
if (isset($infogen['id_u'])) {
    $punteggi = getBestScore($nome_profilo);
    $skins = getSkinBought($infogen['id_u']);
    echo json_encode(array('info' => array( 'soldi' => $infogen['soldi'], 'player_s' => $infogen['player_s'], 'bus_s' => $infogen['bus_s'], 'nazione' => $infogen['nazione']), 'score' => $punteggi, 'skins' => $skins)/*)*/);
}

function getSoldiAndCurrentSkin($username)
{
    $query = "SELECT user.id AS id_u, user.soldi,(SELECT items.codice AS player_c FROM item_acquistati, items, selected_items WHERE item_acquistati.id_item_fk = items.id AND selected_items.player = item_acquistati.id AND selected_items.id_utente_fk = user.id) AS player_s, (SELECT items.codice AS bus_c FROM item_acquistati, items, selected_items WHERE item_acquistati.id_item_fk = items.id AND selected_items.bus = item_acquistati.id AND selected_items.id_utente_fk = user.id) AS bus_s, user.nazione_fk AS nazione FROM (SELECT utenti.id, utenti.soldi, utenti.nazione_fk FROM utenti WHERE utenti.username = ?) as user;";
    
    $outp = $GLOBALS['utils']->query($query, array('username' => $username));
    return (count($outp) > 0) ? $outp[0] : null;
}

function getBestScore($id_utente)
{
    $query = "CALL ClassificheTutteSeason(?,?,?,?);";

    $outp = $GLOBALS['utils']->query($query, array('inizio' => 1, 'fine' => -1, 'valore' => 0, 'utente' => $id_utente), true, true);

    return (count($outp) > 0) ? $outp : null;
}

function getSkinBought($id_utente)
{
    $query = "SELECT items.codice FROM item_acquistati, items WHERE item_acquistati.id_utente_fk = ? AND item_acquistati.id_item_fk = items.id";

    $outp = $GLOBALS['utils']->query($query, array('id_utente' => $id_utente));
    return (count($outp) > 0) ? $outp : null;
}
