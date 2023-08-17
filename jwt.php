<?php
class JWT {
    protected $alg;
    protected $secret_key;

    // 생성자
    function __construct() {
        //사용할 알고리즘
        $this->alg = 'sha256';
        // 비밀 키
        $this->secret_key = getenv('JWT_key');
    }

    // 비밀 키 가져오기
    public function getSecretKey() {
      return $this->secret_key;
    }

    // jwt 발급하기
    function hashing(array $data_token): string {
        // 헤더 - 사용할 알고리즘과 타입 명시
        $header = json_encode(array('alg' => $this->alg, 'typ' => 'JWT'));
        // 페이로드 - 전달할 데이터
        $payload = json_encode($data_token, JSON_UNESCAPED_UNICODE);
        // 시그니처
        $signature = hash($this->alg, $header . $payload . $this->secret_key);
        return base64_encode($header . '.' . $payload . '.' . $signature);
    }

    // jwt 해석하기
    function dehashing($token) {
        // 구분자 . 로 토큰 나누기
        $parted = explode('.', base64_decode($token));
        $signature = $parted[2] ?? null;

        // 토큰 만들 때처럼 시그니처 생성 후 비교
        if (hash($this->alg, $parted[0] . $parted[1] . $this->secret_key) != $signature) {
            return "시그니쳐 오류 시그니처는 $signature 입니다.";
        }
        // 만료 검사
        $payload = json_decode($parted[1], true);
        if ($payload['exp'] < time()) {
            return "만료 오류";
        }
        
        return json_decode($parted[1], true);
    }
}


?>