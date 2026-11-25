<?php
require '../database/pdo.php';
include '../database/functions.php';

unset($_SESSION['authenticated']);
unset($_SESSION['auth_user']);

setMessage('You logged out successfully.', 'success');
redirect('login.php');
?>