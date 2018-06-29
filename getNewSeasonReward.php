<?php
require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$username = $_POST['usern'];
$pw = $_POST['password'];
$season = $_POST['season'];

if ($user->login($username, $pw)) {
    $outp = reward($user->id_user, $season);
    echo json_encode($outp);
}

function reward($id_utente, $season)
{
    $query = "SELECT IF(premi.valuta IS NULL, -1, premi.valuta) as valore, IF(premi.tipo_valuta IS NULL, -1, premi.tipo_valuta) as tipo_valuta , IF(codice IS NULL, '',codice) as codice FROM rewards_season, punteggi, premi LEFT JOIN items ON premi.id_item_fk = items.id WHERE rewards_season.id_utente_fk = ? AND premi.id = rewards_season.id_premi_fk AND punteggi.id = rewards_season.id_punteggi_fk AND punteggi.id_seasonFK = ?";

    return $GLOBALS['utils']->query($query, array('id' => $id_utente, 'season' => $season));
}
