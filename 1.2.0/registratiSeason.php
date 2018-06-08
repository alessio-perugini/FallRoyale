<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$username= trim($_POST['usern']);
$password=$_POST['password'];
$dispositivo=$_POST['dispositivo'];
$nazionalita = $_POST['nazione'];
$versione=$_POST['versione'];

if(strlen($username) >= 3 && strlen($username) <=20 && strlen($password) >=6 && strlen($password) <=255)
{
	if(!checkExistingUsername($username))
		echo json_encode(Array('salt' => createUser($username, $password, $dispositivo, $nazionalita, $versione), 'season' => $user->getCurrentSeason_Db(),  'errore' => 'Registration success'));
	else
		echo json_encode(Array('salt' => '-1', 'season' => -1, 'errore' => "username already exist"));
}

function checkExistingUsername($username){
	if($stmt = $GLOBALS['connessione']->prepare("SELECT username FROM utenti WHERE username = ?"))
	{
		$stmt->bind_param('s', $username);
		
		if($stmt->execute()){
			$stmt->store_result();
			$stmt->bind_result($check_username);
			$stmt->fetch();

			return (isset($check_username)) ? true : false;
		}
	}
}

//response in json
function createUser($username, $password, $dispositivo, $nazione, $versione){
	$salt = $GLOBALS['user']->generateSalt();

	if($stmt = $GLOBALS['connessione']->prepare("INSERT INTO utenti (username, password, dispositivo, salt, nazione_fk) VALUES (?, ?, ?, ?, ?)"))
	{
		$stmt->bind_param('sssss', $username, $password, $dispositivo, $salt, $nazione);
		if($stmt->execute()){
			createScore($username, $password, $versione, $dispositivo);
			createDefaultItem($username, $password);
			setDefaultItems($username, $password);
			$GLOBALS['user']->setVersioneUser($versione, $username, $password, $dispositivo);
			return $salt;
		}else{
			return '-1';
		}
	} else {
		return '-1';
	}
}

function createDefaultItem($username, $password){ //da migliorare fa cacà
	if($stmt = $GLOBALS['connessione']->prepare("INSERT INTO item_acquistati (data_acquisto, id_item_fk, id_utente_fk) VALUES (now(), (SELECT items.id FROM items WHERE codice = 'EXPR'), (SELECT utenti.id FROM utenti WHERE username = ? AND password = ?)), (now(), (SELECT items.id FROM items WHERE codice = 'AIRB'), (SELECT utenti.id FROM utenti WHERE username = ? AND password = ?))"))
	{
		$stmt->bind_param('ssss', $username, $password, $username, $password);
		$stmt->execute();
	}
}

function setDefaultItems($username, $password){
	$query = "INSERT INTO selected_items(selected_items.player, selected_items.bus, selected_items.id_utente_fk) SELECT A.id as player, B.id as bus, user.id as id_utente_fk FROM (SELECT utenti.id FROM utenti WHERE utenti.username = ? AND utenti.password = ?) AS user, item_acquistati A, item_acquistati B WHERE A.id_utente_fk = user.id AND B.id_utente_fk = user.id AND A.id_item_fk = (SELECT items.id FROM items WHERE items.codice = 'EXPR') AND B.id_item_fk = (SELECT items.id FROM items WHERE items.codice = 'AIRB');";
	if($stmt = $GLOBALS['connessione']->prepare($query))
	{
		$stmt->bind_param('ss', $username, $password);
		$stmt->execute();
	}
}

function createScore($username, $password, $versione, $dispositivo){
	if($stmt = $GLOBALS['connessione']->prepare("INSERT INTO punteggi (version_fk, valore, id_utenteFK, id_seasonFK, data) VALUES ( (SELECT version.id FROM version WHERE version.piattaforma = ? AND version.versione = ?) , 0, (SELECT id FROM utenti WHERE username = ? AND password = ?), (SELECT MAX(id) FROM season), now())"))
	{
		$stmt->bind_param('ssss', $dispositivo, $versione, $username, $password);
		$stmt->execute();
	}
}
?>