<?php
namespace Francis\SublimePhp\dotEnv;

class Env
{
    private $envVars;
    public function __construct(string $file = "../.env")
    {
        $this->envVars = file_get_contents($file);
        $arr = json_decode($this->envVars);
        foreach ($arr as $key => $value) {
            $_ENV[$key] = $value;
        }
    }

}
