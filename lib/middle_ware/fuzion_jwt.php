<?PHP 

namespace Middleware;

class FuzionJwt {

    protected $secret_key = 'THE_SECRET_KEY';
    protected $decoded_jwt = NULL;

    static public function Encode($params) {

    }

    static public function Decode($token_string) {
        $jwt = JWT::decode($token_string, 'THE_SECRET_KEY', array('HS256'));
        $fuzion_jwt = new FuzionJwt($jwt);
    }

    protected function __construct($decoded_jwt) {
        $this->decoded_jwt = $decoded_jwt;
    }

    public function authorize($resource, $level) {
        $resources = (array) ($this->decoded_jwt->resources);

        $all = isset($resources['ALL']) ? $resources['ALL'] : 0;
        $res = isset($resources[$resource]) ? $resources[$resource] : 0;

        return max($all, $res) >= $level;
    }
}