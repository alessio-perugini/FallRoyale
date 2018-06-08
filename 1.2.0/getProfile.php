<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$nome_profilo = $_POST['profilo'];
$infogen= getSoldiAndCurrentSkin($nome_profilo);

if(isset($infogen['id'])){
    $punteggi = getBestScore($infogen['id']);
    $skins = getSkinBought($infogen['id']);
echo json_encode(/*array('profilo' =>*/ array('info' => array( 'soldi' => $infogen['soldi'], 'player_s' => $infogen['player_s'], 'bus_s' => $infogen['bus_s'], 'nazione' => $infogen['nazione']), 'score' => $punteggi, 'skins' => $skins)/*)*/);
}

function getSoldiAndCurrentSkin($username){
    $query = "SELECT user.id AS id_u, user.soldi,(SELECT items.codice AS player_c FROM item_acquistati, items, selected_items WHERE item_acquistati.id_item_fk = items.id AND selected_items.player = item_acquistati.id AND selected_items.id_utente_fk = user.id) AS player, (SELECT items.codice AS bus_c FROM item_acquistati, items, selected_items WHERE item_acquistati.id_item_fk = items.id AND selected_items.bus = item_acquistati.id AND selected_items.id_utente_fk = user.id) AS bus, user.nazione_fk FROM (SELECT utenti.id, utenti.soldi, utenti.nazione_fk FROM utenti WHERE utenti.username = ?) as user;";

    if($stmt = $GLOBALS['connessione']->prepare($query))
    {
        $stmt->bind_param('s', $username);
        if($stmt->execute()){
            $stmt->store_result();
            $stmt->bind_result($id, $soldi, $player, $bus, $nazione);
            $stmt->fetch();
            $infogen = array('id' => $id, 'soldi' => $soldi, 'nazione' => $nazione , 'player_s' => $player, 'bus_s' => $bus);
            return $infogen;
        }
    }
}

function getBestScore($id_utente){
    $query = "SELECT punteggi.valore as score, punteggi.id_seasonfk as season FROM punteggi WHERE punteggi.id_utentefk = $id_utente";
    $risultato = $GLOBALS['connessione']->query($query);
    //return Array('punteggi' => $risultato->fetch_all(MYSQLI_ASSOC));
    return $risultato->fetch_all(MYSQLI_ASSOC);
}

function getSkinBought($id_utente){
    $query = "SELECT items.codice FROM item_acquistati, items WHERE item_acquistati.id_utente_fk = $id_utente AND item_acquistati.id_item_fk = items.id";
    $risultato = $GLOBALS['connessione']->query($query);
    //return Array('skins' => $risultato->fetch_all(MYSQLI_ASSOC));
    return $risultato->fetch_all(MYSQLI_ASSOC);
}

?>