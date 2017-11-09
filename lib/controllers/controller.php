<?PHP 

namespace Controllers;

abstract class Controller {

    abstract public function _resource(); // 'ResourceName'

    protected $model = NULL;

    public function __construct(\Models\Model $model) {
        $this->model = $model;
    }

}