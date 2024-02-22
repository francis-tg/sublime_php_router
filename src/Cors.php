<?php
namespace Francis\SublimePhp;

class Cors
{
    private $CORS_ORIGIN_ALLOWED = "*";
    public function __construct(string $host = "*")
    {
        $this->CORS_ORIGIN_ALLOWED = $host;
        $this->core();

    }
    public function consoleLog($level, $msg)
    {
        file_put_contents("php://stdout", "[" . $level . "] " . $msg . "\n");
    }
    /**
     * Summary of applyCorsHeaders
     * @param mixed $origin
     * @return void
     *
     */
    private function applyCorsHeaders($origin)
    {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Methods: GET, POST,PUT,DELETE,PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept');
    }
    private function core()
    {
        if (preg_match('/\.(?:png|jpg|jpeg|gif|csv)$/', $_SERVER["REQUEST_URI"])) {
            $this->consoleLog('info', "Transparent routing for : " . $_SERVER["REQUEST_URI"]);
            return false;
        } else if (preg_match('/^.*$/i', $_SERVER["REQUEST_URI"])) {
            $filePath = "{$_SERVER["SCRIPT_FILENAME"]}";
            $this->applyCorsHeaders($this->CORS_ORIGIN_ALLOWED);
            //var_dump($_SERVER);
            if (!file_exists($filePath)) {
                $this->consoleLog('info', "File not found Error for : " . $_SERVER["REQUEST_URI"]);
                // return false;
                http_response_code(404);
                echo "File not Found : {$filePath}";
                return true;
            }
            $mime = mime_content_type($filePath);
            // https://stackoverflow.com/questions/45179337/mime-content-type-returning-text-plain-for-css-and-js-files-only
            // https://stackoverflow.com/questions/7236191/how-to-create-a-custom-magic-file-database
            // Otherwise, you can use custom rules :
            $customMappings = [
                'js' => 'text/javascript', //'application/javascript',
                'css' => 'text/css',
                "json" => "application/json",
                "encode" => "application/urlencoded",
            ];
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            // consoleLog('Debug', $ext);
            if (array_key_exists($ext, $customMappings)) {
                $mime = $customMappings[$ext];
            }
            $this->consoleLog('info', "CORS added to file {$mime} : {$filePath}");
            //echo file_get_contents($filePath);
            header("Content-type: text/html");

            return true;
        } else {
            $this->consoleLog('info', "Not catched by routing, Transparent serving for : "
                . $_SERVER["REQUEST_URI"]);
            return false; // Let php bultin server serve
        }

    }

}
