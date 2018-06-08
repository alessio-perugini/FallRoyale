<?php require_once('includes/config.php');
//header("Content-Type: application/json; charset=UTF-8");

$username = $_POST['usern'];
$password = $_POST['password'];
$player = $_POST['player'];
$bus = $_POST['bus'];

if($user->login($username, $password)){
    if(isset($player) && $player != '') 
        setCurrentPlayerSkin($player); 
    else 
        setCurrentBusSkin($bus);
	echo "ok";
}else{
    echo "NOPE";
}

    
function setCurrentPlayerSkin($codice){
    $id_utente = $GLOBALS['user']->id_user;
    $query = "UPDATE selected_items SET player = (SELECT id FROM item_acquistati WHERE id_utente_fk = $id_utente AND id_item_fk = (SELECT id FROM items WHERE codice = ?)) WHERE selected_items.id_utente_fk = $id_utente";

    if($stmt = $GLOBALS['connessione']->prepare($query))
    {
        $stmt->bind_param('s', $codice);
        $stmt->execute();
    }
}

function setCurrentBusSkin($codice){
    $id_utente = $GLOBALS['user']->id_user;
    $query = "UPDATE selected_items SET bus = (SELECT id FROM item_acquistati WHERE id_utente_fk = $id_utente AND id_item_fk = (SELECT id FROM items WHERE codice = ?)) WHERE selected_items.id_utente_fk = $id_utente";

    if($stmt = $GLOBALS['connessione']->prepare($query))
    {
        $stmt->bind_param('s', $codice);
        $stmt->execute();
    }
}
?>