<?php
include_once("./konekcija.php");

$categId = $_GET["id"];

if(empty($categId)) {
  http_response_code(400);
  die();
}

$items_stmt = $pdo->prepare("SELECT (
  SELECT MAX(cena) as maxCena FROM aukcije WHERE artikal_id = a.id
), id, ime, cena, opis, sl.url, sl.alt FROM artikli a INNER JOIN slike sl on sl.artikal_id = a.id WHERE cat_id = :categId");

if($items_stmt->execute([
  ":categId" => $categId
])) {
  $items = $items_stmt->fetchAll();

  http_response_code(200);
  echo json_encode($items);
}
