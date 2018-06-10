<?php
require_once('includes/config.php');
$username = $_POST['usern'];
$password = $_POST['password'];
$money = $_POST['money'];

$user->updateMoney($username, $password, $money);
