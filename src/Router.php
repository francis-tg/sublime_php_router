<?php
namespace Francis\SublimePhp;

use Francis\SublimePhp\Engine\View;
class Router extends View
{
    public function get(string $path, $handler)
    {
        return self::addHandler($path, "GET", $handler);
    }
    public function post(string $path, $handler)
    {
        return self::addHandler($path, "POST", $handler);
    }
    /**
     * Summary of put
     * @param string $path
     * @param mixed $handler
     * @return array
     */
    public function put(string $path, $handler)
    {
        return self::addHandler($path, "PUT", $handler);
    }
    /**
     * Summary of delete
     * @param string $path
     * @param mixed $handler
     * @return array
     */
    public function delete(string $path, $handler)
    {
        return self::addHandler($path, "DELETE", $handler);
    }
    /**
     * Summary of patch
     * @param string $path
     * @param mixed $handler
     * @return array
     */
    public function patch(string $path, $handler)
    {
        return self::addHandler($path, "PATCH", $handler);
    }
    /**
     * Summary of update
     * @param string $path
     * @param mixed $handler
     * @return array
     */
    public function update(string $path, $handler)
    {
        return self::addHandler($path, "UPDATE", $handler);
    }
    /**
     * Summary of send
     * @param int $statusCode
     * @return bool|int
     */
    public static function send(int $statusCode){
       return http_response_code($statusCode);
    }

    
}
