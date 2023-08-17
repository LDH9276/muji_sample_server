<?php

session_start();

// 세션에 저장된 사용자 정보를 삭제
unset($_SESSION['id']);

?>