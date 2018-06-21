<?php
require_once('includes/config.php');
    $n_codici = $_POST['n_codici'];
    $valore =  $_POST['valore'];
    $scadenza = $_POST['scadenza'];
    $tipo_codice = $_POST['tipo_codice'];
    
    if ($scadenza == '') {
        $scadenza = null;
    }

    if (isset($n_codici)) {
        for ($i = 0; $i < $n_codici; $i++) {
            insertNewCode(random_str(9), $scadenza, $tipo_codice, $valore);
        }
    }

    function checkNoDuplicate_db($codice)
    {
        $risultati = $GLOBALS['connessione']->query("SELECT id FROM codici WHERE sigla = $codice");
        if (isset($risultati->num_rows)) {
            checkNoDuplicate_db(random_str(9));
        } else {
            return $codice;
        }
    }

    function insertNewCode($codice, $scadenza, $tipo_codice, $valore)
    {
        $codice = checkNoDuplicate_db($codice);
        $query = "INSERT INTO codici (sigla, data_creazione, scadenza, tipo_codice_fk, valore) VALUES( ?, now(), ?, ?, ?)";
        
        if ($stmt = $GLOBALS['connessione']->prepare($query)) {
            $stmt->bind_param('ssii', $codice, $scadenza, $tipo_codice, $valore);
            if ($stmt->execute()) {
                echo "$codice";
            }
        }
    }

    function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
