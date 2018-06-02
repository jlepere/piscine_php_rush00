<?php define('BASENAME', 'rush_00');

if ($_GET['action'] === 'delete' && !empty($_GET['id'])) {
	$user_id = trim($_GET['id']);
	$res = mysqli_query($db_conx, "DELETE FROM users WHERE user_id=$user_id");
}

if ($_GET['action'] === 'add' && !empty($_POST)) {
  $user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
  $user_password = filter_var($_POST['user_password'], FILTER_SANITIZE_STRING);
	$user_rank = filter_var($_POST['user_rank'], FILTER_SANITIZE_NUMBER_INT);
	$user_credit = filter_var($_POST['user_credit'], FILTER_SANITIZE_NUMBER_INT);
	if (empty($user_name) || (empty($user_rank) && $user_rank !== "0") || empty($user_credit) || empty($user_password)
		|| empty($_POST['submit_user']))
		$add_error = "Veuillez remplir tous les champs !";
	if (strlen($user_name) > 32)
		$add_error = "Nom d'utilisateur : max 32 caractères !";
	if ($user_rank < 0 || $user_rank > 1)
		$add_error = "Rang d'utilisateur incorrect !";
	if (!isset($add_error)) {
		$res = mysqli_query($db_conx, "SELECT user_id FROM users WHERE user_name='$user_name'");
		if (mysqli_num_rows($res) !== 0)
			$add_error = "Il y'a déjà un utilisateur à ce nom !";
		else {
			$user_password = sha1($user_password);
			$is_added = mysqli_query($db_conx, "INSERT INTO users (user_name, user_password, user_rank, user_credit) VALUES ('$user_name', '$user_password', '$user_rank', '$user_credit')");
		}
	}
} else if  ($_GET['action'] === 'modif' && !empty($_POST)) {
	$user_name = filter_var($_POST['user_name'], FILTER_SANITIZE_STRING);
	$user_rank = filter_var($_POST['user_rank'], FILTER_SANITIZE_NUMBER_INT);
	$user_credit = filter_var($_POST['user_credit'], FILTER_SANITIZE_NUMBER_INT);
	if (empty($user_name) || (empty($user_rank) && $user_rank !== "0") || empty($user_credit) || empty($_POST['submit_user']))
		$add_error = "Veuillez remplir tous les champs !";
	if ($user_rank < 0 || $user_rank > 1)
    $add_error = "Rang d'utilisateur incorrect !";
	if (!isset($add_error)) {
		$res = mysqli_query($db_conx, "SELECT user_id FROM users WHERE user_name='$user_name'");
		if (mysqli_num_rows($res) === 0)
			$add_error = "Il n'y pas d'utilisateur à ce nom !";
		$is_added = mysqli_query($db_conx, "UPDATE users SET user_rank='$user_rank', user_credit='$user_credit' WHERE user_name='$user_name'");
	}
}

if (isset($add_error))
  echo '<p><strong>'. $add_error .'</strong></p>';

$res = mysqli_query($db_conx, "SELECT * FROM users");
$row = mysqli_fetch_all($res);

if ($_GET['action'] === 'add') { ?>
<form action="admin.php?section=users&action=add" method="post">
	Nom de l'utilisateur : <input type="text" name="user_name"><br>
	Mot de passe de l'utilisateur : <input type="password" name="user_password"><br>
	Rang de l'utilisateur : <select name="user_rank"><option value="0">Membre</option><option value="1">admin</option></select><br>
	Crédit de l'utilisateur : <input type="number" name="user_credit" vqlue="0"><br>
	<input type="submit" name="submit_user" value="Ajouter">
</form>
<?php } else if ($_GET['action'] === 'modif' && !empty($row)) { ?>
<form action="admin.php?section=users&action=modif" method="post">
  Nom de l'utilisateur : <span><?php echo $_GET['user_name']; ?></span><input type="hidden" name="user_name" value="<?php echo $_GET['user_name'] ?>"><br>
	Rang de l'utilisateur : <select name="user_rank"><option value="0" <?php if (!$row[0][4]) { echo 'selected'; } ?>>Membre</option><option value="1" <?php if ($row[0][4]) { echo 'selected'; } ?>>admin</option></select><br>
	Crédit de l'utilisateur : <input type="number" name="user_credit" value="<?php echo $row[0][5]; ?>"><br>
	<input type="submit" name="submit_user" value="Modifier">
</form>
<?php } else {
  if (!empty($row)) { ?>
<div class="admin-details">
  <ul class="admin-actions">
		<li><a href="admin.php?section=users&action=add" title="Ajouter un compte">Ajouter un compte</a></li>
	</ul>
  <?php foreach ($row as $item) { ?>
  <div>
    <?php echo $item[1] .' - Rank: '. $item[4] .' - Crédit: '. $item[5] .'&euro;'; ?>
    <ul>
      <li><a href="admin.php?section=users&action=modif&id=<?php echo $item[0]; ?>&user_name=<?php echo $item[1]; ?>" title="Modifier">Modifier</a></li>
      <li><a href="admin.php?section=users&action=delete&id=<?php echo $item[0]; ?>" title="Supprimer">Supprimer</a></li>
    </ul>
  </div>
  <?php } ?>
</div>
<?php } } ?>
