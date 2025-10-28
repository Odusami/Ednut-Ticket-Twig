<?php
session_start();
// Set toast message for logout
$_SESSION['toast'] = [
    'type' => 'info',
    'message' => 'Logged out successfully'
];

session_destroy();
header('Location: ../index.php');
exit;
?>