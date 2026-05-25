<?php
require_once __DIR__ . '/../inc/config.php';
session_destroy();
session_unset();
header('Location: login.php');
exit;
