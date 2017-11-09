<?PHP 

use Databases\Database;
use Middleware\FuzionJwt;

require 'vendor/autoload.php';

class Builder {

    public static function BuildTestJwts() {
        file_put_contents("jwt_write.txt",      FuzionJwt::Build(['jti'=>2,'resources'=>['ALL'=>2]])->encode() );
        file_put_contents("jwt_read.txt",       FuzionJwt::Build(['jti'=>3,'resources'=>['ALL'=>1]])->encode() );
        file_put_contents("jwt_one.txt",        FuzionJwt::Build(['jti'=>4,'resources'=>['ResourceOne'=>2]])->encode() );
        file_put_contents("jwt_expired.txt",    FuzionJwt::Build(['jti'=>5,'resources'=>['ALL'=>2],'exp'=>0])->encode() );
        file_put_contents("jwt_blacklist.txt",  FuzionJwt::Build(['jti'=>0,'resources'=>['ALL'=>2]])->encode() );
    }

    public static function BuildDatabase() {
        $database = new Database('build');
        return $database;
    }

    public static function BuildModels() {
        $db = new Database('default');
        foreach($db->getTables() as $table_name) {
            self::BuildModel($db, $table_name);
        }
    }

    public static function BuildControllers() {
        foreach(self::GetModels() as $model) {
            self::BuildController($model);
        }
    }

    public static function BuildRoutes () {
        $routes = [];
        foreach(self::GetControllers() as $controller) {
            self::BuildRoute($controller, $routes);
        }
        self::OutputRoutes($routes);
    }

    protected static function BuildRoute($controller, &$routes) {
        $r = new ReflectionClass($controller);
        $resource = $r->getStaticPropertyValue('resource');
        $methods = array_filter($r->getMethods(ReflectionMethod::IS_PUBLIC),function($m){return strpos($m->name,'_') !== 0;});

        $routes[$resource] = [];

        //No URL Parameters GET, POST, Methods
        foreach($methods as $r_method) {
            $method = $r_method->name;
            switch ($method) {
                case 'PUT':
                case 'DELETE':
                    break;
                case 'GET':
                case 'POST':
                    $routes[$resource][$method] = "$controller:$method";
                    break;
                default:
                    $routes[$resource][$method] = ['POST' => "$controller:$method"];
                    break;
            }
        }
 
        //URL Parameters GET PUT DEL
        $routes[$resource]['id:[0-9]+'] = [];
        
        foreach($methods as $r_method) {
            $method = $r_method->name;
            switch ($method) {
                case 'GET':
                case 'PUT':
                case 'DELETE':
                    $routes[$resource]['id:[0-9]+'][$method] = "$controller:$method";
                    break;
                default:
            }
        }  
    }

    protected static function OutputRoutes($routes) {
        
        $input = fopen("lib/routes_file.php", 'r');
        $lines = [];
        while (($line = fgets($input)) !== false) {
            $lines[] = $line;
        }
        fclose($input);

        $output = fopen("lib/routes_file.php", 'w');

        $state = 0;
        foreach($lines as $line) {
            switch ($state) {
                case 0:
                    fputs($output, $line);
                    if(strpos($line,'//BEGIN_AUTO') !== FALSE) {
                        self::IterateAndOuputRoutes($routes,$output,$tabs="\t");
                        $state = 1;
                    }
                    break;
                case 1:
                    if(strpos($line,'//END_AUTO') !== FALSE) {
                        $state = 0;
                        fputs($output, $line);
                    }    
                    break;
            }
        }

        fclose($output); 
    }

    protected static function IterateAndOuputRoutes($routes,$out,$tabs) {
        if (count($routes) > 0) {
            $keys = array_keys($routes);
            $last = end($keys);
            reset($keys);

            foreach($keys as $key) {
                $comma = $last === $key ? '' : ',';
                if(is_string($routes[$key])) {
                    $val = $routes[$key];
                    fputs($out,"$tabs'$key' => '$val'$comma".PHP_EOL);
                } else {
                    fputs($out,"$tabs'$key' => [".PHP_EOL);
                    self::IterateAndOuputRoutes($routes[$key],$out,$tabs."\t");
                    fputs($out,"$tabs]$comma".PHP_EOL);
                }
            }          
        } 
    }

