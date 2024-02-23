<?php
namespace Francis\SublimePhp;

use Francis\SublimePhp\Engine\View;
class Router extends View
{
    protected function defaultMiddleware() {
        print("I'm default middleware");
    }

    public function get(string $path, ?callable $handler = null, ?array $middlewares = null)
    {
        $this->addHandlerWithOptionalMiddleware($path, "GET", $handler, $middlewares);
    }

    public function post(string $path, ?callable $handler = null, ?array $middlewares = null)
    {
        $this->addHandlerWithOptionalMiddleware($path, "POST", $handler, $middlewares);
    }

    public function put(string $path, ?callable $handler = null, ?array $middlewares = null)
    {
        $this->addHandlerWithOptionalMiddleware($path, "PUT", $handler, $middlewares);
    }

    public function delete(string $path, ?callable $handler = null, ?array $middlewares = null)
    {
        $this->addHandlerWithOptionalMiddleware($path, "DELETE", $handler, $middlewares);
    }

    public function patch(string $path, ?callable $handler = null, ?array $middlewares = null)
    {
        $this->addHandlerWithOptionalMiddleware($path, "PATCH", $handler, $middlewares);
    }

    public function update(string $path, ?callable $handler = null, ?array $middlewares = null)
    {
        $this->addHandlerWithOptionalMiddleware($path, "UPDATE", $handler, $middlewares);
    }

    /**
     * Summary of send
     * @param int $statusCode
     * @return bool|int
     */
    public static function send(int $statusCode,mixed $value){
       http_response_code($statusCode);
        return print_r($value);

    }
    public static function redirect(string $path){
        header("Location: $path");
        exit;
    }

    
}
