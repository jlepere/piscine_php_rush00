<?php define('BASENAME', 'rush_00');

if (isset($_COOKIE['is_logged']))
  setcookie('is_logged', '', time() + 3600, '/');
header('Location: index.php'); ?>
