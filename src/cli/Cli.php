<?php

namespace Francis\SublimePhp\cli;

class Cli
{
 static function consoleLog($level, $msg)
{
    file_put_contents("php://stdout", "[" . $level . "] " . $msg . "\n");
}
   
}
