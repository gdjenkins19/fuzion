<?PHP 

class Response extends Exception {

  public function __construct($code,$message=NULL,$data=NULL) {
    $this->code($code);
    $this->message($message);
    $this->data($data);
    parent::__construct();
  }

  protected function code($code) {
    $this->body['code'] = $code;
    $this->body['status'] = self::$status[$code];
    return $this;
  }

  public function data($data) {
    $this->body['data'] = $data;
    return $this;
  }

  public function message($message) {
    $this->body['message'] = $message;
    return $this;
  }

  public function respond() {
    $now = new \DateTime();
    $this->body['datetime'] = $now->format('Y-m-d H:i:s');
    $this->body['timestamp'] = $now->getTimestamp();

    foreach(array_keys($this->body) as $field) {
      if ($this->body[$field] === NULL) unset($this->body[$field]);
    }

    http_response_code($this->body['code']);
    header('Content-Type: application/json');
    echo(json_encode($this->body));           
  }

  public function getValue($field) {
    return $this->body[$field];
  }

  private $body = [
    "program" => 'Fuzion',
    "version" => '0.0.1',
    "release" => '1',
    "datetime" => NULL,
    "timestamp" => NULL,
    "code" => NULL,
    "status" => NULL,
    "message" => NULL,
    "data" => NULL  
  ];

  private static $status = [
    200 => 'Ok',
    201 => 'Created',
    304 => 'Not Modified',
    400 => 'Bad Request',
    401 => 'Not Authorized',
    403 => 'Forbidden',
    404 => 'Page/Resource Not Found',
    405 => 'Method Not Allowed',
    415 => 'Unsupported Media Type',
    500 => 'Internal Server Error'
  ];

}
