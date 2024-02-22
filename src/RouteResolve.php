<?php
namespace Francis\SublimePhp;

class RouteResolve extends Cors
{
    private $request_uri;
    private $request_method;

    private $params = [];
    private $handlers = [];
    private $middleware = [];
    public function __construct()
    {
        $this->request_uri = $_SERVER['REQUEST_URI'];
        $this->request_method = $_SERVER["REQUEST_METHOD"];

    }
    /**
     * Ajoute un middleware à la liste des middleware.
     * @param callable $middleware Le middleware à ajouter.
     */
    public function addMiddleware(callable $middleware)
    {
        $this->middleware[] = $middleware;
    }
    /**
     * Summary of extract_params_from_url
     * @param array $pattern_path
     * @param string $uri_path
     * @return array
     */
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
    /**
     * Summary of is_param
     * @param string $part
     * @return bool
     */
    protected function is_param(string $part): bool
    {
        return preg_match('/^\:[^\/]+\:$/', $part) === 1;
    }
    /**
     * Summary of extract_param_name
     * @param string $part
     * @return string
     */
    protected function extract_param_name(string $part): string
    {
        return trim($part, ':');
    }
    /**
     * Summary of extract_param_value
     * @param int $i
     * @param string $uri_path
     * @return string|null
     */
    protected function extract_param_value(int $i, string $uri_path): ?string
    {
        $uri_parts = explode('/', $uri_path);
        return $uri_parts[$i] ?? null;
    }
    /**
     * Summary of uri_matches_pattern
     * @param array $pattern_path
     * @param string $uri_path
     * @return bool
     */
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
    /**
 * Summary of addHandler
 * @param string $path
 * @param string $method
 * @param mixed $handler
 * @return array
 */

    public function addHandler(string $path, string $method, $handler)
    {
        $parse_uri = parse_url($this->request_uri, PHP_URL_PATH);
        
        if($parse_uri===$path){
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
        return [];
    }
    /**
     * 
     * 
     * @param string $path
     * @param string $method
     * @param mixed $handler
     * @param mixed $middlewares
     * @return array
     */
    public function addRootHandlerWithMiddleware(string $path, string $method, $handler, ?array $middlewares = null)
    {
        $parse_uri = parse_url($this->request_uri, PHP_URL_PATH);
        
        if ($parse_uri === $path && $this->request_method === $method) {
            $pattern_path = explode("/", $path);
            $this->params = $this->extract_params_from_url($pattern_path, $parse_uri);
            
            // Vérifie si l'URI correspond au chemin de la route et à la méthode HTTP
            if ($this->uri_matches_pattern($pattern_path, $parse_uri)) {
                // Si des middlewares sont spécifiés, les exécuter avant le gestionnaire de route
                if (!empty($middlewares)) {
                    foreach ($middlewares as $middleware) {
                        $middleware($this->request_uri, $this->request_method);
                    }
                }
                
                // Ajoute le gestionnaire de route avec ses détails dans le tableau des gestionnaires
                $this->handlers[$this->request_method . $parse_uri] = [
                    "path" => $parse_uri,
                    "method" => $this->request_method,
                    "rel-path" => $path,
                    "handler" => $handler,
                    "params" => $this->params,
                    "query" => $_GET,
                    "body" => $_POST,
                ];
                
                // Retourne les détails du gestionnaire de route ajouté
                return $this->handlers[$this->request_method . $parse_uri];
            }
        }
        
        // Retourne un tableau vide si aucune correspondance n'est trouvée
        return [];
    }

    /**
     * Ajoute un gestionnaire de route avec middleware.
     * @param string $path Le chemin de la route.
     * @param string $method La méthode HTTP de la route.
     * @param callable $handler Le gestionnaire de la route.
     */
    public function addHandlerWithMiddleware(string $path, string $method, callable $handler)
    {
        $this->addHandler($path, $method, function() use ($handler) {
            // Exécuter les middlewares
            foreach ($this->middleware as $middleware) {
                $middleware($this->request_uri, $this->request_method);
            }
            // Exécuter le gestionnaire de route final
            $handler();
        });
    }
     /**
     * Ajoute un gestionnaire de route avec middleware facultatif.
     * @param string $path Le chemin de la route.
     * @param string $method La méthode HTTP de la route.
     * @param callable $handler Le gestionnaire de la route.
     * @param callable|array|null $middleware Le middleware facultatif.
     */
    protected function addHandlerWithOptionalMiddleware(string $path, string $method, $handler, ?array $middlewares = null)
    {
        $this->addHandler($path, $method, function () use ($handler, $middlewares) {
            if (!empty($middlewares)) {
                foreach ($middlewares as $middleware) {
                    $this->executeMiddleware($middleware);
                }
            }
            
            $handler($this->handlers);
        });
    }

    /**
     * Exécute le middleware avec les arguments nécessaires.
     * @param callable $middleware Le middleware à exécuter.
     */
    protected function executeMiddleware(callable $middleware)
    {
        // Exécuter le middleware avec les arguments nécessaires
        $middleware($this->handlers);
    }
    /**
     * Summary of run
     * @return void
     */
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
