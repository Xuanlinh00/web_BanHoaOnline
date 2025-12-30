<?php
require_once 'config/constants.php';
require_once 'config/session.php';

// Destroy session and redirect
session_destroy();
header('Location: ' . APP_URL . '/index.php');
exit;
?>