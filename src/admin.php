<?php define('BASENAME', 'rush_00');

session_start();

if (!isset($config))
  require_once('includes/config.php');
require_once('includes/user.php');

if (!($db_conx = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_base'])))
  exit('Database config error, check config.php');

if (!($user_logged = user_is_logged($db_conx)) || $user_logged['user_rank'] != 1)
  header('Location: index.php');

$page_title = 'Administration';

include('views/header.php'); ?>
<section class="page">
  <div class="container">
    <nav class="admin">
      <ul>
        <li><a href="admin.php?section=users" title="Liste des comptes">Liste des comptes</a></li>
        <li><a href="admin.php?section=categories" title="Gestion des catégories">Gestion des catégories</a></li>
        <li><a href="admin.php?section=items" title="Gestion des produits">Gestion des produits</a></li>
        <li><a href="admin.php?section=orders" title="Gestion des commandes">Gestion des commandes</a></li>
      </ul>
    </nav>
    <div class="content">
      <?php if (isset($_GET['section'])) {
        if ($_GET['section'] === 'users')
          include('admin/admin_users.php');
        else if ($_GET['section'] === 'categories')
          include('admin/admin_categories.php');
        else if ($_GET['section'] === 'items')
          include('admin/admin_items.php');
        else if ($_GET['section'] === 'orders')
          include('admin/admin_orders.php');
      } ?>
    </div>
  </div>
</section>
<?php include('views/footer.php'); ?>
