<?php
require_once("includes/config.php");
$username = $_POST['usern'];
$password = $_POST['password'];
$id_ruota = $_POST['ruota'];

//Costanti
$limit_daily_spins = 2;

if ($user->login($username, $password) && !Check_Daily_Spin_Limit($user->id_user, $limit_daily_spins)) {
    $ruota = getRuota($id_ruota);
    $premio_estratto = estrazionePremio($ruota);
    setLogRuota($id_ruota, $premio_estratto['id_premio'], $user->id_user);
    setReward($premio_estratto, $user->id_user);
    echo json_encode(array('quantita' => $premio_estratto['valuta'], 'tipo_valuta' => $premio_estratto['tipo_valuta'], 'item' => $premio_estratto['codice']));
}

//controllo limite daily
function Check_Daily_Spin_Limit($id_utente, $limit_daily_spins)
{
    $query = "SELECT count(*) as n FROM log_ruota WHERE id_utente_fk = ? AND data_spin > CURRENT_DATE";

    $outp = $GLOBALS['utils']->query($query, array('id_user' => $id_utente));

    return (count($outp) > 0) ? ($outp[0]['n'] >= $limit_daily_spins) ? true : false : false;
}

//lettura ruota
function getRuota($ruota)
{
    $query = "SELECT premi.id as id_premio, premi.valuta, premi.tipo_valuta, items.id as id_item, items.codice FROM spicchi_ruote, premi LEFT JOIN items ON premi.id_item_fk = items.id WHERE spicchi_ruote.id_ruota_fk = ? AND premi.id = spicchi_ruote.id_premio_fk ";

    $outp = $GLOBALS['utils']->query($query, array('ruota' => $ruota));

    return $outp;
}

//estrazione premio + controllo di vittorie massime
function estrazionePremio($ruota)
{
    //controllo vittorie max

    return $ruota[rand(0, count($ruota)-1)];
}

//inserimento log
function setLogRuota($id_ruota, $id_premio, $id_utente)
{
    $query = "INSERT INTO log_ruota (id_ruota_fk, id_premio_vinto_fk, id_utente_fk) VALUES(?, ?, ?)";

    $GLOBALS['utils']->query($query, array('ruota' => $id_ruota, 'premio' => $id_premio, 'utente' => $id_utente), false);
}

//assegnamento premio
function setReward($premio, $id_utente)
{
    if (isset($premio['valuta'])) {
        switch ($premio['tipo_valuta']) {
            case 1: //soldi
                $query = "UPDATE utenti SET soldi = soldi + ? WHERE id = ?";
            break;
        }
        $GLOBALS['utils']->query($query, array('quantita' => $premio['valuta'], 'id_utente' => $id_utente), false);
    }
    if (isset($premio['id_item']) && !checkItemExistance($id_utente, $premio['id_item'])) {
        $query = "INSERT INTO item_acquistati (data_acquisto, id_item_fk, id_utente_fk) VALUES (NOW(), ?, ?)";
        $GLOBALS['utils']->query($query, array('id_item_fk' => $premio['id_item'], 'id_utente' => $id_utente), false);
    }
}

function checkItemExistance($id_utente, $id_item)
{
    $query = "SELECT id FROM item_acquistati WHERE item_acquistati.id_utente_fk = ? AND item_acquistati.id_item_fk = ?";
    return (isset($GLOBALS['utils']->query($query, array('id_utente' => $id_utente, 'id_item' => $id_item))[0])) ? true : false;
}
