<?php
namespace Francis\SublimePhp;

use Francis\SublimePhp\RouteResolve;

class Router extends RouteResolve
{
    public function get(string $path, $handler)
    {
        return self::addHandler($path, "GET", $handler);
    }
    public function post(string $path, $handler)
    {
        return self::addHandler($path, "POST", $handler);
    }
    public function put(string $path, $handler)
    {
        return self::addHandler($path, "PUT", $handler);
    }
    public function delete(string $path, $handler)
    {
        return self::addHandler($path, "DELETE", $handler);
    }
    public function patch(string $path, $handler)
    {
        return self::addHandler($path, "PATCH", $handler);
    }
    public function update(string $path, $handler)
    {
        return self::addHandler($path, "UPDATE", $handler);
    }
    public static function send(int $statusCode){
       return http_response_code($statusCode);
    }

    
}
