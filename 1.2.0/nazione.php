<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$result = $connessione->query("SELECT * FROM nazioni");
$outp = array();

$outp = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(array('nazioni' => $outp));
?>