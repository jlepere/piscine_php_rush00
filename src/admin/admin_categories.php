<?php define('BASENAME', 'rush_00');

if ($_GET['action'] === 'modif' && !empty($_GET['id'])) {
  $category_id = trim($_GET['id']);
  $res = mysqli_query($db_conx, "SELECT * FROM categories WHERE category_id='$category_id'");
  $row = mysqli_fetch_assoc($res);
  $category_name = $row['category_name'];
  $category_desc = $row['category_desc'];
}

if ($_GET['action'] === 'delete' && !empty($_GET['id'])) {
  $category_id = trim($_GET['id']);
  $res = mysqli_query($db_conx, "DELETE FROM categories WHERE category_id=$category_id");
}

if (($_GET['action'] === 'add' || $_GET['action'] === 'modif') && !empty($_POST)) {
  $category_name = filter_var($_POST['category_name'], FILTER_SANITIZE_STRING);
  $category_desc = filter_var($_POST['category_desc'], FILTER_SANITIZE_STRING);
  if (empty($category_name) || empty($category_desc) || empty($_POST['submit_category']))
    $add_error = "Veuillez remplir tous les champs !";

  if (strlen($category_name) > 64)
    $add_error = "Nom de la catégorie : max 64 caractères !";
  if (strlen($category_desc) > 255)
    $add_error = "Description de la catégorie : max 255 caractères !";
  if (!isset($add_error)) {
    if ($_GET['action'] === 'add') {
      $res = mysqli_query($db_conx, "SELECT category_id FROM categories WHERE category_name='$category_name'");
      if (mysqli_num_rows($res) !== 0)
        $add_error = "Il y'a déjà une categorie à ce nom !";
      else
        $is_added = mysqli_query($db_conx, "INSERT INTO categories (category_name, category_desc) VALUES ('$category_name', '$category_desc')");
    }
    else if ($_GET['action'] === 'modif')
      $is_added = mysqli_query($db_conx, "UPDATE categories SET category_name='$category_name', category_desc='$category_desc' WHERE category_id='$category_id'");
  }
}

if ($is_added)
  echo '<meta http-equiv="refresh" content="0; URL=admin.php?section=categories">'; ?>
<div class="admin-details">
  <ul class="admin-actions">
    <li><a href="admin.php?section=categories&action=add" title="Ajouter une catégorie">Ajouter une catégorie</a></li>
  </ul>
  <?php if ($_GET['action'] === 'add' || ($_GET['action'] === 'modif' && !empty($row))) {
    if (isset($add_error))
      echo '<p><strong>'. $add_error .'</strong></p>'; ?>
  <form action="admin.php?section=categories&action=<?php echo $_GET['action']; if ($_GET['action'] === 'modif') { echo '&id='. $category_id; } ?>" method="post">
    Nom de la catégorie: <input type="text" name="category_name" value="<?php echo $category_name; ?>"><br>
    Description de la catégorie: <input type="text" name="category_desc" value="<?php echo $category_desc; ?>"><br>
    <input type="submit" name="submit_category" value="<?php echo ($_GET['action'] === 'add' ? 'Ajouter' : 'Modifier' ) ?>">
  </form>
  <?php } else {
    $res = mysqli_query($db_conx, "SELECT * FROM categories");
    if (mysqli_num_rows($res) !== 0) {
      while ($item = mysqli_fetch_assoc($res)) { ?>
  <div>
    <strong><?php echo $item['category_name']; ?></strong> <?php echo $item['category_desc']; ?>
    <ul>
      <li><a href="admin.php?section=categories&action=modif&id=<?php echo $item['category_id']; ?>" title="Modifer">Modifier</a></li>
      <li><a href="admin.php?section=categories&action=delete&id=<?php echo $item['category_id']; ?>" title="Supprimer">Supprimer</a></li>
    </ul>
  </div>
  <?php } } else { echo '<p>Il n\'y a pas de catégories !</p>'; } } ?>
</div>
