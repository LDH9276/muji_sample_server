<?php

  // CORS 허용
  include_once 'cors.php';

  // 데이터베이스 연결
  include_once 'dbconn.php';

  // POST로 받아오기
  $id = $_POST['id'] ?? '';
  $password = $_POST['password'] ?? '';
  $name = $_POST['name'] ?? '';
  $email = $_POST['email'] ?? '';
  
  // 프로필 파일 받아오기
  if(isset($_FILES['profile'])) {
    $file_name = $_FILES['profile']['name']; //파일명
    $file_size = $_FILES['profile']['size']; //파일크기
    $file_tmp = $_FILES['profile']['tmp_name']; //파일명
    $file_type = $_FILES['profile']['type']; //파일유형
  
    $ext = explode('.',$file_name); 
    $ext = strtolower(array_pop($ext));
    //file.hwp -> [file] [hwp]
  
    $expensions = array("jpeg", "jpg", "png", "pdf", "hwp", "docx", "pptx", "ppt", "txt", null); //올라갈 파일 지정
    //SWF나 EXE같은 악성코드 배포방지
    
    if(in_array($ext, $expensions) === false){ //해당 확장자가 아니라면
      $errors[] = "올바른 확장자가 아닙니다.";
    } //경고
  
    if($file_size > 2097152) { //2MB이상 올라가면
      $errors[] = '파일 사이즈는 2MB 이상 초과할 수 없습니다.';
    } //경고
  
    if(empty($errors) == true) { //에러가 없다면
      move_uploaded_file($file_tmp, "./files/".$file_name); //경로에 저장
      $files = $file_name; // 변수에 파일명을 담는다
    } else { //경고가 있다면
      print_r($errors); //경고출력
    }
  } else { // 만약 이미지 업로드가 아니라면
    $files = null; //null로 반환한다.
  }

  // 특수문자 제거
  $id = mysqli_real_escape_string($conn, $id);
  $password = mysqli_real_escape_string($conn, $password);
  $name = mysqli_real_escape_string($conn, $name);
  $email = mysqli_real_escape_string($conn, $email);



  // 아이디 중복검사
  $sql = "SELECT * FROM members WHERE id = '$id'";
  $result = mysqli_query($conn, $sql);


  // 아이디가 중복되면 에러메시지를 보냄
  if(mysqli_num_rows($result) > 0) {
    echo json_encode([
      'idChk' => false,
    ]);
    exit;
  }

  // 비밀번호 암호화
  $password = password_hash($password, PASSWORD_DEFAULT);
  
  // 회원가입 절차 실행
  $stmt = $conn->prepare("INSERT INTO members (id, pass, name, email, profile) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $id, $password, $name, $email, $files);
  $result = $stmt->execute();
  
  if($result) {
    // 회원가입 이후 JSON으로 성공 메시지를 보냄
    echo json_encode([
      'success' => true
    ]);
  } else {
    // 실패하면 JSON으로 실패 메시지를 보냄
    echo json_encode([
      'success' => false,
      'error' => 'Invalid username or password'
    ]);
  }
?>