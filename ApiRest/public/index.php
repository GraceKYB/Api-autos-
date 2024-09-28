<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require '../vendor/autoload.php';
require '../src/config/db.php';

$config = ['settings' => ['displayErrorDetails' => true]];
$app = new \Slim\App($config);


require '../src/rutas/autos.php';
require '../src/rutas/clientes.php';
// Run app
$app->run();

?>