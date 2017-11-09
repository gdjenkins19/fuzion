<?PHP 

class App {

    protected $middle_wares = [];
    protected $routes_file = 'lib/routes_file.php';

    public function add($middle_ware) {
        $this->middle_wares[] = $middle_ware;
    }

    public function setRoutes($routes_file) {
        $this->routes_file = $routes_file;
    }

    public function run() {
        $response = NULL;

        try {

            $request = new Request();

            $this->runMiddleware($request);

            $router = new Router($this->routes_file);
            $resource = $router->getResource($request->getMethod(), $request->getUrl());
                
            list($controller_class, $method, $args) = $resource;
            
            $controller = ResourceFactory::Build($controller_class);

            $controller->$method($request, $args);

        } catch (Response $r) {
            $response = $r;
        } catch (Exception $e) {
            $response = new Response(500,'Unknown Error: '.$e->getMessage());
        }

        $response->respond();
    }

    protected function runMiddleware(&$request) {
        foreach($this->middle_wares as $middle_ware) {
            $middle_ware($request);
        }
    }

}