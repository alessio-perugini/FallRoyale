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

    public function login($username, $password){
        $query = "SELECT user.id, user.soldi, user.salt, seas.id_s AS seasonc,(SELECT items.codice AS player_c FROM item_acquistati, items, selected_items WHERE item_acquistati.id_item_fk = items.id AND selected_items.player = item_acquistati.id AND selected_items.id_utente_fk = user.id) AS player, (SELECT items.codice AS bus_c FROM item_acquistati, items, selected_items WHERE item_acquistati.id_item_fk = items.id AND selected_items.bus = item_acquistati.id AND selected_items.id_utente_fk = user.id) AS bus, Ifnull((SELECT punteggi.valore FROM punteggi WHERE punteggi.id_utentefk = user.id AND punteggi.id_seasonfk = seas.id_s), 0) AS punteggio, user.nazione_fk FROM (SELECT utenti.id, utenti.soldi, utenti.salt, utenti.nazione_fk FROM utenti WHERE username = ? AND password = ?) AS user, (SELECT Max(season.id) AS id_s FROM season) AS seas;";
        
        if($stmt = $GLOBALS['connessione']->prepare($query))
        {
            $stmt->bind_param('ss', $username, $password);
            if($stmt->execute())
            {
                $stmt->store_result();
                $stmt->bind_result($this->id_user, $this->money, $this->salt, $this->season, $this->selected_player, $this->selected_bus, $this->score, $this->nazione);
                $stmt->fetch();

                if(isset($this->id_user)){
                    self::set_last_login_Db($this->id_user);
                    if($this->salt == ''){
                        self::updateSalt_db($this->id_user);
                        $this->salt = self::getSaltFromId_Db($this->id_user);
                    }
                    /*if(self::checkBoughtSkins($this->id_user) == 0){
                        self::createDefaultItem($username, $password);
                    }*/
                    return true;
                }else{
                    return false;
                }

            }		
        }
        return false;
    }

    //da modificare il return
    function getCurrentSeason_Db(){
        $risultato = $GLOBALS['connessione']->query("SELECT MAX(id) AS season FROM season");

        return (int)$risultato->fetch_all(MYSQLI_ASSOC)[0]['season'];
    }

    function set_last_login_Db($id){
        $query = "UPDATE utenti SET last_login=now() WHERE utenti.id=?";
    
        if($stmt = $GLOBALS['connessione']->prepare($query))
        {
            $stmt->bind_param('i', $id);
            $stmt->execute();
        }
    }

    //da controllare il return
    public function getSalt_Db($username, $password){
        if($stmt = $GLOBALS['connessione']->prepare("SELECT salt FROM utenti WHERE username=? and password=?"))
        {
            $stmt->bind_param('ss', $username, $password);
            if($stmt->execute())
            {
                $stmt->store_result();
                $stmt->bind_result($saltdb);
                $stmt->fetch();
    
                return $saltdb;
            }		
        }
    }

    function getSaltFromId_Db($id){
        if($stmt = $GLOBALS['connessione']->prepare("SELECT salt FROM utenti WHERE id = ?"))
        {
            $stmt->bind_param('i', $id);
            if($stmt->execute())
            {
                $stmt->store_result();
                $stmt->bind_result($saltdb);
                $stmt->fetch();
    
                return (isset($saltdb)) ? $saltdb : false;
            }		
        }
    }

    function updateNazione($username, $password, $nazione){
        $queryInsert = "UPDATE utenti SET nazione_fk=? WHERE username= ? and password= ?;";
        if($stmt = $GLOBALS['connessione']->prepare($queryInsert))
        {
            $stmt->bind_param('sss', $nazione, $username, $password);
            $stmt->execute();
            return true;
        }
        return false;
    }
    
    function updateSalt_db($id){
        $queryInsert = "UPDATE utenti SET salt=? WHERE id=?;";
        $salt = self::generateSalt();
        if($stmt = $GLOBALS['connessione']->prepare($queryInsert))
        {
            $stmt->bind_param('si', $salt, $id);
            $stmt->execute();
        }
    }
    
    public function generateSalt(){
        require_once('crypt/PasswordStorage.php');
        return PasswordStorage::create_salt();
    }

    /*
    public function createDefaultItem($username, $password){
        if($stmt = $GLOBALS['connessione']->prepare("INSERT INTO item_acquistati (data_acquisto, id_item_fk, id_utente_fk) VALUES (now(), (SELECT items.id FROM items WHERE codice = 'EXPR'), (SELECT utenti.id FROM utenti WHERE username = ? AND password = ?)), (now(), (SELECT items.id FROM items WHERE codice = 'AIRB'), (SELECT utenti.id FROM utenti WHERE username = ? AND password = ?))"))
        {
            $stmt->bind_param('ssss', $username, $password, $username, $password);
            $stmt->execute();
        }
    }*/

    function checkBoughtSkins($id_user){
        $risultato = $GLOBALS['connessione']->query("SELECT id FROM item_acquistati WHERE id_utente_fk = $id_user GROUP BY id");

        return (int)$risultato->fetch_all(MYSQLI_ASSOC);
    }

    public function updateMoney($username, $password, $money){
        $queryInsert = "UPDATE utenti SET soldi=? WHERE username = ? AND password = ?;";
        if($stmt = $GLOBALS['connessione']->prepare($queryInsert))
        {
            $stmt->bind_param('iss', $money, $username, $password);
            $stmt->execute();
        }
    }

    public function addMoney($username, $password, $money){
        $queryInsert = "UPDATE utenti SET soldi = soldi + ? WHERE username = ? AND password = ?;";
        if($stmt = $GLOBALS['connessione']->prepare($queryInsert))
        {
            $stmt->bind_param('iss', $money, $username, $password);
            $stmt->execute();
        }
    }

    public function getSelectedSkin($id){
        $query = "SELECT items.codice FROM utenti, item_acquistati, items WHERE utenti.id = $id AND item_acquistati.id = utenti.selected_skin AND item_acquistati.id_item_fk = items.id";
        $result = $GLOBALS['connessione']->query($query);
        $outp = $result->fetch_all(MYSQLI_ASSOC);
        $this->selected_player = (isset($outp) && count($outp) > 0) ? $outp[0]['codice'] : 'EXPR';
    }

    public function setVersioneUser($versione, $username, $password, $dispositivo){
        $query = "SELECT version.id FROM version, utenti WHERE utenti.username = ? AND password=? AND version.versione = ? AND version.piattaforma = ?";
        if($stmt = $GLOBALS['connessione']->prepare($query))
        {
            $stmt->bind_param('ssss', $username, $password, $versione, $dispositivo);
            if($stmt->execute()){
                $stmt->store_result();
                $stmt->bind_result($id_vers);
                $stmt->fetch();
                if($stmt = $GLOBALS['connessione']->prepare("UPDATE utenti SET versione = ?, dispositivo = ? WHERE username = ? AND password = ?"))
                {
                    $stmt->bind_param('isss', $id_vers, $dispositivo, $username, $password);
                    $stmt->execute();
                }

            }
        }
    }

}

?>