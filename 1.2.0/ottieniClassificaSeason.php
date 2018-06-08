<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$result = $connessione->query("SELECT punteggi.valore, utenti.id,utenti.username, utenti.dispositivo, utenti.nazione_fk AS nazione FROM utenti,punteggi WHERE utenti.banned = 0 AND punteggi.id_utenteFK=utenti.id and punteggi.id_seasonFK=(SELECT MAX(id) FROM season) ORDER BY punteggi.valore DESC, punteggi.data ASC LIMIT 99 ;");
$outp = array();

$outp = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(array('punteggi' => $outp));

?>