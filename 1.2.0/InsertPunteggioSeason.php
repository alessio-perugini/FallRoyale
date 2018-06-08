<?php require_once('includes/config.php');

$username = $_POST['usern'];
$password = $_POST['password'];
$score = $_POST['punteggio'];
$hash = $_POST['hash'];
$versione = $_POST['versione'];

if(!$user->login($username, $password))
	exit(0);

$esiste = checkScoreExistance($username,$password);
$inserimento = (isset($esiste)) ? checkGreaterScore($esiste, $score) : true;//mi da l'ok se lo score che riceve è più grande di quello che sta nel db
//controllo degli hash
$valido = false;
if($inserimento)
	$valido = checkHash($score, $hash);

//aggiorna o inserisce
if($valido && $inserimento){
	if(isset($esiste)) updateScore($score, $versione, $username, $password, $esiste);
	else  addSCore($score,$username,$password); //Con le versioni nuove il punteggio lo crea a tutti e lo mette a 0 quindi non serve più
}

$user->updateSalt_db($user->id_user);
$user->salt = $user->getSaltFromId_Db($user->id_user);
echo json_encode(array('salt' => $user->salt));

//fix allo score per risovlere il bug di simone
function checkGreaterScore($id_score, $punteggio){
	$queryCerca = "SELECT valore FROM punteggi WHERE id = ?";
	if($stmt = $GLOBALS['connessione']->prepare($queryCerca))
	{
		$stmt->bind_param('i', $id_score);

		if($stmt->execute()){
			$stmt->store_result();
			$stmt->bind_result($esiste);
			$stmt->fetch();

			return ($punteggio > $esiste) ? true : false;
		}
	}
}

function checkScoreExistance($username, $password){
	$queryCerca = "SELECT punteggi.id FROM season, utenti, punteggi WHERE punteggi.id_utenteFK=utenti.id and punteggi.id_seasonFK=season.id and utenti.username=? and password=? and id_seasonFK=(SELECT MAX(id) FROM season) and banned != 1";
	$queryCerca = "SELECT punteggi.id FROM(SELECT season.id as id_s, season.versione_min_android, season.versione_min_apple FROM season ORDER BY id_s DESC LIMIT 1) AS seas, (SELECT utenti.versione, utenti.id, utenti.dispositivo FROM utenti WHERE utenti.username = ? AND utenti.password =) AS user, punteggi WHERE punteggi.id_utentefk = user.id AND punteggi.id_seasonfk = seas.id_s AND id_seasonfk = seas.id_s AND IF(user.dispositivo = 'Android', user.versione >= seas.versione_min_android, user.versione >= seas.versione_min_apple)";

	if($stmt = $GLOBALS['connessione']->prepare($queryCerca))
	{
		$stmt->bind_param('ss', $username, $password);

		if($stmt->execute()){
			$stmt->store_result();
			$stmt->bind_result($esiste);
			$stmt->fetch();
			return $esiste;
		}
	}
}

function updateScore($score, $versione, $username, $password, $esiste){
	$queryInsert = "UPDATE punteggi SET valore=?, version_fk=(SELECT version.id FROM version, utenti WHERE utenti.username = ? AND version.versione = ? AND piattaforma = utenti.dispositivo), data=now() WHERE id_utenteFK=(SELECT id FROM utenti WHERE username= ? and password= ?) AND id = ?;";
	if($stmt = $GLOBALS['connessione']->prepare($queryInsert))
	{
		$stmt->bind_param('issssi', $score, $username, $versione, $username, $password, $esiste);
		$stmt->execute();
		
	}
}


function addSCore($score,$username,$password){
	$queryInsert = "INSERT INTO punteggi (valore, version_fk, id_utenteFK,id_seasonFK,data) VALUES (?, (SELECT version.id FROM version, utenti WHERE utenti.username = ? AND version.versione = ? AND piattaforma = utenti.dispositivo), (SELECT id FROM utenti WHERE username= ? and password= ?),(SELECT MAX(id) FROM season),now());";
			
	if($stmt = $GLOBALS['connessione']->prepare($queryInsert))
	{
		$stmt->bind_param('isss', $score, $versione, $username, $password);
		$stmt->execute();
	}
}


function checkHash($punteggio, $client_hash){
	require_once('classi/crypt/PasswordStorage.php');
	$salt = $GLOBALS['user']->getSalt_Db($GLOBALS['username'],$GLOBALS['password']);
	try{
		$client_hash = 'sha1:' . PasswordStorage::PBKDF2_ITERATIONS . ':18:' . $client_hash;
		$server_hash = PasswordStorage::create_hash($punteggio, $salt);
		$srv_punteggio = PasswordStorage::verify_password($punteggio, $server_hash);
		$clnt_puteggio = PasswordStorage::verify_password($punteggio, $client_hash);

		if(($clnt_puteggio && $srv_punteggio)){
			return true;
		}else{
			insertCheater($GLOBALS['username'], $GLOBALS['password'], $salt, $client_hash, $punteggio);
			return false;
		}
	}catch(Exception $e){
		$username =  $_POST['usern'];
		error_log("Username: $username Caught $e");
		return false;
	}
	//return ($clnt_puteggio && $srv_punteggio) ? true : false;
}

function insertCheater($username, $password, $salt_good, $salt_bad, $punteggio){
	$queryInsert = "INSERT INTO cheater(utente_fk, data, salt_good, salt_bad, punteggio) VALUES ( (SELECT id FROM utenti WHERE username = ? AND password = ?), now(), ?, ?, ?)";
			
	if($stmt = $GLOBALS['connessione']->prepare($queryInsert))
	{
		$stmt->bind_param('ssssi', $username, $password, $salt_good, $salt_bad, $punteggio);
		$stmt->execute();
	}
}

?>