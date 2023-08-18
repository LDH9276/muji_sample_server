<?php

// 리액트는 세션기능이 없어 JWT로 대체
session_start();

// 세션에 저장된 사용자 정보를 삭제
unset($_SESSION['id']);

// 수정했습니다!
// test

?>