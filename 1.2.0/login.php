<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$username = $_POST['usern'];
$password = $_POST['password'];
$versione = $_POST['versione'];
$dispositivo = $_POST['dispositivo'];

if($user->login($username, $password)){
	$user->setVersioneUser($versione, $username, $password, $dispositivo);
	$output = array('login' => true, 'season' =>  $user->season, 'soldi' =>  $user->money, 'score' => $user->score , 'salt' => $user->salt, 'player_s' => $user->selected_player, "bus_s" => $user->selected_bus, "nazione" => $user->nazione);
	echo json_encode($output);
}else{
	$output = array('login' => false, 'season' =>  -1, 'soldi' =>  -1, 'score' => -1 , 'salt' => "", 'player_s' => "", 'bus_s' => "");
	echo json_encode($output);
}
?>