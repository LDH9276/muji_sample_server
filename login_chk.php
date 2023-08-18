<?php

include_once 'dbconn.php';
include_once 'jwt.php';

// JWT 객체 생성
$jwt = new JWT();
$access_secret_key = $jwt->getAccessSecretKey();
$refresh_secret_key = $jwt->getRefreshSecretKey();

// POST 요청으로 받은 데이터
// ID : 아이디
// password : 비밀번호
$id = $_POST['id'];
$password = $_POST['password'];

// SQL 인젝션 방지
$id = mysqli_real_escape_string($conn, $id);
$password = mysqli_real_escape_string($conn, $password);

// DB에서 아이디 검색
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = mysqli_fetch_array($result);
$dbPass = $row['pass'];

if(password_verify($password, $dbPass)) {
  
  // 프로필 사진 이름과 확장자 분리 (ex. profile.jpg -> profile, jpg)
  $profilles = explode('.', $row['profile']);

  // 토큰에 담을 데이터
  $data_token = [
    'id' => $row['id'],
    'name' => $row['name'],
    'profile' => $profilles[0],
    'profile2' => $profilles[1],
    'user_info' => $row['user_info']
  ];

  // 액세스 토큰 발급
  $access_token = $jwt->issueAccessToken($data_token);

  // 리프레시 토큰 발급
  $refresh_token = $jwt->issueRefreshToken($data_token);

  // DB에 토큰 저장
  $stmt = $conn->prepare("INSERT INTO token (user_id, refresh_token) VALUES (?, ?, ?)");
  $stmt->bind_param("ss", $row['id'], $refresh_token);
  $stmt->execute();

  // 쿠키에 토큰 저장
  echo json_encode([
    'success' => true,
    'user_id' => $row['id'],
    'user_name' => $row['name'],
    'user_profile' => $profilles[0] . "." . $profilles[1],
    'user_info' => $row['user_info'],
    'access_token' => $access_token,
    'refresh_token' => $refresh_token
  ]);
} else {
  echo json_encode([
    'success' => false,
    'error' => 'Invalid credentials'
  ]);
}

?>