<?PHP 

require 'vendor/autoload.php';

$app = new App();
//$app->add(new Middleware\DefaultAuthorizationHeader());
//$app->add(new MiddleWare\RequireJwt());
//$app->add(new MiddleWare\RedisJwt());
$app->run();
