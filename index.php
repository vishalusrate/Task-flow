<?php
require_once 'config.php';
header('Location: ' . (isLoggedIn() ? 'pages/dashboard.php' : 'login.php'));
exit;
