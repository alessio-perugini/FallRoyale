<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$username = $_POST['usern'];
$password = $_POST['password'];
$versione = $_POST['versione'];
$dispositivo = $_POST['dispositivo'];

if ($user->login($username, $password)) {
    $user->setVersioneUser($versione, $username, $password, $dispositivo);
    
    if ($user->referral == null) {
        $user->referral = crea_referral();
        $utils->query("UPDATE utenti SET referral = ?", array("referral" => $user->referral ), false);
    }
        
    $output = array('login' => true, 'season' =>  $user->season, 'soldi' =>  $user->money, 'score' => $user->score , 'referral' => $user->referral, 'salt' => $user->salt, 'player_s' => $user->selected_player, "bus_s" => $user->selected_bus, "nazione" => $user->nazione);
    echo json_encode($output);
} else {
    $output = array('login' => false, 'season' =>  -1, 'soldi' =>  -1, 'score' => -1 , 'salt' => "", 'player_s' => "", 'bus_s' => "");
    echo json_encode($output);
}

function crea_referral()
{
    $user = $GLOBALS['username'];
    $user = $user[0] . $user[strlen($user) - 1]; //prendo la prima e l'ultima lettera del nick
    //faccio l'hash usando come salt il nickname e come pw i 2 caratteri del nome
    $hash = hash_pbkdf2("sha256", $user, $GLOBALS['username'], 1000, 16);
    return $hash;
}
