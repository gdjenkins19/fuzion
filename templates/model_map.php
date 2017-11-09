<?PHP 

namespace Models;

class MODEL_NAMEMap extends ModelMap {

    public function apiName() {
        return 'MODEL_NAME';
    }

    public function dbName() {
        return 'TABLE_NAME';
    }

    protected $columns = DATABASE_COLUMNS;
    protected $api_to_db = DATABASE_ATOD;
    protected $db_to_api = DATABASE_DTOA;
    protected $hiddens = DATABASE_HIDDEN;
    protected $autos = DATABASE_AUTO;   
}