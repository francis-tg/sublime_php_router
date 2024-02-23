<?php
namespace Cisco\SublimeRequest;
use Cisco\SublimeDB\Db;
use Francis\SublimePhp\Router;

class Request extends Db  {
    public  function logUser(array $request){
        $body = $request["body"];
        $user = $this->selectOne("user",["*"],[["email"=>$body["email"]],["password"=>$body["password"]]]);
        var_dump($user);
    }
    public function registerUser(array $request){
        $body = $request["body"];
        $this->insert("user", $body);
        Router::redirect("/");
    }
}