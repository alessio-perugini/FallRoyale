<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$username = $_POST['usern'];
$password = $_POST['password'];

if($user->login($username, $password)){
	$output = array('login' => true, 'season' =>  2, 'soldi' =>  $user->money, 'score' => $user->score , 'salt' => $user->salt);
	echo json_encode($output);
	//echo 'LOGGED|' . $user->season . '|' . $user->salt;
}else{
	$output = array('login' => false, 'season' =>  -1, 'soldi' =>  -1, 'score' => -1 , 'salt' => "");
	echo json_encode($output);
}
?>