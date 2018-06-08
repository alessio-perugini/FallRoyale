<?php require_once('includes/config.php');
header("Content-Type: application/json; charset=UTF-8");

$tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : 'globale';
$nazione = (isset($_POST['nazione'])) ? $_POST['nazione'] : '';

if (isset($tipo)) {
    switch ($tipo) {
        case 'globale': echo json_encode($classifica->getGlobalLeaderBoard()); break;
        case 'day': echo json_encode($classifica->getDailyLeaderBoard()); break;
        case 'week': echo json_encode($classifica->getWeeklyLeaderBoard()); break;
        case 'month': echo json_encode($classifica->getMonthLeaderBoard()); break;
        case 'nazione': echo json_encode($classifica->getNationalityLeaderBoard($nazione)); break;
    }
}
