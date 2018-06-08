<?php
require_once('includes/config.php');
$username = $_POST['usern'];
$password = $_POST['password'];
$money = $_POST['money'];
/*
try{
    
	if($stmt = $connessione->prepare("SELECT id, soldi FROM utenti WHERE username = ? AND password = ? LIMIT 1"))
	{
		$stmt->bind_param('ss', $username, $password);
		$stmt->execute();
		$stmt->store_result();  
		$stmt->bind_result($id, $soldi);
		$stmt->fetch(); 
		$soldi_persi = $soldi - $money;
		echo "soldi: $soldi_persi id: $id money: $soldi";
		if($soldi_persi > 0)
			$connessione->query("INSERT INTO bus_cronologia (id_utente, username, soldipersi ) VALUES ($id, '$username', $soldi_persi)");
	}
}catch(Exception $e){
	
}*/

$user->updateMoney($username, $password, $money);
?>