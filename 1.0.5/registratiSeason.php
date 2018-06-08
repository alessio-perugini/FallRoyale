<?php require_once('../includes/config.php');

$username= trim($_POST['usern']);
$password=$_POST['password'];
$dispositivo=$_POST['dispositivo'];

if(strlen($username) >= 3 && strlen($password) >=6)
{
	if(!checkExistingUsername($username))
		createUser($username, $password, $dispositivo);
	else
		echo "username already exist";
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

function createUser($username, $password, $dispositivo){
	if($stmt = $GLOBALS['connessione']->prepare("INSERT INTO utenti (username, password, dispositivo) VALUES (?,?,?)"))
	{
		$stmt->bind_param('sss', $username, $password, $dispositivo);
		echo ($stmt->execute()) ? "successful registration": "Error try again";
	} else {
		echo "Error try again";
	}
}
?>