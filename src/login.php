<?php define('BASENAME', 'rush_00');

session_start();

if (!isset($config))
  require_once('includes/config.php');
require_once('includes/user.php');

if (!($db_conx = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_base'])))
  exit('Database config error, check config.php');

if (user_is_logged($db_conx))
  header('Location: index.php');

if (!empty($_POST)) {
  $user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
  $user_password = filter_var($_POST['user_password'], FILTER_SANITIZE_STRING);
  if (empty($user_name) || empty($user_password) || empty($_POST['user_login']))
    $login_error = "Veuillez remplir tous les champs !";
  if (!isset($login_error)) {
    $user_password = sha1($user_password);
    $res = mysqli_query($db_conx, "SELECT * FROM users WHERE user_name='$user_name' AND user_password='$user_password'");
    if (mysqli_num_rows($res) === 0)
      $login_error = "Identifiant invalide !";
    else
      $is_logged = create_user_cookie($db_conx, $res);
  }
}

$page_title = 'Connexion';

include('views/header.php'); ?>
<section class="page">
  <div class="container">
    <div class="content">
      <h2><?php echo $page_title; ?></h2>
      <?php if ($is_logged) {
        echo '<p><strong>Vous êtes connecté.</strong><br>Vous allez être redirigé !</p>';
        echo '<meta http-equiv="refresh" content="3; URL=index.php">';
      } else {
        if (isset($login_error))
          echo '<p><strong>'. $login_error .'</strong></p>'; ?>
      <form action="" method="post">
        Identifiant: <input type="text" name="user_name" value="<?php if (isset($user_name)) { echo $user_name; } ?>"><br>
        Mot de passe: <input type="password" name="user_password"><br>
        <input type="submit" name="user_login" value="<?php echo $page_title; ?>">
      </form>
      <?php } ?>
    </div>
  </div>
</section>
<?php include('views/footer.php'); ?>
