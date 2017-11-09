<?PHP 

namespace Controllers;

class MODEL_NAME extends Controller {

    use DefaultGet;
    use DefaultPut;
    use DefaultPost;
    use DefaultDelete;

    public static $resource = 'RESOURCE_NAME';
    
    public function __construct(\Models\MODEL_NAME $model) {
        parent::__construct($model);
    }

    public function _resource() {
        return $resource;
    }

}