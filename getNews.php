<?php
require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

getNews();

function getNews()
{
    if ($notizie = $GLOBALS['connessione']->query("SELECT titolo, testo, foto, importante, link FROM notizie WHERE importante = true OR (data_creazione <= NOW() AND NOW() <= scadenza) ORDER BY data_creazione DESC")) {
        echo json_encode(array('news' => $notizie->fetch_all(MYSQLI_ASSOC)));
    }
}
