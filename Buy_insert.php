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

  // 비회원 주문기능 (구현예정)
  if($id == null || $id == ''){

    //랜덤 숫자 5자리로 처리
    $id = rand(00000, 99999);
    
    // 맨 앞자리 값에 따라 맨 앞자리 알파벳으로 변환
    // 값의 예 : A19321, B245431 ...
    if($id < 10000){
      $id = "A" . $id;
    } else if($id < 20000){
      $id = "B" . $id;
    } else if($id < 30000){
      $id = "C" . $id;
    } else if($id < 40000){
      $id = "D" . $id;
    } else if($id < 50000){
      $id = "E" . $id;
    } else if($id < 60000){
      $id = "F" . $id;
    } else if($id < 70000){
      $id = "G" . $id;
    } else if($id < 80000){
      $id = "H" . $id;
    } else if($id < 90000){
      $id = "I" . $id;
    }

    // id값을 문자열로 변환
    $id = (string)$id;

    $sql = "SELECT * FROM `muji_buy_table` WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if($result){
      $row = mysqli_fetch_array($result);

      // 중복되는 id값이면 다시 랜덤 숫자로 재처리
      if($id == $row['id']){
        
        //랜덤 숫자 5자리로 처리
        $id = rand(00000, 99999);
        
        //맨 앞자리 값에 따라 맨 앞자리 알파벳으로 변환
        if($id < 10000){
          $id = "A" . $id;
        } else if($id < 20000){
          $id = "B" . $id;
        } else if($id < 30000){
          $id = "C" . $id;
        } else if($id < 40000){
          $id = "D" . $id;
        } else if($id < 50000){
          $id = "E" . $id;
        } else if($id < 60000){
          $id = "F" . $id;
        } else if($id < 70000){
          $id = "G" . $id;
        } else if($id < 80000){
          $id = "H" . $id;
        } else if($id < 90000){
          $id = "I" . $id;
        }
      } else {
        // 중복되지 않은 id값이면 그대로 사용
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
