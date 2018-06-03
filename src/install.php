<?php define('BASENAME', 'rush_00');

session_start();

if (!isset($config))
  require_once('includes/config.php');

if (!($db_conx = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'])))
  exit('Database config error, check config.php');

$create_db = mysqli_query($db_conx, "CREATE DATABASE IF NOT EXISTS ". $config['db_base']);

if ($create_db && mysqli_select_db($db_conx, $config['db_base'])) {
  $create_user = mysqli_query($db_conx, "CREATE TABLE IF NOT EXISTS users (
    user_id INT NOT NULL AUTO_INCREMENT,
    user_name VARCHAR(32),
    user_password VARCHAR(42),
    user_token VARCHAR(42),
    user_rank INT(1) DEFAULT '0',
    user_credit INT DEFAULT '10',
    PRIMARY KEY (user_id),
    UNIQUE (user_id, user_name)
  )");
  if ($create_user) {
    $pass = sha1('admin');
    mysqli_query($db_conx, "INSERT INTO users (user_name, user_password, user_rank, user_credit) VALUES ('admin', '$pass', 1, 100000)");
  }

  $create_categories = mysqli_query($db_conx, "CREATE TABLE IF NOT EXISTS categories (
    category_id INT NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(64),
    category_desc VARCHAR(255),
    PRIMARY KEY (category_id),
    UNIQUE (category_id, category_name)
  )");

  $create_items = mysqli_query($db_conx, "CREATE TABLE IF NOT EXISTS items (
    item_id INT NOT NULL AUTO_INCREMENT,
    item_name VARCHAR(64),
    item_desc VARCHAR(255),
    item_photo VARCHAR(255) DEFAULT 'upload/default.jpg',
    item_price FLOAT(11) DEFAULT '0',
    item_count INT(11) DEFAULT '0',
    item_categories TEXT,
    PRIMARY KEY (item_id),
    UNIQUE (item_id, item_name)
  )");

  $create_order = mysqli_query($db_conx, "CREATE TABLE IF NOT EXISTS orders (
    order_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    PRIMARY KEY (order_id),
    UNIQUE (order_id)
  )");
  $create_sales = mysqli_query($db_conx, "CREATE TABLE IF NOT EXISTS sales (
    sale_id INT NOT NULL AUTO_INCREMENT,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    item_count INT NOT NULL,
    PRIMARY KEY (sale_id),
    UNIQUE (sale_id)
  )");
  if ($create_user && $create_categories && $create_items && $create_order && $create_sales)
    echo 'DB OK !';
} ?>
