<?php

session_start();

try {
  $pdo = new PDO('mysql:host=localhost;dbname=site;charset=utf8', 'root', '');
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_CLASS);
} catch(PDOException $e) {
  die();
}
