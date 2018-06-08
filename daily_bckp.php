<?php
    $dbFile = 'fallroyale '.date('d-m-y H:m:s').'.sql.gz';
    $dbHost = 'localhost'; // Database Host
    $dbUser = 'root'; // Database Username
    $dbPass = 'RetMyuz1'; // Database Password
    exec( 'mysqldump --host="'.$dbHost.'" --user="'.$dbUser.'" --password="'.$dbPass.'" --add-drop-table "fallroyale" | gzip > "/var/backups/Db/'.$dbFile.'"' );
?>