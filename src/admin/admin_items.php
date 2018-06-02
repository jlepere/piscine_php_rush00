<?php define('BASENAME', 'rush_00');

$category_list = mysqli_query($db_conx, "SELECT * FROM categories");

if ($_GET['action'] === 'delete' && !empty($_GET['id'])) {
  $item_id = trim($_GET['id']);
  $res = mysqli_query($db_conx, "DELETE FROM items WHERE item_id=$item_id");
} else if ($_GET['action'] === 'add' && !empty($_POST)) {
  $item_name = filter_var($_POST['item_name'], FILTER_SANITIZE_STRING);
  $item_desc = filter_var($_POST['item_desc'], FILTER_SANITIZE_STRING);
  $item_price = filter_var($_POST['item_price'], FILTER_SANITIZE_NUMBER_INT);
  $item_count = filter_var($_POST['item_count'], FILTER_SANITIZE_NUMBER_INT);
  $item_category = (is_array($_POST['item_category']) && !empty($_POST['item_category']) ? filter_var_array($_POST['item_category'], FILTER_SANITIZE_STRING) : []);

  if (empty($item_name) || empty($item_desc) || empty($item_price) ||
    empty($item_count) ||  empty($_POST['submit_item']))
    $add_error = "Veuillez remplir tous les champs !";
  if (strlen($item_name) > 64)
    $add_error = "Nom du produit : max 64 caractères !";
  if (strlen($item_desc) > 255)
    $add_error = "Description du produit : max 255 caractères !";
  if ($item_price < 0)
    $add_error = "Prix du produit: min 0 !";
  if ($item_count < 0)
    $add_error = "Nombre de produit: min 0 !";

  $categ = [];
  foreach ($item_category as $key => $category) {
    $needle = $item_category[$key];
    $categ[$key] = mysqli_query($db_conx, "SELECT category_id FROM categories WHERE category_name='$needle'");
    if (mysqli_num_rows($categ[$key]) != 1)
      $add_error = "Choisir une catégorie valide !";
    $categ[$key] = mysqli_fetch_assoc($categ[$key])['category_id'];
  }

  $res = mysqli_query($db_conx, "SELECT item_id FROM items WHERE item_name='$item_name'");
  if (mysqli_num_rows($res) > 0)
    $add_error = "Il y a déjà un produit à ce nom !";
  if ($_FILES['item_photo']['size'] == 0)
    $add_error = "Une image pour le produit !";

  $target_ext = '.'. pathinfo($_FILES['item_photo']['name'], PATHINFO_EXTENSION);
  $target_name = sha1(basename($_FILES['item_photo']['name']) . (string)time());
  $target_file = 'assets/upload/'. $target_name . $target_ext;
  while (file_exists($target_file)) {
    $target_name = sha1(basename($_FILES['item_photo']['name']) . (string)time());
    $target_file = 'assets/upload/'. $target_name . $target_ext;
  }
  if (strlen(basename($FILES['item_photo']['name']) > 256))
    $add_error = "Le nom du fichier photo est trop long !";
  if ($_FILE['item_photo']['size'] > 5000000)
    $add_error = "Le fichier photo est trop lourd !";
  if (!isset($add_error)) {
    if (move_uploaded_file($_FILES['item_photo']['tmp_name'], $target_file)) {
      var_dump($categ);
      $item_categories = serialize([]);
      /*$item_categories = [];
      foreach ($categ as $key => $category)
        $item_categories .= $category.",";*/
      $is_added = mysqli_query($db_conx, "INSERT INTO items (item_name, item_desc, item_photo, item_price, item_count, item_categories) VALUES ('$item_name', '$item_desc', '$target_file', '$item_price', '$item_count', '$item_categories')");
    }
  }
}

if (isset($add_error))
  echo '<p><strong>'. $add_error .'</strong></p>';

if ($_GET['action'] === 'add') { ?>
<form action="admin.php?section=items&action=add" method="post" enctype="multipart/form-data">
  Nom du produit: <input type="text" name="item_name"><br>
  Description du produit: <input type="text" name="item_desc"><br>
  Photo du produit: <input type="file" name="item_photo"><br>
  Prix du produit: <input type="number" name="item_price" value="0"><br>
  Nombre de produit disponibles: <input type"number" name="item_count" value="0"><br>
  <?php if (mysqli_num_rows($category_list) !== 0) {
    echo 'Catégorie du produit:';
    while ($item = mysqli_fetch_assoc($category_list))
      echo '<input type="checkbox" name="item_category[]" value="'. $item['category_name'] .'"> '. $item['category_name'];
  } ?>
  <br>
  <input type="submit" name="submit_item" value="Ajouter">
</form>
<?php } else { ?>
<div class="admin-details">
  <ul class="admin-actions">
		<li><a href="admin.php?section=items&action=add" title="Ajouter un produit">Ajouter un produit</a></li>
	</ul>

</div>
<?php } ?>
