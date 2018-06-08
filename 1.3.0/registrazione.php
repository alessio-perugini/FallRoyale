<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$username = trim($_POST['usern']);
$password = $_POST['password'];
$dispositivo = $_POST['dispositivo'];
$nazionalita = $_POST['nazione'];
$versione = $_POST['versione'];
$referral_code = (isset($_POST['ref_code'])) ? $_POST['ref_code'] : '';

if (strlen($username) >= 3 && strlen($username) <=20 && strlen($password) >=6 && strlen($password) <=255) {
    if (!checkExistingUsername($username)) {
        $out = createUser($username, $password, $dispositivo, $nazionalita, $versione, $referral_code);
        echo json_encode(array('salt' => $out['salt'], 'referral' => $out['referral'], 'season' => $user->getCurrentSeason_Db(),  'errore' => 'Registration success'));
    } else {
        echo json_encode(array('salt' => '-1', 'season' => -1, 'errore' => "username already exist"));
    }
}

function checkExistingUsername($username)
{
    $query = "SELECT username FROM utenti WHERE username = ?";

    return (count($GLOBALS['utils']->query($query, array('username' => $username))) > 0) ? true : false;
}

//response in json aggiungere return del referral
function createUser($username, $password, $dispositivo, $nazione, $versione, $referral_code)
{
    $salt = $GLOBALS['user']->generateSalt();
    $codice_ref_personale = crea_referral();
    if ($stmt = $GLOBALS['connessione']->prepare("INSERT INTO utenti (username, password, dispositivo, salt, nazione_fk, referral) VALUES (?, ?, ?, ?, ?, ?)")) {
        $stmt->bind_param('ssssss', $username, $password, $dispositivo, $salt, $nazione, $codice_ref_personale);
        if ($stmt->execute()) {
            if ($referral_code != '') {
                redeem_referral($username, $referral_code);
            }
            createScore($username, $password, $versione, $dispositivo);
            createDefaultItem($username, $password);
            setDefaultItems($username, $password);
            $GLOBALS['user']->setVersioneUser($versione, $username, $password, $dispositivo);
            return array('salt' => $salt, 'referral' => $codice_ref_personale);
        } else {
            return '-1';
        }
    } else {
        return '-1';
    }
}

function createDefaultItem($username, $password)
{ //da migliorare fa cacà
    $query = "INSERT INTO item_acquistati (data_acquisto, id_item_fk, id_utente_fk) VALUES (now(), (SELECT items.id FROM items WHERE codice = 'EXPR'), (SELECT utenti.id FROM utenti WHERE username = ? AND password = ?)), (now(), (SELECT items.id FROM items WHERE codice = 'AIRB'), (SELECT utenti.id FROM utenti WHERE username = ? AND password = ?))";
    $GLOBALS['utils']->query($query, array('username' => $username, 'password' => $password,'username2' => $username, 'password2' => $password), false);
}

function setDefaultItems($username, $password)
{
    $query = "INSERT INTO selected_items(selected_items.player, selected_items.bus, selected_items.id_utente_fk) SELECT A.id as player, B.id as bus, user.id as id_utente_fk FROM (SELECT utenti.id FROM utenti WHERE utenti.username = ? AND utenti.password = ?) AS user, item_acquistati A, item_acquistati B WHERE A.id_utente_fk = user.id AND B.id_utente_fk = user.id AND A.id_item_fk = (SELECT items.id FROM items WHERE items.codice = 'EXPR') AND B.id_item_fk = (SELECT items.id FROM items WHERE items.codice = 'AIRB');";

    $GLOBALS['utils']->query($query, array('username' => $username, 'password' => $password), false);
}

function createScore($username, $password, $versione, $dispositivo)
{
    $query = "INSERT INTO punteggi (version_fk, valore, id_utenteFK, id_seasonFK, data) VALUES ( (SELECT version.id FROM version WHERE version.piattaforma = ? AND version.versione = ?) , 0, (SELECT id FROM utenti WHERE username = ? AND password = ?), (SELECT MAX(id) FROM season), now())";
    $GLOBALS['utils']->query($query, array('dispositivo' => $dispositivo, 'versione' => $versione, 'username' => $username, 'password' => $password), false);
}

function crea_referral()
{
    $user = $GLOBALS['username'];
    $user = $user[0] . $user[strlen($user) - 1]; //prendo la prima e l'ultima lettera del nick
    //faccio l'hash usando come salt il nickname e come pw i 2 caratteri del nome
    $hash = hash_pbkdf2("sha256", $user, $GLOBALS['username'], 1000, 16);
    return $hash;
}

function redeem_referral($username, $codice_ref)
{
    //metere controllo nella query se già il codice è stato riscattata da chi si vuole iscrivere
    $query = 'INSERT INTO inviti (data, mittente, destinatario) SELECT NOW() as data, id as mittente, receiver.destinatario as destinatario FROM utenti as sender, (SELECT utenti.id as destinatario FROM utenti WHERE utenti.username = ?) as receiver WHERE referral = ?';
    $GLOBALS['utils']->query($query, array('username' => $username, 'codice_ref' => $codice_ref), false);
}
