<?php
    require_once('includes/config.php');
    $nome = $_POST['nome'];
    $costo =  $_POST['costo'];
    $codice = $_POST['codice'];

    insertNewSkin($nome, $costo, $codice);

    function checkNoDuplicate_db($codice){
        $risultati = $GLOBALS['connessione']->query("SELECT id FROM items WHERE codice = $codice");
        if(isset($risultati->num_rows))
        {
            return false;
        }else{
            return true;
        }
    }

    function insertNewSkin($nome, $costo, $codice){
        if(checkNoDuplicate_db($codice)){
            $query = "INSERT INTO items (nome, costo, codice) VALUES( ?, ?, ?)";
        
            if($stmt = $GLOBALS['connessione']->prepare($query))
            {
                $stmt->bind_param('sis', $nome, $costo, $codice);
                if($stmt->execute()){
                    echo "nome: $nome costo: $costo codice: $codice INSERITA!<br>";
                }
            }
        }else{
            echo "Skin già esistente";
        }

    }
?>