<?php
namespace Cisco\SublimeRequest;
use Cisco\SublimeDB\Db;

class Request extends Db  {
    public  function logUser(array $request){
        $body = $request["body"];
        $user = $this->selectOne("user",["*"],["email"=>$body["email"],"password"=>$body["password"]]);
        var_dump($user);
    }
    public function registerUser(array $request){
        
    }
}