<?php require_once('includes/config.php');
//header("Content-Type: application/json; charset=UTF-8");

$username = $_POST['usern'];
$password = $_POST['password'];
$new_username = $_POST['name'];

if (strlen($new_username) >= 3 && strlen($new_username) <= 20) {
    $query = "SELECT id FROM utenti WHERE username = ?";
    $outp = $utils->query($query, array('username' => $new_username));

    if (count($outp) <= 0) {
        setNewUsername($username, $password, $new_username);
    } else {
        echo "username already taken";
    }
}

function setNewUsername($username, $password, $new_username)
{
    $query = "UPDATE utenti SET username = ? WHERE username = ? AND password = ?";
    return $GLOBALS['utils']->query($query, array('new_username' => $new_username, 'username' => $username, 'pw' => $password), false);
}
