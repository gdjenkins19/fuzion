<?PHP 

class Router {

    protected $routes = NULL;

    public function __construct($route_file=NULL) {
        $this->routes = ($route_file === NULL) ? [] : include $route_file;
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function addRoute($request_method, $path, $class, $method) {
        $full_path = "$path/$request_method/$class:$method";
        $segments = array_filter(explode('/',$full_path),function($s){return $s !== '';});
        $this->routes = $this->addSegments($this->routes,$segments);
    }

    //returns [$class,$method,$args] $args = []
    public function getResource($request_method,$path) {
        $full_path = "$path/$request_method";
        $segments = array_filter(explode('/',$full_path),function($s){return $s !== '';});
        return $this->getResourceFromSegments($segments);
    }

    protected function addSegments($routes,$segments) {
        $segment = array_shift($segments);
        if(count($segments) > 0) {
            if(!isset($routes[$segment])) $routes[$segment] = [];
            $routes[$segment] = $this->addSegments($routes[$segment], $segments);
            return $routes;
        } else {
            return $segment;
        }
    }

    protected function getResourceFromSegments($url_segments) {
        $finder = $this->routes;
        $args = [];

        foreach($url_segments as $segment) {
            if ($finder !== NULL) {
                if (isset($finder[$segment])) {
                    $finder = $finder[$segment];
                } else {
                    $finder = $this->lookForRegexes($segment, $finder, $args);
                }
            }
        }

        if($finder === NULL) {
            throw new Response(500, "Router: Unknown resource",$url_segments);
        }

        $finder = explode(':',$finder);
        array_push($finder,$args);

        return $finder;
    }

    protected function lookForRegexes($segment, $finder, &$args) {
        if(is_array($finder)) {
            foreach(array_keys($finder) as $key) {
                if(preg_match('/^([A-Za-z]+):(.+)$/', $key, $matches)) {
                    $var = $matches[1];
                    $regex = '/^'.$matches[2].'$/';
                    if(preg_match($regex, $segment)) {
                        $args[$var] = $segment;
                        return $finder[$key];
                    }
                }
            }
        }

        return NULL;
    }
        
}