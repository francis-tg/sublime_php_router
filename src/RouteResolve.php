<?php
namespace Francis\SublimePhp;

class RouteResolve
{
    private $request_uri;
    private $request_method;

    private $params = [];
    private $handlers = [];
    public function __construct()
    {
        $this->request_uri = $_SERVER['REQUEST_URI'];
        $this->request_method = $_SERVER["REQUEST_METHOD"];

    }
    protected function extract_params_from_url(array $pattern_path, string $uri_path): array
    {
        $params = [];

        foreach ($pattern_path as $i => $pattern_part) {
            if ($this->is_param($pattern_part)) {
                $param_name = $this->extract_param_name($pattern_part);
                $param_value = $this->extract_param_value($i, $uri_path);
                $params[$param_name] = $param_value;
            }
        }

        return $params;
    }

    protected function is_param(string $part): bool
    {
        return preg_match('/^\:[^\/]+\:$/', $part) === 1;
    }

    protected function extract_param_name(string $part): string
    {
        return trim($part, ':');
    }

    protected function extract_param_value(int $i, string $uri_path): ?string
    {
        $uri_parts = explode('/', $uri_path);
        return $uri_parts[$i] ?? null;
    }

    protected function uri_matches_pattern(array $pattern_path, string $uri_path): bool
    {
        $pattern_parts = array_filter($pattern_path, [$this, 'is_param']);
        $uri_parts = explode('/', trim($uri_path));
        if (count($pattern_path) !== count($uri_parts)) {

            return false;
        }

        foreach ($pattern_parts as $i => $pattern_part) {
            $param_name = $this->extract_param_name($pattern_part);
            $param_value = $this->extract_param_value($i, $uri_path);
            if ($param_value === null) {
                var_dump("false");
                return false;
            }
        }

        return true;
    }
    public function addHandler(string $path, string $method, $handler)
    {
        $parse_uri = parse_url($this->request_uri, PHP_URL_PATH);
        $pattern_path = explode("/", $path);
        $this->params = $this->extract_params_from_url($pattern_path, $parse_uri);
        if ($this->uri_matches_pattern($pattern_path, $parse_uri) && $this->request_method === $method) {
            return $this->handlers[$this->request_method . $parse_uri] = [
                "path" => $parse_uri,
                "method" => $this->request_method,
                "rel-path" => $path,
                "handler" => $handler,
                "params" => $this->params,
                "query" => $_GET,
                "body" => $_POST,
            ];
        }
    }
    public function run()
    {
        $callback = null;
        $newHanler = [];
        foreach ($this->handlers as $key => $value) {
            if ($value["path"] === $this->request_uri && $value["method"] === $this->request_method) {
                array_push($newHanler, $value);
                $callback = $value["handler"];
            } else {
                $callback = null;
            }
        }
        if (!$callback) {
            echo "<pre> Url <b>" . $this->request_uri . "</b> not found </pre>";
            return;
        }
        $callback && call_user_func_array($callback, $newHanler);
    }

}
