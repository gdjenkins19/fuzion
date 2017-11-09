<?PHP 

namespace Models;

abstract class ModelMap {

    abstract public function apiName();
    abstract public function dbName();

    protected $columns = NULL;
    protected $api_to_db = NULL;
    protected $db_to_api = NULL;
    protected $hiddens = NULL;
    protected $autos = NULL;

    public function convertApiToDb($args) {

        foreach(array_keys($args) as $a) {
            $d = $this->api_to_db[$a];
            if(isset($this->columns[$d])) $args[$d] = $args[$a];
            unset($args[$a]);
        }

        return $args;
    }

    public function convertDbToApi($args) {
        
        foreach(array_keys($args) as $d) {
            $a = $this->db_to_api[$d];
            $args[$a] = $args[$d];
            unset($args[$d]);
        }

        return $args;
    }

    public function hide($args) {
        foreach($this->hiddens as $hide) {
            unset($args[$hide]);
        }

        return $args;
    }

    public function auto($args) {
        foreach($this->autos as $auto) {
            $args[$auto] = $this->$auto();
        }

        return $args;
    }
    
}