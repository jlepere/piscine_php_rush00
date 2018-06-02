<div class="sidebar">
  <?php if ($_GET['action'] === 'del_all_item') { unset($_SESSION['item_list']); } ?>
  <div class="item_search">
    <p>Recherche par nom de produit :</p>
    <form action="search.php" method="get">
      <input type="text" name="item_name" value="<?php if (isset($_GET['name_search_submit'])) { echo $_GET['item_name']; } ?>">
      <input type="submit" name="name_search_submit" value="Recherche">
    </form>
  </div>
  <?php if (mysqli_num_rows($category_list) != 0) { ?>
  <div class="category_search">
    <p>Recherche par catégorie :</p>
    <form action="search.php" method="get">
      <select name="category_search">
      <?php while ($item = mysqli_fetch_assoc($category_list))
        echo '<option value="'. $item['category_id'] .'" '. (isset($_GET['category_search']) && $_GET['category_search'] == $item['category_id'] ? 'selected' : '') .'>'. $item['category_name'] .'</option>'; ?>
      </select>
      <input type="submit" name="category_search_submit" value="Recherche">
    </form>
  </div>
  <?php } ?>
  <div class="item_user_list">
    <?php if (isset($_SESSION['item_list'])) {
      $total_price = 0;
      echo '<p>Votre panier :</p>';
      echo '<ul class="item_user_list">';
      $item_tmp_list = mysqli_query($db_conx, "SELECT * FROM items");
      while ($item = mysqli_fetch_assoc($item_tmp_list)) {
        foreach ($_SESSION['item_list'] as $key => $value) {
          if ($item['item_id'] == $key) {
            $total_price += ($item['item_price'] * $value);
            echo '<li>(x'. $value .') <a href="item.php?id='. $item['item_id'] .'">'. $item['item_name'] .'</a> : <span>'. ($item['item_price'] * $value) .' €</span></li>';
          }
        }
      }
      echo '<li><span style="text-align: right; display: block;">Prix total : '. $total_price .' €<span></li>';
      echo '<li><span style="text-align: right; display: block;">'. ($user_logged ? '<a href="order.php">Passer commande</a><br>' : '') .'<a href="index.php?action=del_all_item">Vider le panier</a></span></li>';
      echo '</ul>';
    } else {
      echo '<p>Votre panier est vide.</p>';
    } ?>
  </div>
</div>
