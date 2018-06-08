<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$dispositivo = $_POST['dispositivo'];

echo json_encode(array('version' => forceUpdate($dispositivo)));

function forceUpdate($dispositivo){
	$queryCerca = "SELECT versione FROM version WHERE piattaforma = ?";
	if($stmt = $GLOBALS['connessione']->prepare($queryCerca))
	{
		$stmt->bind_param('s', $dispositivo);

		if($stmt->execute()){
			$stmt->store_result();
			$stmt->bind_result($versione);
			$stmt->fetch();
			
			return $versione;
		}
	}
}

?>