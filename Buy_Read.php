<?php

  // CORS 허용
  include_once 'cors.php';

  // 데이터베이스 연결
  include_once 'dbconn.php';

  // POST로 받아오기
  $id = $_POST['id'] ?? '';

  // 쿼리문 작성 및 실행
  $sql = "SELECT * FROM muji_buy_table WHERE userID = '$id'";
  $result = mysqli_query($conn, $sql);


  $data = array();
  while($row = mysqli_fetch_array($result)) {

    $data[] = array(
      'orderID' => $row['orderID'],
      'id' => $row['userID'],
      'product' => json_decode($row['product'], true),
      'date' => $row['date'],
    );
  }

  // JSON으로 데이터를 보냄
  echo json_encode(
    array("list" => $data)
  );

?>
