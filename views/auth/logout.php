<?php
require_once 'config/constants.php';
require_once 'config/session.php';

session_destroy();
header('Location: /web_banhoa/index.php');
exit;
?>
