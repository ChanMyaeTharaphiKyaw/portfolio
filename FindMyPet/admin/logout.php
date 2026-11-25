<?php
require '../database/pdo.php';
include '../database/functions.php';

unset($_SESSION['admin_authenticated']);
unset($_SESSION['admin_user']);

// Redirect to admin login page
setMessage('You logged out successfully.', 'success');
redirect("login.php");
?>