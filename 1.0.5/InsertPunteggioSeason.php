<?php require_once('../includes/config.php');

$username=$_POST['usern'];
$password=$_POST['password'];
$score=$_POST['punteggio'];
$versione=$_POST['versione'];

$esiste = checkScoreExistance($username,$password);
if(isset($esiste)) updateScore($score,$versione,$username,$password); else addSCore($score,$versione,$username,$password);

function checkScoreExistance($username,$password){
	$queryCerca = "SELECT punteggi.id FROM season, utenti, punteggi WHERE punteggi.id_utenteFK=utenti.id and punteggi.id_seasonFK=season.id and utenti.username=? and password=? and id_seasonFK=(SELECT MAX(id) FROM season)";
	if($stmt = $GLOBALS['connessione']->prepare($queryCerca))
	{
		$stmt->bind_param('ss',$username,$password);

		if($stmt->execute()){
			$stmt->store_result();
			$stmt->bind_result($esiste);
			$stmt->fetch();
			
			return $esiste;
		}
	}
}

function updateScore($score,$versione,$username,$password){
	$queryInsert = "UPDATE punteggi SET valore=?, versione=?, data=now() WHERE id_utenteFK=(SELECT id FROM utenti WHERE username= ? and password= ?);";
	if($stmt = $GLOBALS['connessione']->prepare($queryInsert))
	{
		$stmt->bind_param('isss', $score,$versione,$username,$password);
		$stmt->execute();
	}
}

function addSCore($score,$versione,$username,$password){
	$queryInsert = "INSERT INTO punteggi (valore, versione, id_utenteFK,id_seasonFK,data) VALUES (?, ?, (SELECT id FROM utenti WHERE username= ? and password= ?),(SELECT MAX(id) FROM season),now());";
			
	if($stmt = $GLOBALS['connessione']->prepare($queryInsert))
	{
		$stmt->bind_param('isss', $score,$versione,$username,$password);
		$stmt->execute();
	}
}

?>