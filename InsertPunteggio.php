<?php require_once('includes/config.php');

$username = $_POST['usern'];
$password = $_POST['password'];
$score = $_POST['punteggio'];
$hash = $_POST['hash'];
$versione = $_POST['versione'];

if (!$user->login($username, $password)) {
    exit(0);
}

//controllare se può metterre lo score dalla versione
if (checkMiniumVersion($versione, $user->id_user)) {
    $esiste = checkScoreExistance($username, $password);
    $inserimento = (isset($esiste)) ? checkGreaterScore($esiste, $score) : true;//mi da l'ok se lo score che riceve è più grande di quello che sta nel db
    //controllo degli hash
    $valido = false;
    if ($inserimento) {
        $valido = checkHash($score, $hash);
    }

    //aggiorna o inserisce
    if ($valido && $inserimento) {
        if (isset($esiste)) {
            updateScore($score, $versione, $username, $password, $esiste);
        } else {
            addSCore($score, $username, $password, $versione);
        } //Con le versioni nuove il punteggio lo crea a tutti e lo mette a 0 quindi non serve più
    }
}
//genera ed invia un nuovo salt
$user->updateSalt_db($user->id_user);
$user->salt = $user->getSaltFromId_Db($user->id_user);
echo json_encode(array('salt' => $user->salt));

function checkMiniumVersion($versione, $id)
{
    $query = "SELECT user.dispositivo FROM(SELECT utenti.dispositivo FROM utenti WHERE utenti.id = ?) AS user, (SELECT version.id, version.piattaforma FROM version WHERE version.versione = ?) AS versione, (SELECT season.id AS id_s, season.versione_min_android, season.versione_min_apple FROM season ORDER BY id_s DESC LIMIT 1) AS seas WHERE versione.piattaforma = user.dispositivo AND IF(user.dispositivo = 'Android', versione.id >= seas.versione_min_android, versione.id >= seas.versione_min_apple)";

    $output = $GLOBALS['utils']->query($query, array('id' => $id, 'versione' => $versione));
    return (count($output) > 0) ? true : false;
}

function checkGreaterScore($id_score, $punteggio)
{
    $query = "SELECT valore FROM punteggi WHERE id = ?";

    $punteggio_esistente = $GLOBALS['utils']->query($query, array('id_score' => $id_score));
    return ($punteggio > ((count($punteggio_esistente) > 0) ? $punteggio_esistente[0]['valore'] : 0)) ? true : false;
}

function checkScoreExistance($username, $password)
{
    $query = "SELECT punteggi.id AS id_p FROM(SELECT season.id as id_s, season.versione_min_android, season.versione_min_apple FROM season ORDER BY id_s DESC LIMIT 1) AS seas, (SELECT utenti.versione, utenti.id, utenti.dispositivo FROM utenti WHERE utenti.username = ? AND utenti.password = ? ) AS user, punteggi WHERE punteggi.id_utentefk = user.id AND punteggi.id_seasonfk = seas.id_s AND id_seasonfk = seas.id_s AND IF(user.dispositivo = 'Android', user.versione >= seas.versione_min_android, user.versione >= seas.versione_min_apple)";
    $check = $GLOBALS['utils']->query($query, array('username' => $username, 'password' => $password));
    return (count($check) > 0) ? $check[0]['id_p'] : null;
}

function updateScore($score, $versione, $username, $password, $esiste)
{
    $query = "UPDATE punteggi SET valore = ?, version_fk=(SELECT version.id FROM version, utenti WHERE utenti.username = ? AND version.versione = ? AND piattaforma = utenti.dispositivo), data=now() WHERE id_utenteFK=(SELECT id FROM utenti WHERE username= ? and password= ?) AND id = ?;";

    $GLOBALS['utils']->query($query, array('score' => $score, 'username' => $username, 'versione' => $versione, 'username2' => $username, 'password' => $password, 'esiste' => $esiste ), false);
}


function addSCore($score, $username, $password, $versione)
{
    $query = "INSERT INTO punteggi (valore, version_fk, id_utenteFK, id_seasonFK, data) VALUES (?, (SELECT version.id FROM version, utenti WHERE utenti.username = ? AND version.versione = ? AND piattaforma = utenti.dispositivo), (SELECT id FROM utenti WHERE username= ? and password= ?) , (SELECT MAX(id) FROM season), now());";

    $GLOBALS['utils']->query($query, array('score' => $score, 'username2' => $username, 'versione' => $versione, 'username' => $username, 'password' => $password), false);
}


function checkHash($punteggio, $client_hash)
{
    require_once('classi/crypt/PasswordStorage.php');
    $salt = $GLOBALS['user']->getSalt_Db($GLOBALS['username'], $GLOBALS['password']);
    try {
        $client_hash = 'sha1:' . PasswordStorage::PBKDF2_ITERATIONS . ':18:' . $client_hash;
        $server_hash = PasswordStorage::create_hash($punteggio, $salt);
        $srv_punteggio = PasswordStorage::verify_password($punteggio, $server_hash);
        $clnt_puteggio = PasswordStorage::verify_password($punteggio, $client_hash);

        if (($clnt_puteggio && $srv_punteggio)) {
            return true;
        } else {
            if ($punteggio > 0) {
                insertCheater($GLOBALS['username'], $GLOBALS['password'], $salt, $client_hash, $punteggio);
            }
            return false;
        }
    } catch (Exception $e) {
        $username =  $_POST['usern'];
        error_log("Username: $username Caught $e");
        return false;
    }
}

function insertCheater($username, $password, $salt_good, $salt_bad, $punteggio)
{
    $query= "INSERT INTO cheater(utente_fk, data, salt_good, salt_bad, punteggio) VALUES ( (SELECT id FROM utenti WHERE username = ? AND password = ?), now(), ?, ?, ?)";

    $GLOBALS['utils']->query($query, array('username' => $username, 'password' => $password,'salt_good' => $salt_good, 'salt_bad' => $salt_bad, 'punteggio' => $punteggio, ), false);
}
