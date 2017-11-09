<?PHP 

namespace Models;

class MODEL_NAME extends Model {

    public function __construct(\Databases\Database $database, \Models\MODEL_NAMEMap $map) {
        parent::__construct($database, $map);
    }

    public function name() {
        return 'MODEL_NAME';
    }

    public function tableName() {
        return 'TABLE_NAME';
    }
}