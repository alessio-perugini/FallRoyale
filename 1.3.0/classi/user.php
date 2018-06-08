<?php

class user
{
    public $salt = '';
    public $id_user;
    public $money;
    public $score;
    public $season;
    public $selected_player;
    public $selected_bus;
    public $nazione;
    public $referral;

    public function login($username, $password)
    {
        $query = "SELECT user.id, user.soldi, user.salt, user.referral AS referral, seas.id_s AS seasonc,(SELECT items.codice AS player_c FROM item_acquistati, items, selected_items WHERE item_acquistati.id_item_fk = items.id AND selected_items.player = item_acquistati.id AND selected_items.id_utente_fk = user.id) AS player, (SELECT items.codice AS bus_c FROM item_acquistati, items, selected_items WHERE item_acquistati.id_item_fk = items.id AND selected_items.bus = item_acquistati.id AND selected_items.id_utente_fk = user.id) AS bus, Ifnull((SELECT punteggi.valore FROM punteggi WHERE punteggi.id_utentefk = user.id AND punteggi.id_seasonfk = seas.id_s), 0) AS punteggio, user.nazione_fk FROM (SELECT utenti.id, utenti.soldi, utenti.salt, utenti.nazione_fk, utenti.referral FROM utenti WHERE username = ? AND password = ?) AS user, (SELECT Max(season.id) AS id_s FROM season) AS seas;";
        
        if ($stmt = $GLOBALS['connessione']->prepare($query)) {
            $stmt->bind_param('ss', $username, $password);
            if ($stmt->execute()) {
                $stmt->store_result();
                $stmt->bind_result($this->id_user, $this->money, $this->salt, $this->referral, $this->season, $this->selected_player, $this->selected_bus, $this->score, $this->nazione);
                $stmt->fetch();
                $this->salt = self::getSaltFromId_Db($this->id_user);
                if (isset($this->id_user)) {
                    self::set_last_login_Db($this->id_user);
                    if ($this->salt == '') {
                        self::updateSalt_db($this->id_user);
                        $this->salt = self::getSaltFromId_Db($this->id_user);
                    }
                    /*if(self::checkBoughtSkins($this->id_user) == 0){
                        self::createDefaultItem($username, $password);
                    }*/
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    //da modificare il return
    public function getCurrentSeason_Db()
    {
        $risultato = $GLOBALS['connessione']->query("SELECT MAX(id) AS season FROM season");

        return (int)$risultato->fetch_all(MYSQLI_ASSOC)[0]['season'];
    }

    public function set_last_login_Db($id)
    {
        $query = "UPDATE utenti SET last_login=NOW() WHERE utenti.id=?";

        $GLOBALS['utils']->query($query, array('id' => (int)$id), false);
    }

    public function getSalt_Db($username, $password)
    {
        $query = "SELECT salt FROM utenti WHERE username=? and password=?";

        $output = $GLOBALS['utils']->query($query, array('username' => $username, 'password' => $password));
        return (count($output) > 0) ? $output[0]['salt'] : '';
    }

    public function getSaltFromId_Db($id)
    {
        $query = "SELECT salt FROM utenti WHERE id = ?";

        $output = $GLOBALS['utils']->query($query, array('id' => $id));
        return (count($output)>0) ? $output[0]['salt'] : '';
    }

    public function updateNazione($username, $password, $nazione)
    {
        $query= "UPDATE utenti SET nazione_fk=? WHERE username= ? and password= ?;";
        $GLOBALS['utils']->query($query, array('nazione_fk' => $nazione, 'username' => $username, 'password' => $password), false);
        return true;
    }
    
    public function updateSalt_db($id)
    {
        $query = "UPDATE utenti SET salt=? WHERE id=?;";
        $salt = self::generateSalt();
        $GLOBALS['utils']->query($query, array('salt' => $salt, 'id' => $id), false);
    }
    
    public function generateSalt()
    {
        require_once('crypt/PasswordStorage.php');
        return PasswordStorage::create_salt();
    }

    public function checkBoughtSkins($id_user)
    {
        $risultato = $GLOBALS['connessione']->query("SELECT id FROM item_acquistati WHERE id_utente_fk = $id_user GROUP BY id");

        return (int)$risultato->fetch_all(MYSQLI_ASSOC);
    }

    public function updateMoney($username, $password, $money)
    {
        $query = "UPDATE utenti SET soldi=? WHERE username = ? AND password = ?;";

        $GLOBALS['utils']->query($query, array('money' => $money, 'username' => $username, 'password' => $password), false);
    }

    public function addMoney($username, $password, $money)
    {
        $query = "UPDATE utenti SET soldi = soldi + ? WHERE username = ? AND password = ?;";

        $GLOBALS['utils']->query($query, array('money' => $money, 'username' => $username, 'password' => $password), false);
    }

    public function getSelectedSkin($id)
    {
        $query = "SELECT items.codice FROM utenti, item_acquistati, items WHERE utenti.id = $id AND item_acquistati.id = utenti.selected_skin AND item_acquistati.id_item_fk = items.id";
        $result = $GLOBALS['connessione']->query($query);
        $outp = $result->fetch_all(MYSQLI_ASSOC);
        $this->selected_player = (isset($outp) && count($outp) > 0) ? $outp[0]['codice'] : 'EXPR';
    }
    //migliorare con query unica
    public function setVersioneUser($versione, $username, $password, $dispositivo)
    {
        $query = "SELECT version.id AS id_v FROM version, utenti WHERE utenti.username = ? AND password=? AND version.versione = ? AND version.piattaforma = ?";
        $id_vers = $GLOBALS['utils']->query($query, array('username' => $username, 'password' => $password, 'versione' => $versione, 'dispositivo' => $dispositivo));

        if (count($id_vers)>0) {
            $id_vers = $id_vers[0]['id_v'];
        }

        $query = "UPDATE utenti SET versione = ?, dispositivo = ? WHERE username = ? AND password = ?";
        $GLOBALS['utils']->query($query, array('id_vers' => $id_vers, 'dispositivo' => $dispositivo, 'username' => $username, 'password' => $password), false);
    }
}
