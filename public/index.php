<?php 
require_once "../vendor/autoload.php";

use Cisco\Sublime\middlewares\Validation;
use Cisco\SublimeDB\Db;
use Cisco\SublimeRequest\Request;
use Francis\SublimePhp\Cors;
use Francis\SublimePhp\Engine\View;
use Francis\SublimePhp\Router;
$router = new Router();

new Db();
new Cors();
$validation_middleware = new Validation();

$router->get("/", function ($params) {
    View::view("index");
});

$router->post("/user/:id:", function ($params) {
    var_dump($params);
});
$router->put("/user/:id:", function ($params) {
    var_dump($params["params"]);
});

$router->get("/login", function ($params) {
    Router::view("login");
});

$router->post("/login", function ($request) {
    $req = new Request();
    return $req->logUser($request);
});

$router->get("/register", function ($request) {
    View::view("register");
},[$validation_middleware,"validateField"]);
$router->post("/register", function ($request) {
    $req = new Request();
    return $req->logUser($request);
});
View::clearCache();

$router->run();