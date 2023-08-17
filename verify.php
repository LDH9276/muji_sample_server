<?php

// CORS 허용
include_once 'cors.php';
header('Access-Control-Allow-Headers: Authorization, Content-Type');

//JWT 토큰 불러오기
include_once 'jwt.php';

//DB 연결
include_once 'dbconn.php';

$jwt = new JWT();
$secret_key = $jwt->getSecretKey();

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$token = substr($authHeader, strpos($authHeader, ' ') + 1);
$result = $jwt->dehashing($token);

if (is_array($result)) {

// JWT 토큰이 유효하면 전달
echo json_encode([
  'success' => true,
  'user_id' => $result['id'],
  'user_name' => $result['name'],
  'user_profile' => $result['profile'] . "." . $result['profile2']
]);

} else {
  // JWT 토큰이 유효하지 않음
  echo json_encode([
    'success' => false,
    'error' => $result
  ]);
}

?>