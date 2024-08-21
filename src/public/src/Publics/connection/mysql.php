<?php
$dbhost = "localhost";
$dbname = "factory";
$dbuser = "root";
$dbpass = "Admin@cpl2017";
$dbchar = "utf8";

try {

  $host = "mysql:host={$dbhost};dbname={$dbname};charset={$dbchar}";
  $dbcon = new PDO($host, $dbuser, $dbpass);
  $dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("ERROR: Could not connect. " . $e->getMessage());
}
