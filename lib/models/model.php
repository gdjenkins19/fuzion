<?PHP 

namespace Models;

abstract class Model {

    abstract public function name();
    abstract public function tableName();
    
    protected $database = NULL;
    protected $map = NULL;

    public function __construct(\Databases\Database $database, \Models\ModelMap $map) {
        $this->database = $database;
        $this->map = $map;
    }

    public function create($args) {
        $args = $this->map->convertApiToDb($args);
        $args = $this->map->hide($args);
        $args = $this->map->auto($args);

        $result = $this->database->insert($this->tableName(), $args);
        return $result;
    }

    public function read($args) {
        $args = $this->map->convertApiToDb($args);
        $args = $this->map->hide($args);

        $results = $this->database->select($this->tableName(), $args);
        return $this->convertResults($results);
    }

    public function update($args) {
        $args = $this->map->convertApiToDb($args);
        $args = $this->map->hide($args);
        $args = $this->map->auto($args);

        $result = $this->database->update($this->tableName(), $args);
        return $result;
    }

    public function delete($args) {
        $args = $this->map->convertApiToDb($args);
        $args = $this->map->hide($args);

        $result = $this->database->delete($this->tableName(), $args);
        return $result;
    }

    protected function convertResults($results) {
        $alt_results = [];
        foreach($results as $result) {
            $result = $this->map->convertDbToApi($result);
            $result = $this->map->hide($result);
            $alt_results[] = $result;
        }
        return $alt_results;
    }

}