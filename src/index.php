<?php define('BASENAME', 'rush_00');

session_start();

if (!isset($config))
  require_once('includes/config.php');
require_once('includes/user.php');

if (!($db_conx = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_base'])))
  exit('Database config error, check config.php');


$user_logged = user_is_logged($db_conx);

$page_title = 'Accueil';
$category_list = mysqli_query($db_conx, "SELECT * FROM categories");
$item_list = mysqli_query($db_conx, "SELECT * FROM items ORDER BY item_id DESC LIMIT 6");
include('views/header.php'); ?>
<section class="page">
  <div class="container">
    <?php include('views/sidebar.php'); ?>
    <div class="content">
      <?php if (mysqli_num_rows($item_list) != 0) { ?>
      <ul class="item-list">
        <?php while ($item = mysqli_fetch_assoc($item_list)) { ?>
        <li>
          <a href="item.php?id=<?php echo $item['item_id']; ?>" title="<?php echo $item['item_name']; ?>">
            <p class="name"><?php echo $item['item_name']; ?></p>
            <img src="<?php echo $item['item_photo']; ?>" alt="<?php echo $item['item_name']; ?>" width="" height="">
            <p class="desc"><?php echo $item['item_desc']; ?></p>
            <p class="info<?php if ($item['item_count'] <= 0) { echo ' none'; } elseif ($item['item_count'] <= 10) { echo ' low'; } ?>"><span><?php echo $item['item_price']; ?> &euro;</span> <span><?php echo $item['item_count']; ?> dispo !</span></p>
          </a>
        </li>
        <?php } ?>
      </ul>
      <?php } else {
        echo '<p>Il n\'a pas de produits dans la base de donnée !';
      } ?>
    </div>
  </div>
</section>
<?php include('views/footer.php'); ?>
