<?php
require_once('includes/config.php');

$result = $connessione->query(" SELECT p.sigla
FROM   codici p
       LEFT OUTER JOIN codici_riscattati s ON s.id_codice_fk  = p.id
WHERE  s.id_codice_fk IS NULL");
$outp = array();
$outp = $result->fetch_all(MYSQLI_ASSOC);

for($i = 0; $i < count($outp); $i++){
	echo $outp[$i]['sigla'] . "<br>";
}
?>