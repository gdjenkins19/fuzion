<?PHP 

namespace Databases;

class Database {

    protected $db_name = NULL;
    protected $config = NULL;
    protected $pdo = NULL;

    public function __construct($type=NULL) {  
        $this->config = $this->buildConfig($type);
        $this->db_name = $this->config['dbname'];
        $this->pdo = $this->buildPdo($this->config);
    }

    public function name() {
        return $this->db_name;
    }
    
    public function delete($table, $args) {
        if(!isset($args['id'])) return NULL;
        $id_args = ['id'=>$args['id']];
        
        $where = $this->where($id_args);
        $sql = "DELETE FROM `$table`$where;";
        $statement = $this->prepare_statement($sql,$id_args);
        return $statement ? $statement->rowCount() : NULL;                    
    }
            
    public function select($table, $args=[]) {
        $where = $this->where($args);
        $sql = "SELECT * FROM `$table`$where;";

        $statement = $this->prepare_statement($sql,$args);
        return $statement ? $statement->fetchAll(\PDO::FETCH_ASSOC) : NULL; 
    }

    public function insert($table, $args) {
        $fields = $this->getInsertLists($args);
        $sql = "INSERT INTO `$table` ( $fields[0] ) VALUES ( $fields[1] );";
        $statement = $this->prepare_statement($sql,$args);
        return $statement ? $this->pdo->lastInsertId() : NULL; 
    }

    public function update($table, $args) {
        if(!isset($args['id'])) return NULL;
        $id = $args['id'];
        unset($args['id']);

        $updates = $this->getUpdateList($args);
        $where = $this->where(['id'=>$id]);
        $sql = "UPDATE `$table` SET $updates$where";
        
        $args['id'] = $id;
        
        $statement = $this->prepare_statement($sql,$args);
        return $statement ? $statement->rowCount() : NULL;                    
    }

    public function getTableColumns($table) {
        $sql = "SHOW COLUMNS FROM `$table`;";
        $query = $this->pdo->query($sql);
        $columns = $query->fetchAll();//\PDO::FETCH_COLUMN);
        return $columns;
    }

    public function getTables() {
        $sql = 'SHOW TABLES;';
        $query = $this->pdo->query($sql);
        $tables = $query->fetchAll(\PDO::FETCH_COLUMN);
        return $tables;
    }

    public function query($sql,$type=0) {
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $data = ($type === 0) ? $statement->fetchAll(\PDO::FETCH_ASSOC) : ['id'=>$this->pdo->lastInsertId()];
        return $data;
    }

    // $sql contains a :argName for each $args key
    protected function prepare_statement($sql, $args) {
        $statement = $this->pdo->prepare($sql);

        foreach($args as $k=>$v) {
            $k = ":$k";
            $statement->bindValue($k,$v);
        }

        $res = $statement->execute();
        return $res ? $statement : FALSE;
    }

    protected function getUpdateList($args) {
        $ul = '';
        $sep = '';
        foreach($args as $k=>$v) {
            $ul .= ($sep."`$k` = :$k");
            $sep = ", ";
        }
        return $ul;
    }

    protected function where($arry=NULL) {
        $clause = '';
        if($arry !== NULL) {
            $sep = ' WHERE ';
            foreach($arry as $k=>$v) {
                $clause .= ($sep."`$k` = :$k"); //:$k is :varName for prepared statement
                $sep = ' AND '; 
            }    
        }
        return $clause;
    }

    protected function getInsertLists($args) {
        $vars = '';
        $vals = '';
        $sep = '';
        foreach ($args as $k=>$v) {
            $vars .= ($sep."`$k`");
            $vals .= ($sep.":$k");
            $sep = ', ';
        }

        return [$vars,$vals];
    }

    protected function buildConfig($type=NULL) {
        if ($type === NULL) $type = 'default';
        $file_configs = json_decode(file_get_contents("lib/database/database.json"),TRUE);
        return $file_configs[$type];
    }

    protected function buildPdo($config) {

        if(isset($config['generate'])) {
            $this->generateDb($config);
        }

        extract($config); // $dbname $host $user $password $generate
        
        $charset = 'utf8';
        $opt = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $pdo = new \PDO($dsn, $user, $password, $opt);

        return $pdo;   
    }

    protected function generateDb($config) {
        extract($config); // $dbname $host $user $password $generate
        
        $pdo = new \PDO("mysql:host=$host", $user, $password);
        
        $results = $pdo->exec( "DROP DATABASE IF EXISTS $dbname;" );
        $results = $pdo->exec( "CREATE DATABASE $dbname;" );

        foreach($generate as $sql_file) {
            $sql = file_get_contents($sql_file);
            $sql = str_replace('DATABASE_NAME',$dbname,$sql);
            $result = $pdo->exec( $sql );
        }
    }

}
