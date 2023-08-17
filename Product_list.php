<?php

// CORS 허용
include_once 'cors.php';

// 데이터베이스 연결
include './dbconn.php';

// GET으로 받아오기
$ProductID = (int)$_GET['id'];
$ProductID = mysqli_real_escape_string($conn, $ProductID);

// 쿼리문 작성 및 실행
$stmt = $conn->prepare("SELECT * FROM shop_data WHERE no = ?");
$stmt->bind_param("i", $ProductID);
$stmt->execute();
$result = $stmt->get_result();
$row = mysqli_fetch_array($result);
$productImg = explode(', ', $row['detail_imgs']);

// JSON으로 데이터를 보냄
echo json_encode([
  'success' => true,
  'no' => $row['no'],
  'cate' => $row['cate'],
  'parent' => $row['parent'],
  'name' => $row['name'],
  'img' => $row['img'],
  'price' => $row['price'],
  'detail' => $row['comment'],
  'detail_imgs' => $productImg
]);



?>