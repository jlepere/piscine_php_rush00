<?php defined('BASENAME') or exit('No direct script access allowed'); ?>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php echo $config['site_name']; ?> | <? echo $page_title; ?></title>
  </head>
  <body>
    <header>
      <div class="container">
        <a id="logo" href="index.php" title="<?php echo $config['site_name']; ?>">
          <h1><?php echo $config['site_name']; ?></h1>
        </a>
        <?php if (isset($user_logged) && $user_logged !== false): ?>
        <p class="header-text" >Bienvenue <?php
          echo $user_logged['user_name']; ?> - Cr&eacute;dit: <?= $user_logged["user_credit"] ?> &euro; - <?php
          if ($user_logged['user_rank'] == 1)
            echo ' <a href="admin.php" title="Administration">Administration</a>';
          ?> - <a href="logout.php" title="Se déconnecter">Se déconnecter</a></p>
        <?php else: ?>
        <p class="header-text"><a href="login.php" title="Se connecter">Se connecter</a> - <a href="register.php" title="Pas de compte ? Inscris toi !">Pas de compte ? Inscris toi !</a></p>
        <?php endif; ?>
      </div>
    </header>
