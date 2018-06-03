<?php define('BASENAME', 'rush_00');

session_start();

if (!isset($config))
  require_once('includes/config.php');
require_once('includes/user.php');

if (!($db_conx = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_base'])))
  exit('Database config error, check config.php');

$user_logged = user_is_logged($db_conx);

if ($_GET['action'] === 'del_item' && isset($_GET['id'])) {
  $nb_item = $_SESSION['item_list'][$_GET['id']];
  if ($nb_item !== null)
    unset($_SESSION['item_list'][$_GET['id']]);
}

if ($_GET['action'] === 'add_item' && isset($_GET['id'])) {
  $get_id = trim($_GET['id']);
  $nb_item = $_SESSION['item_list'][$_GET['id']];
  $add_item = mysqli_query($db_conx, "SELECT * FROM items WHERE item_id='$get_id'");
  if ($add_item !== false && mysqli_num_rows($add_item) != 0) {
    $item_query = mysqli_fetch_assoc($add_item);
    if ($nb_item === null && $item_query['item_count'] > 0) {
      $nb_item = 1;
      $_SESSION['item_list'][$_GET['id']] = $nb_item;
    }
    else if ($nb_item !== null && $item_query['item_count'] >= $nb_item + 1) {
      $nb_item++;
      $_SESSION['item_list'][$_GET['id']] = $nb_item;
    }
  }
}

$category_list = mysqli_query($db_conx, "SELECT * FROM categories");
if (isset($_GET['id'])) {
  $item_id = trim($_GET['id']);
  $item_list = mysqli_query($db_conx, "SELECT * FROM items WHERE item_id='$item_id'");
  if ($item_list !== false && mysqli_num_rows($item_list) !== 0)
    $selected_item = mysqli_fetch_assoc($item_list);
  else
    $page_title = 'Produit non trouvé !';
} else
  $page_title = 'Produit non trouvé !';

if (isset($selected_item))
  $page_title = $selected_item['item_name'];
include('views/header.php'); ?>
<section class="page">
  <div class="container">
    <?php include('views/sidebar.php'); ?>
    <div class="content page-item">
      <?php if (isset($selected_item)) { ?>
      <h2><?php echo $selected_item['item_name']; ?></h2>
      <img class="photo" src="<?php echo $selected_item['item_photo']; ?>" alt="<?php echo $selected_item['item_name']; ?>">
      <p class="desc"><?php echo $selected_item['item_desc']; ?></p>
      <p class="price">Prix : <?php echo $selected_item['item_price']; ?> &euro;</p>
      <p class="count<?php if ($selected_item['item_count'] <= 0) { echo ' none'; } elseif ($selected_item['item_count'] <= 10) { echo ' low'; } ?>"><?php echo $selected_item['item_count']; ?> disponible</p>
      <?php if ($selected_item['item_count'] > 0)
        echo '<a class="panier" href="item.php?action=add_item&id='. $selected_item['item_id'] .'" title="Ajouter au panier">Ajouter au panier</a>';
      } else {
        echo '<p>Il n\'a pas de produits dans la base de donnée !';
      } ?>
    </div>
  </div>
</section>
<?php include('views/footer.php'); ?>
