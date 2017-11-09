<?PHP 

class ResourceFactory {

    static public function Build($resource) {
        $obj = NULL;
        $class = new ReflectionClass($resource);

        $paramObjects = NULL;

        if ($class->hasMethod('__construct')) {
            $constructor = new ReflectionMethod($resource, '__construct');
            $parameters = $constructor->getParameters();
            if (count($parameters) > 0) {
                $paramObjects = [];
                foreach($parameters as $parameter) {
                    $paramObjects[] = self::Build($parameter);
                }
            }
        }

        $obj = new $resource($paramObjects);

        return $obj;
    }
}