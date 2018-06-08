<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$username = $_POST['usern'];
$password = $_POST['password'];
$nazione = $_POST['nazione'];

echo json_encode(array('changed' => $user->updateNazione($username, $password, $nazione)));
