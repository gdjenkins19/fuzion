<?PHP 

class Request {

    protected $method = NULL;
    protected $url = NULL;
    protected $params = NULL;
    protected $auth_header = NULL;
    protected $attributes = [];

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->url = $this->createUrl();
        $this->params = $this->createParams();
        $this->auth_header = $_SERVER['HTTP_AUTHORIZATION'];
    }

    public function getAttribute($key) {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : NULL;
    }

    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
    } 

    public function getUrl() {
        return $this->url;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getParams($args=[]) {
        foreach($this->params as $k=>$v) {
            if(!isset($args[$k])) $args[$k] = $v;
        }
        return $args;
    }

    public function getAuthorizationHeader() {
        return $this->auth_header;
    }

    public function setDefaultAuthorizationHeader($header) {
        if (!isset($this->auth_header)) {
            $this->auth_header = $header;
        }
    }

    protected function createParams() {
        $params = [];

        switch ($this->method) {
            case 'GET':
            case 'DELETE':
                $params = $_GET;
                break;
            case 'PUT':
                parse_str(file_get_contents('php://input'),$params);
                break;
            case 'POST':
                $params = $_POST;
                break;
        }

        return $params;
    }

    protected function createUrl() {
        $url = $_SERVER['REDIRECT_URL'];
        $removes = explode('/',$_SERVER['SCRIPT_FILENAME']);

        foreach($removes as $remove) {
            if($remove !== '') {
                $url = str_replace('/'.$remove,'',$url);
            }
        }

        if($url === '/') {
            $url = '/Routes';
        }
        
        return $url;
    }
}