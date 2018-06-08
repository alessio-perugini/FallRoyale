<?php
require_once('includes/config.php');

?>
<html>
    <body>
    <div style="width: 49%; float:left">
        <center><b>Login</b></center>
        <form role="form" method="post" action="loginSeason.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="usern" id="usern" class="form-control input-lg" placeholder="User Name" value="junkex2">
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" value="asd1asd">
            </div>
            <div class="form-group">
                <input type="text" name="versione" id="versione" class="form-control input-lg" placeholder="versione" value="1.0.4">
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>
        <center><b>RegistratiSeason</b></center>
        <form role="form" method="post" action="registratiSeason.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="usern" id="usern" class="form-control input-lg" placeholder="User Name" value="junkex2">
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" value="asd1asd">
            </div>

            <div class="form-group">
                <input type="text" name="dispositivo" id="dispositivo" class="form-control input-lg" placeholder="dispositivo" value="Android">
            </div>
            <div class="form-group">
                <input type="text" name="versione" id="versione" class="form-control input-lg" placeholder="versione" value="1.0.4">
            </div>
            <div class="form-group">
                <input type="text" name="nazione" id="nazione" class="form-control input-lg" placeholder="nazione" value="IT">
            </div>

            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>

        <center><b>InsertPunteggioSeason</b></center>
        <form role="form" method="post" action="InsertPunteggioSeason.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="usern" id="usern" class="form-control input-lg" placeholder="User Name" value="junkex2">
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" value="asd1asd">
            </div>
            <div class="form-group">
                <input type="text" name="punteggio" id="punteggio" class="form-control input-lg" placeholder="punteggio" value="20">
            </div>
            <div class="form-group">
                <input type="text" name="versione" id="versione" class="form-control input-lg" placeholder="1.0.5" value="1.0.5">
            </div>
            <div class="form-group">
                <input type="text" name="hash" id="hash" class="form-control input-lg" placeholder="hash" value="SvuGsucO9o14KBfIenJ5wgFFE96EihDE:YBEQyl7oToin+ZX5vB8djxt/">
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>                  
        </form>

        <center><b>ottieniClassificaSeason</b></center>
        <form role="form" method="post" action="ottieniClassificaSeason.php" autocomplete="off">
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>

        <center><b>getNews</b></center>
        <form role="form" method="post" action="getNews.php" autocomplete="off">
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>

        <center><b>getProfile</b></center>
        <form role="form" method="post" action="getProfile.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="profilo" id="profilo" class="form-control input-lg" placeholder="profilo" value="junkex2">
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>
    </div>
    
    <div style="width: 49%; float:right">
        <center><b>changeNazione.php</b></center>
        <form role="form" method="post" action="changeNazione.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="usern" id="usern" class="form-control input-lg" placeholder="User Name" value="junkex2">
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" value="asd1asd">
            </div>
            <div class="form-group">
                <input type="text" name="nazione" id="nazione" class="form-control input-lg" placeholder="nazione" value="GB">
            </div>

            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>

        <center><b>getVersion.php</b></center>
        <form role="form" method="post" action="getVersion.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="dispositivo" id="dispositivo" class="form-control input-lg" placeholder="dispositivo" value="Android">
            </div>

            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>

        <center><b>updateMoney.php</b></center>
        <form role="form" method="post" action="updateMoney.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="usern" id="usern" class="form-control input-lg" placeholder="User Name" value="junkex2">
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" value="asd1asd">
            </div>

            <div class="form-group">
                <input type="text" name="money" id="money" class="form-control input-lg" placeholder="money" value="100">
            </div>

            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>
        <center><b>riscattaCodice.php</b></center>
        <form role="form" method="post" action="riscattaCodice.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="usern" id="usern" class="form-control input-lg" placeholder="User Name" value="junkex2">
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" value="asd1asd">
            </div>

            <div class="form-group">
                <input type="text" name="codice" id="codice" class="form-control input-lg" placeholder="codice" value="AAA">
            </div>

            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>

        <center><b>insertItem.php</b></center>
        <form role="form" method="post" action="insertItem.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="usern" id="usern" class="form-control input-lg" placeholder="User Name" value="junkex2">
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" value="asd1asd">
            </div>

            <div class="form-group">
                <input type="text" name="codice" id="codice" class="form-control input-lg" placeholder="codice" value="AAAA">
            </div>

            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>

        <center><b>getItems</b></center>
        <form role="form" method="post" action="getItems.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="usern" id="usern" class="form-control input-lg" placeholder="User Name" value="junkex2">
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" value="asd1asd">
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>
        <center><b>genSalt</b></center>
        <form role="form" method="post" action="test/genSalt.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="pw" id="pw" class="form-control input-lg" placeholder="pw" value="20">
            </div>

            <div class="form-group">
                <input type="text" name="salt" id="salt" class="form-control input-lg" placeholder="salt" value="">
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>

        <center><b>setCurrentSkin</b></center>
        <form role="form" method="post" action="setCurrentSkin.php" autocomplete="off">
            <div class="form-group">
                <input type="text" name="usern" id="usern" class="form-control input-lg" placeholder="User Name" value="junkex2">
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" value="asd1asd">
            </div>

             <div class="form-group">
                <input type="text" name="codice" id="codice" class="form-control input-lg" placeholder="codice" value="EXPR">
            </div>

            <div class="row">
                <div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Testa" class="btn btn-primary btn-block btn-lg"></div>
            </div>
            <hr>            
        </form>
    </div>
    </body>
</html>