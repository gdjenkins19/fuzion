<?PHP 

namespace Controllers;

abstract class Controller {

    abstract public function _resource(); // 'ResourceName'

    protected $model = NULL;

    public function __construct(\Models\Model $model) {
        $this->model = $model;
    }

    public function authorize($request, $level) {
        $jwt = $request->getAttribute('jwt');

        if (! $jwt->authorized($this->_resource(),$level)) throw new \Response(401,'Insufficient Permissions');        
    }
}