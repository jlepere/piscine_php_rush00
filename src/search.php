<?php define('BASENAME', 'rush_00');

session_start();

if (!isset($config))
  require_once('includes/config.php');
require_once('includes/user.php');

if (!($db_conx = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_base'])))
  exit('Database config error, check config.php');

$user_logged = user_is_logged($db_conx);

$page_title = 'Recherche';
$category_list = mysqli_query($db_conx, "SELECT * FROM categories");
$search_item = array();
if (isset($_GET['name_search_submit']) && !empty($_GET['item_name'])) {
  $item_list = mysqli_query($db_conx, "SELECT * FROM items");
  while ($item = mysqli_fetch_assoc($item_list)) {
    if (strpos(strtolower($item['item_name']), strtolower($_GET['item_name'])) !== false)
      $search_item[] = $item;
  }
}
if (isset($_GET['category_search_submit']) && !empty($_GET['category_search'])) {
  $item_list = mysqli_query($db_conx, "SELECT * FROM items");
  while ($item = mysqli_fetch_assoc($item_list)) {
    if (strpos(strtolower($item['item_categories']), strtolower($_GET['category_search'] .',')) !== false)
      $search_item[] = $item;
  }
}

include('views/header.php'); ?>
<section class="page">
  <div class="container">
    <?php include('views/sidebar.php'); ?>
    <div class="content">
      <?php if (count($search_item) != 0) { ?>
      <ul class="item-list">
        <?php foreach ($search_item as $item) { ?>
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
        echo '<p>Cette recherche n\'a rien donnée !</p>';
      } ?>
    </div>
  </div>
</section>
<?php include('views/footer.php'); ?>
