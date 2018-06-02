<?php defined('BASENAME') or exit('No direct script access allowed');

function create_user_cookie($db_conx, $query)
{
  $row = mysqli_fetch_assoc($query);
  $user_id = $row['user_id'];
  $user_name = $row['user_name'];
  $user_token = sha1($user_name . (time() + 3600) . $user_id);
  setcookie('is_logged', serialize(array($user_name, $user_token)), time() + 3600, '/');
  return mysqli_query($db_conx, "UPDATE users SET user_token='$user_token' WHERE user_id=$user_id");
}
function user_is_logged($db_conx)
{
  if (isset($_COOKIE['is_logged']))
  {
    $cookie_user = unserialize($_COOKIE['is_logged']);
    if (!empty($cookie_user) && count($cookie_user) == 2)
    {
      $user_name = $cookie_user[0];
      $user_token = $cookie_user[1];
      $res = mysqli_query($db_conx, "SELECT * FROM users WHERE user_name='$user_name' AND user_token='$user_token'");
      if (mysqli_num_rows($res) == 1)
        return mysqli_fetch_assoc($res);
    }
  }
  return false;
} ?>
