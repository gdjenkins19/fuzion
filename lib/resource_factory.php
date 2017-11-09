<?PHP 

class ResourceFactory {

    static public function Build($resource) {
        $reflector = new ReflectionClass($resource);
        $paramObjects = [];

        $constructor = $reflector->getConstructor();

        if (isset($constructor)) {
            $parameters = $constructor->getParameters();
            if (count($parameters) > 0) {
                foreach($parameters as $param) {
                    if(!$param->isOptional()) {
                        $paramObjects[] = self::Build($param->getClass()->name);
                    }
                }
            }
        }

        $obj = $reflector->newInstanceArgs($paramObjects);

        return $obj;
    }
}