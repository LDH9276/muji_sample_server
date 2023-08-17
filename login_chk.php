<?php

  // CORS 허용
  include_once 'cors.php';

  // 데이터베이스 연결
  include_once 'dbconn.php';
  include_once 'jwt.php';

  // JWT 토큰 변수지정
  $jwt = new JWT();
  $secret_key = $jwt->getSecretKey();

  // POST로 받아오기
  $id = $_POST['id'];
  $password = $_POST['password'];

  // 특수문자 제거
  $id = mysqli_real_escape_string($conn, $id);
  $password = mysqli_real_escape_string($conn, $password);
  
  //로그인 절차 실행
  $stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = mysqli_fetch_array($result);
  $dbPass = $row['pass'];

  // 비밀번호 확인
  if(password_verify($password, $dbPass)) {

    $profilles = explode('.', $row['profile']);

    // JWT 토큰 생성
    $token = $jwt->hashing(array(
      'exp' => time() + 60 * 60, // 1시간 exp : 1시간
      'id' => $row['id'], // 사용자 아이디 id : ID
      'name' => $row['name'], // 사용자 이름 name : NAME
      // 프로필 부분은 . 으로 되어 있어 base64로 인코딩
      'profile' =>  $profilles[0], // 사용자 프로필 사진이름명
      'profile2' =>  $profilles[1], // 사용자 프로필 사진확장자
      'user_info' => $row['user_info'], // 사용자 정보
    ), JSON_UNESCAPED_UNICODE);

    // 로그인이 성공하면 성공 메시지와 함께 사용자 정보를 반환
    echo json_encode([
      'success' => true, // 성공 여부
      'user_id' => $row['id'], // 사용자 아이디
      'user_name' => $row['name'], // 사용자 이름
      'user_profile' => $profilles[0] . "." . $profilles[1], // 사용자 프로필 사진
      'user_info' => $row['user_info'], // 사용자 정보
      'token' => $token // JWT 토큰
    ]);
  } else {
    // 실패하면 실패 메시지를 반환
    echo json_encode([
      'success' => false,
      'error' => 'Invalid username or password'
    ]);
  }
?>