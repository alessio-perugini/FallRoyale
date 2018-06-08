<?php
require_once('includes/config.php');

?>
<html>
    <body>
        <center><b>CreaCodici</b></center>
        <form role="form" method="post" action="crea_codici.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="n_codici" id="n_codici" class="form-control input-lg" placeholder="n_codici" value="2">
            </div>

            <div class="form-group">
                <input type="text" name="valore" id="valore" class="form-control input-lg" placeholder="valore" value="100">
            </div>
            <div class="form-group">
                <input type="text" name="scadenza" id="scadenza" class="form-control input-lg" placeholder="scadenza" value="2018-04-28 18:19:48">
            </div>
            <div class="form-group">
                <input type="text" name="tipo_codice" id="tipo_codice" class="form-control input-lg" placeholder="tipo_codice" value="1">
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>                  
        </form>
        <center><b>inserisci_skin</b></center>
        <form role="form" method="post" action="inserisci_skin.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="nome" id="nome" class="form-control input-lg" placeholder="nome" value="asd asd as ">
            </div>

            <div class="form-group">
                <input type="text" name="costo" id="costo" class="form-control input-lg" placeholder="costo" value="100">
            </div>
            <div class="form-group">
                <input type="text" name="codice" id="codice" class="form-control input-lg" placeholder="codice" value="XYZW">
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>                  
        </form>
    </body>
</html>