    protected static function GetControllers() {
        $controllers = array_keys(require __DIR__ . '/../vendor/composer/autoload_classmap.php');
        $controllers = array_map(function($class){return '\\'.$class;},$controllers);
        $controllers = array_filter($controllers,function($c){return strpos($c,'\\Controllers') === 0;});

        $controllers = array_filter($controllers,function($c){
            $reflect = new \ReflectionClass($c);
            return
                method_exists($c,'_resource') && 
                !$reflect->isAbstract();
        });

        return $controllers;
    }

    public static function BuildController($model) {
        $model_name = str_replace('\\Models\\','',$model);
        $resource_name = $model_name;
        $file_name = self::snakify($model_name);
        
        $in = file_get_contents("templates/controller.php");
        $in = str_replace('MODEL_NAME', $model_name, $in);
        $in = str_replace('RESOURCE_NAME', $resource_name, $in);
        $path = 'lib/controllers/auto';
        if (!is_dir($path)) mkdir($path);
        
        file_put_contents("$path/$file_name.php", $in);
    }

    protected static function GetModels() {
        $models = array_keys(require __DIR__ . '/../vendor/composer/autoload_classmap.php');
        $models = array_map(function($class){return '\\'.$class;},$models);
        $models = array_filter($models,function($m){
            return (strpos($m,'\\Models') === 0) && ($m !== '\\Models\\Model') && !preg_match('/Map$/',$m);
        });

        return $models;
    }

    protected static function BuildModel($db, $table_name) {
        $database_name = $db->name();
        $database_namespace = self::camelize($database_name,TRUE);
        $model_name = self::camelize($table_name,TRUE);

        $columns = [];
        $a_to_d = [];
        $d_to_a = [];
        $hidden = [];
        $auto = [];
        foreach ($db->getTableColumns($table_name) as $column) {
            $name = $column['Field'];
            $camelized = self::camelize($name,TRUE);
            unset($column['Field']);
            $columns[$name] = $column;
            $a_to_d[$camelized] = $name;
            $d_to_a[$name] = $camelized;
        }

        $path = 'lib/models/auto';
        if (!is_dir($path)) mkdir($path);

        //MODEL
        $in = file_get_contents("templates/model.php");
        $in = str_replace('DATABASE_NAME', $database_name, $in);
        $in = str_replace('MODEL_NAME', $model_name, $in);
        $in = str_replace('TABLE_NAME', $table_name, $in);
        file_put_contents("$path/$table_name.php", $in);

        //MODEL MAP
        $in = file_get_contents("templates/model_map.php");
        $in = str_replace('MODEL_NAME', $model_name, $in);
        $in = str_replace('TABLE_NAME', $table_name, $in);
        $in = str_replace('DATABASE_COLUMNS', self::compact(var_export($columns,TRUE)), $in);
        $in = str_replace('DATABASE_ATOD', self::compact(var_export($a_to_d,TRUE)), $in);
        $in = str_replace('DATABASE_DTOA', self::compact(var_export($d_to_a,TRUE)), $in);
        $in = str_replace('DATABASE_HIDDEN', self::compact(var_export($hidden,TRUE)), $in);
        $in = str_replace('DATABASE_AUTO', self::compact(var_export($auto,TRUE)), $in);
        file_put_contents("$path/$table_name".'_map.php', $in);
    }

    protected static function snakify($str) {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }
     
    protected static function camelize($str, $capitalise_first_char = false) {
        if($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    protected static function compact($in) {
        return preg_replace("/\s+/", "", $in);
    }

    protected static function delete_directory($dirname) {
        if (is_dir($dirname))
          $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                        unlink($dirname."/".$file);
                else
                        delete_directory($dirname.'/'.$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

}
