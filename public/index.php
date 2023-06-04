<?php 
require_once "../vendor/autoload.php";
use Francis\SublimePhp\Router;
$router = new Router();

$router->get("/user/:id:", function ($params) {
    var_dump($params);
});
$router->post("/user/:id:", function ($params) {
    var_dump($params);
});
$router->put("/user/:id:", function ($params) {
    var_dump($params["params"]);
});

$router->get("/", function ($params) {
    Router::view("index",$params);
});



$router->run();