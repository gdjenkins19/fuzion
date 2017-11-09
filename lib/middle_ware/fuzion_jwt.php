<?PHP 

namespace Middleware;

use \Firebase\JWT\JWT;

class FuzionJwt {

    static protected $secret_key = 'supersecretkeyyoushouldnotcommittogithub';
    protected $decoded_jwt = NULL;

    static public function Build($params) {

        $exp = (isset($params['exp'])) ? $params['exp'] : (60*5);

        $token = array(
            "iss" => "http://fuzion.freemanco.com",
            "jti" => 1,
            "aud" => "user@freemanco.com",
            "iat" => time(),
            "exp" => time() + (60*5),
            "resources" => []
        );

        foreach($params as $k=>$v) {
            $token[$k] = $v;
        }

        //$jwt = JWT::encode($token, self::$secret_key, 'HS256');
        //$decoded_jwt = JWT::decode($jwt, self::$secret_key, array('HS256'));
        $fuzion_jwt = new FuzionJwt($token); //$decoded_jwt);
        return $fuzion_jwt;
    }

    static public function Decode($token_string) {
        $decoded_jwt = JWT::decode($token_string, self::$secret_key, array('HS256'));
        $fuzion_jwt = new FuzionJwt($decoded_jwt);
        return $fuzion_jwt;
    }

    protected function __construct($decoded_jwt) {
        $this->decoded_jwt = $decoded_jwt;
    }

    public function encode() {
        return JWT::encode($this->decoded_jwt, self::$secret_key, 'HS256');
    }

    public function authorized($resource, $level) {
        $resources = (array) ($this->decoded_jwt->resources);

        $all = isset($resources['ALL']) ? $resources['ALL'] : 0;
        $res = isset($resources[$resource]) ? $resources[$resource] : 0;

        return max($all, $res) >= $level;
    }

    public function getId() {
        return $this->decoded_jwt->jti;
    }
}