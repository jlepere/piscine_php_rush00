<?php define('BASENAME', 'rush_00');

session_start();

if (!isset($config))
  require_once('includes/config.php');
require_once('includes/user.php');

if (!($db_conx = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_base'])))
  exit('Database config error, check config.php');

if (($user_logged = user_is_logged($db_conx)) == false)
  header('Location: login.php');

if (isset($_SESSION['item_list'])) {
  $max_value = 0;
  $item_tmp_list = mysqli_query($db_conx, "SELECT * FROM items");
  while ($item = mysqli_fetch_assoc($item_tmp_list)) {
    foreach ($_SESSION['item_list'] as $key => $value) {
      if ($item['item_id'] == $key) {
        $max_value += ($item['item_price'] * $value);
      }
    }
  }
}

if ($max_value <= $user_logged['user_credit'])
  $can_buy = true;

$page_title = 'Votre commande';
$category_list = mysqli_query($db_conx, "SELECT * FROM categories");
include('views/header.php'); ?>
<section class="page">
  <div class="container">
    <?php include('views/sidebar.php'); ?>
    <div class="content">
      <?php if (isset($_SESSION['item_list'])) {
        $user_id = $user_logged['user_id'];
        if ($can_buy) {
          $order_create = mysqli_query($db_conx, "INSERT INTO orders (user_id) VALUES ('$user_id')");
          $order_select = mysqli_query($db_conx, "SELECT * FROM orders ORDER BY order_id DESC LIMIT 1");
          $order_id = mysqli_fetch_assoc($order_select)['order_id'];
        }
        $total_price = 0;
        echo '<p>Votre panier :</p>';
        echo '<ul class="item_user_list">';
        $item_tmp_list = mysqli_query($db_conx, "SELECT * FROM items");
        while ($item = mysqli_fetch_assoc($item_tmp_list)) {
          foreach ($_SESSION['item_list'] as $key => $value) {
            if ($item['item_id'] == $key) {
              $total_price += ($item['item_price'] * $value);
              echo '<li>(x'. $value .') <a href="item.php?id='. $item['item_id'] .'">'. $item['item_name'] .'</a> : <span>'. ($item['item_price'] * $value) .' €</span></li>';
              if ($can_buy) {
                $item_id = $item['item_id'];
                $change_count = ($item['item_count'] - $value);
                mysqli_query($db_conx, "UPDATE items SET item_count='$change_count' WHERE item_id=$item_id");
                mysqli_query($db_conx, "INSERT INTO sales (order_id, item_id, item_count) VALUES ('$order_id', $key, trim($value))");
              }
            }
          }
        }
        $user_id = $user_logged['user_id'];
        if ($can_buy) {$change_credit = $user_logged['user_credit'] - $total_price;}
        mysqli_query($db_conx, "UPDATE users SET user_credit='$change_credit' WHERE user_id=$user_id");
        echo '<li><span style="float: left;">'. ($can_buy ? 'Votre commade a été passé !' : 'Vous n\'avez pas les crédits nécessaire !') .'</span> <span style="text-align: right; display: block;">Prix total : '. $total_price .' €<span></li>';
        echo '</ul>';
        if ($can_buy)
          unset($_SESSION['item_list']);
      } else {
        echo '<p>Votre panier est vide.</p>';
      } ?>
    </div>
  </div>
</section>
<?php include('views/footer.php'); ?>
