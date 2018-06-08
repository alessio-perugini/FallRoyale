<?php
class classifica
{
    private $connessione;
    
    public function __construct($connessione)
    {
        $this->connessione = $connessione;
    }

    public function getNationalityLeaderBoard($nazione)
    {
        $query = "SELECT punteggi.valore, utenti.id, utenti.username, utenti.dispositivo, utenti.nazione_fk AS nazione FROM utenti, punteggi WHERE utenti.banned = 0 AND utenti.nazione_fk = ? AND punteggi.id_utenteFK= utenti.id AND punteggi.id_seasonFK=(SELECT MAX(id) FROM season) ORDER BY punteggi.valore DESC, punteggi.data ASC LIMIT 99";
        if ($stmt = $this->connessione->prepare($query)) {
            $stmt->bind_param('s', $nazione);
            
            if ($stmt->execute()) {
                $outp = array();
                $result = $stmt->get_result();
                $outp = $result->fetch_all(MYSQLI_ASSOC);
        
                return array('punteggi' => $outp);
            }
        }
    }

    private function templateDateLB($data)
    {
        if ($data != 'GLOBAL') {
            $query = "SELECT punteggi.valore, utenti.id,utenti.username, utenti.dispositivo, utenti.nazione_fk AS nazione FROM utenti,punteggi WHERE utenti.banned = 0 AND punteggi.id_utenteFK=utenti.id and punteggi.id_seasonFK=(SELECT MAX(id) FROM season) AND punteggi.data > DATE_SUB(CURDATE(), INTERVAL 1 $data) ORDER BY punteggi.valore DESC, punteggi.data ASC LIMIT 99;";
        } else {
            $query = "SELECT punteggi.valore, utenti.id,utenti.username, utenti.dispositivo, utenti.nazione_fk AS nazione FROM utenti,punteggi WHERE utenti.banned = 0 AND punteggi.id_utenteFK=utenti.id and punteggi.id_seasonFK=(SELECT MAX(id) FROM season) ORDER BY punteggi.valore DESC, punteggi.data ASC LIMIT 99 ;";
        }
            
        $result = $this->connessione->query($query);
        $outp = $result->fetch_all(MYSQLI_ASSOC);

        return array('punteggi' => $outp);
    }
    
    public function getGlobalLeaderBoard()
    {
        return self::templateDateLB('GLOBAL');
    }
    
    public function getDailyLeaderBoard()
    {
        return self::templateDateLB('DAY');
    }

    public function getWeeklyLeaderBoard()
    {
        return self::templateDateLB('WEEK');
    }

    public function getMonthLeaderBoard()
    {
        return self::templateDateLB('MONTH');
    }
}
