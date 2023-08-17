<?php

// CORS 허용
include_once 'cors.php';

// 데이터베이스 연결
include 'dbconn.php';

$sql = "SELECT * FROM `shop_data`";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);

// JSON으로 데이터를 보냄
// JSON은 다음과 같이 전송한다. "list" : {"no" ,"name", "img", "price"}
$data = array();

while($row = mysqli_fetch_assoc($result)) {
  $data[] = array(
    'no' => $row['no'],
    'name' => $row['name'],
    'img' => $row['img'],
    'price' => $row['price'],
    'cate' => $row['cate'],
  );
}

// JSON으로 list 묶음으로 전달
echo json_encode(array("list" => $data));

?>