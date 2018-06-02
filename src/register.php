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
  if (strlen($user_name) > 32)
    $register_error = "L'identifiant ne doit pas dépasser 32 caractères !";
  if (empty($user_name) || empty($user_password) || empty($_POST['user_register']))
    $register_error = "Veuillez remplir tous les champs !";
  if (!isset($register_error)) {
    $user_password = sha1($user_password);
    $res = mysqli_query($db_conx, "SELECT user_id FROM users WHERE user_name='$user_name'");
    if (mysqli_num_rows($res) !== 0)
      $register_error = "Il y a déjà un compte avec cet identifiant !";
    else
      $is_registered = mysqli_query($db_conx, "INSERT INTO users (user_name, user_password) VALUES ('$user_name', '$user_password')");
  }
}

$page_title = 'Inscription';

include('views/header.php'); ?>
<section class="page">
  <div class="container">
    <div class="content">
      <h2><?php echo $page_title; ?></h2>
      <?php if ($is_registered) {
        echo '<p><strong>Votre compte a été créé !</strong><br>Vous allez être redirigés !</p>';
        echo '<meta http-equiv="refresh" content="3; URL=login.php">';
      } else {
        if (isset($register_error))
          echo '<p><strong>'. $register_error .'</strong></p>'; ?>
      <form action="" method="post">
        Identifiant: <input type="text" name="user_name" value="<?php if (isset($user_name)) { echo $user_name; } ?>"><br>
        Mot de passe: <input type="password" name="user_password"><br>
        <input type="submit" name="user_register" value="<?php echo $page_title; ?>">
      </form>
      <?php } ?>
    </div>
  </div>
</section>
<?php include('views/footer.php'); ?>
