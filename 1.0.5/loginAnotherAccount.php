<?php require_once('../includes/config.php');

$username=$_POST['usern'];
$password=$_POST['password'];

$money = getMoney($username, $password);
$score = getScore($username, $password);

if(isset($money) && isset($score))
	echo $money."|".$score;

function getMoney($username, $password){
	if($stmt = $GLOBALS['connessione']->prepare("SELECT soldi FROM utenti WHERE username=? and password=?"))
	{
		$stmt->bind_param('ss', $username, $password);
		if($stmt->execute())
		{
			$stmt->store_result();
			$stmt->bind_result($soldi);
			$stmt->fetch();
			return $soldi;
		}
	}
}

function getScore($username, $password){
	if($stmt = $GLOBALS['connessione']->prepare("SELECT valore FROM utenti,punteggi WHERE username=? and password=? and punteggi.id_utenteFK=utenti.id"))
	{
		$stmt->bind_param('ss', $username, $password);
		if($stmt->execute())
		{
			$stmt->store_result();
			$stmt->bind_result($punteggio);
			$stmt->fetch();
			
			return $punteggio;
		}		
	}
}
?>
