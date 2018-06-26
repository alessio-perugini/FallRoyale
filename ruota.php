<?php
require_once("includes/config.php");
//header("Content-Type: application/json; charset=UTF-8");
$username = $_POST['usern'];
$password = $_POST['password'];
$id_ruota = (int)$_POST['ruota'];

//Costanti
$limit_daily_spins = 200;

if ($user->login($username, $password) && !Check_Daily_Spin_Limit($user->id_user, $limit_daily_spins)) {
    $ruota = getRuota($id_ruota);
    $premio_estratto = estrazionePremio($ruota, $user->id_user);
    setLogRuota($premio_estratto['id_spicchio'], $user->id_user);
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
    $query = "SELECT spicchi_ruote.id as id_spicchio, premi.id as id_premio, premi.valuta, premi.tipo_valuta, items.id as id_item, items.codice FROM spicchi_ruote, premi LEFT JOIN items ON premi.id_item_fk = items.id WHERE spicchi_ruote.id_ruota_fk = ? AND premi.id = spicchi_ruote.id_premio_fk ";

    $outp = $GLOBALS['utils']->query($query, array('ruota' => $ruota));

    return $outp;
}

//controllo se ha superato il limite di estrazione daily o week
function checkLimitReward($ruota, $id_utente)
{
    $query = "SELECT limit_day.c as daily, limit_week.c as weekly 
    FROM (SELECT COUNT(*) as c FROM log_ruota WHERE data_spin > DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY) AND id_spicchio_fk = ?) as limit_day, (SELECT COUNT(*) as c FROM log_ruota WHERE data_spin > DATE_SUB(CURRENT_DATE, INTERVAL 1 WEEK) AND id_spicchio_fk = ?) as limit_week, ((SELECT IFNULL(day_limit, 0) day_limit, IFNULL(week_limit, 0) week_limit FROM limiti_estrazioni WHERE spicchio_ruota_fk = ?) UNION (SELECT 0 day_limit, 0 week_limit) LIMIT 1) AS general_limits
    WHERE limit_week.c < IF(general_limits.week_limit = 0, limit_week.c + 1, general_limits.week_limit) AND limit_day.c < IF(general_limits.day_limit = 0, limit_day.c + 1, general_limits.day_limit)";
    
    $limits = $GLOBALS['utils']->query($query, array('id_spicchio' => $ruota['id_spicchio'], 'id_spicchio2' => $ruota['id_spicchio'], 'id_spicchio3' => $ruota['id_spicchio']));
    return (count($limits) > 0) ? false : true; //se non ritorna item ha superato il limite
}

//estrazione premio + controllo di vittorie massime
function estrazionePremio($ruota, $id_utente)
{
    $estrazione = rand(0, count($ruota)-1);
    //controllo vittorie max
    if (!checkLimitReward($ruota[$estrazione], $id_utente)) {
        //estrai quello che hai
        return $ruota[$estrazione];
    } else {
        //vai al next
        do {
            $estrazione = ($estrazione + 1 == count($ruota) ? 0 : $estrazione + 1);
        } while (checkLimitReward($ruota[$estrazione], $id_utente));
        return $ruota[$estrazione];
    }
}

//inserimento log
function setLogRuota($id_spicchio, $id_utente)
{
    $query = "INSERT INTO log_ruota (id_spicchio_fk, id_utente_fk) VALUES(?, ?)";

    $GLOBALS['utils']->query($query, array('id_spicchio' => $id_spicchio, 'utente' => $id_utente), false);
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
