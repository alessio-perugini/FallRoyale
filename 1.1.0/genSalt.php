<?php
require_once('classi/crypt/PasswordStorage.php');

$pw = $_POST['pw'];
$salt = $_POST['salt'];//PasswordStorage::create_salt();
$hash = PasswordStorage::create_hash($pw, $salt);

echo $salt . '<br>' . $hash;

// Right password returns true.
$result = PasswordStorage::verify_password($pw, $hash);

if ($result === TRUE)
{
    echo "<br>Correct password: pass\n";
}
else
{
    echo "<br>Correct password: FAIL\n";
    $all_tests_pass = false;
}

?>