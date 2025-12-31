<?php
define('ROOT_DIR', dirname(dirname(__DIR__)));
require_once ROOT_DIR . '/config/constants.php';
require_once ROOT_DIR . '/config/session.php';

// Destroy session and redirect
session_destroy();
header('Location: ' . APP_URL . '/');
exit;
?>