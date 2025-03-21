<?php
$password = '1234';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
echo $hashedPassword;
?>