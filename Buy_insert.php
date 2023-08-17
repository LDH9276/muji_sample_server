<?php

  // CORS 허용
  include_once 'cors.php';

  // 데이터베이스 연결
  include_once 'dbconn.php';

  // POST로 받아오기
  $id = $_POST['id'] ?? '';
  $cart = $_POST['cart'] ?? '';
  $date = date("Y-m-d H:i:s");
  $cart = json_encode($cart);

  // 비회원 주문기능 (미구현)
  if($id == null || $id == ''){
    //랜덤 숫자 5자리로 처리
    $id = rand(00000, 99999);
    $sql = "SELECT * FROM `muji_buy_table` WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if($result){
      $row = mysqli_fetch_array($result);
      if($id == $row['id']){
        $id = rand(00000, 99999);
      } else {
        $id = $id;
      }
    } else{
      $id = $id;
    }
  }

  // 쿼리문 작성 및 실행
  $stmt = $conn->prepare("INSERT INTO muji_buy_table (userID, product, date) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $id, $cart, $date);
  $result = $stmt->execute();

  if(!$result){
    echo json_encode([
      'success' => false,
      'error' => '구매에 실패했습니다.'
    ]);
  } else {
    echo json_encode([
      'success' => true,
      'error' => '구매에 성공했습니다.'
    ]);
  }

?>
