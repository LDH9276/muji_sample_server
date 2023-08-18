<?php

// CORS 허용
include_once 'cors.php';
header('Access-Control-Allow-Headers: Authorization, Content-Type');

//JWT 토큰 불러오기
include_once 'jwt.php';

//DB 연결
include_once 'dbconn.php';

// JWT 객체 생성
$jwt = new JWT();
$access_secret_key = $jwt->getAccessSecretKey();
$refresh_secret_key = $jwt->getRefreshSecretKey();
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];

// 액세스 토큰 가져오기
$token = substr($authHeader, strpos($authHeader, ' ') + 1);

// 액세스 토큰 해석
$result = $jwt->decodeAccessToken($token);

// 조건 : 액세스 토큰이 유효하다면
if (is_array($result)) {
  // 액세스 토큰이 유효하면 전달
  echo json_encode([
    'success' => true,
    'user_id' => $result['id'],
    'user_name' => $result['name'],
    'user_profile' => $result['profile'] . "." . $result['profile2']
  ]);
} 

// 조건 : 액세스 토큰이 유효하지 않다면 리프레시 토큰 검사
else {

  // 리프레시 토큰 가져오기
  $refresh_token = $_COOKIE['refresh_token'];

  // 리프레시 토큰 해석
  $result = $jwt->decodeRefreshToken($refresh_token);

  // 조건 : 리프레시 토큰 해석이 성공하면
  if (is_array($result)) {

    // 리프레시 토큰이 유효하면 새 액세스 토큰 발급
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // DB에서 리프레시 토큰 검색 (위조된 리프레시 토큰이 아닌지 검사)
    $stmt = $conn->prepare("SELECT * FROM token WHERE user_id = ? AND refresh_token = ?");
    $stmt->bind_param("is", $result['id'], $refresh_token);
    $stmt->execute();
    $result_db = $stmt->get_result();

    // 리프레시 토큰이 DB에도 같은 값이 존재한다면 새 액세스 토큰 발급
    if ($result_db->num_rows > 0) {

      // 새 액세스 토큰 발급
      $data_token = [
        'id' => $result['id'],
        'name' => $result['name'],
        'profile' => $result['profile'],
        'profile2' => $result['profile2'],
        'exp' => time() + 60 * 60 // 1시간
      ];
      $new_access_token = $jwt->issueAccessToken($data_token);
      
      // 새 액세스 토큰 클라이언트에 전달
      echo json_encode([
        'success' => true,
        'user_id' => $result['id'],
        'user_name' => $result['name'],
        'user_profile' => $result['profile'] . "." . $result['profile2'],
        'new_access_token' => $new_access_token
      ]);
    } else {

      // 리프레시 토큰이 DB에서 검색되지 않는다면
      echo json_encode([
        'success' => false,
        'error' => '토큰이 유효하지 않습니다.'
      ]);
    }

    // DB 연결 종료
    $stmt->close();
    $conn->close();
  } else {

    // 리프레시 토큰이 유효하지 않음
    echo json_encode([
      'success' => false,
      'error' => $result
    ]);
  }
}
?>