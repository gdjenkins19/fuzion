<?PHP 

require 'vendor/autoload.php';

$app = new App();
//$app->add(new Middleware\DefaultAuthorizationHeader());
$app->add(new Middleware\RequireJwt());
$app->add(new Middleware\RedisJwt());

$app->run();
