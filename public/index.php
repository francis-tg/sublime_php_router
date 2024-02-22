<?php 
require_once "../vendor/autoload.php";
use Cisco\SublimeDB\Db;
use Cisco\SublimeRequest\Request;
use Francis\SublimePhp\Cors;
use Francis\SublimePhp\Engine\View;
use Francis\SublimePhp\Router;
$router = new Router();

new Db();
new Cors();

$router->get("/", function ($params) {
    View::view("index");
});
$middleware = function ($req) {
    var_dump($req);
};
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
},[$middleware]);
$router->post("/register", function ($request) {
    $req = new Request();
    return $req->logUser($request);
});
View::clearCache();

$router->run();