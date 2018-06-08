<?php require_once('../includes/config.php');

$username = $_POST['usern'];
$password = $_POST['password'];

if(login($username, $password))
	getCurrentSeason($username, $password);
else
	echo "error username or password";

function login($username, $password){
	if($stmt = $GLOBALS['connessione']->prepare("SELECT id FROM utenti WHERE username=? and password=?"))
	{
		$stmt->bind_param('ss', $username, $password);
		if($stmt->execute())
		{
			$stmt->store_result();
			$stmt->bind_result($check_username);
			$stmt->fetch();
	
			return (isset($check_username)) ? true : false;
		}		
	}
}

function getCurrentSeason($username, $password){
	$sql =("SELECT MAX(id) AS sea FROM season");
	$result = mysqli_query($GLOBALS['connessione'], $sql);
	
	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			echo "LOGGED|".$row["sea"];
		}
		set_last_login($username, $password);
	}
}

function set_last_login($username, $pw){
	$query = "UPDATE utenti SET last_login=now() WHERE username=? AND password=?";

	if($stmt = $GLOBALS['connessione']->prepare($query))
	{
		$stmt->bind_param('ss', $username, $pw);
		$stmt->execute();
	}
}

?>