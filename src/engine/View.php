<?php

namespace Francis\SublimePhp\Engine;

use Francis\SublimePhp\Cors;
use Francis\SublimePhp\RouteResolve;

class View extends RouteResolve
{
    static $blocks = [];
    static $cache_path = "../src/.cache/";
    static $file_path = "../views/";
    static $cache_enabled = true;
    static $cache_lifetime = 3600;
    static $cache_file_prefix = "sublimephp_";
    static $file_ext = ".html";

    public static function view($file, $data = array())
    {
        $path = self::$file_path . $file . self::$file_ext;
        $cached_file = self::cache($path);
        array_merge($_SERVER, ["." => $path]);
        extract($data, EXTR_SKIP);
        require $cached_file;
    }
 
    public static function cache($file)
    {
        if (!file_exists(self::$cache_path)) {
            mkdir(self::$cache_path, 0755);
        }
        $cached_file = self::$cache_path . str_replace(array('/', self::$file_ext), array('_', ''), $file . '.php');
        if (!self::$cache_enabled || !file_exists($cached_file) || filemtime($cached_file) < filemtime($file)) {
            $code = self::includeFiles($file);
            $code = self::compileCode($code);
            file_put_contents($cached_file, '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . PHP_EOL . $code);
        }
        return $cached_file;
    }
    private static function compilePHP($code)
    {
        return preg_replace("~\{\s*(.+?)}\s*\}~is", "<?php $1 ?>", $code);
    }
    public static function compileEchos($code)
    {
        return preg_replace('~\{{\s*(.+?)\s*\}}~is', '<?= $$1 ?>', $code);
    }
    public static function clearCache()
    {
        $getFile =scandir(self::$cache_path);
        foreach ($getFile as $file) {
            # code...
            is_file(self::$cache_path.DIRECTORY_SEPARATOR.$file) && unlink(self::$cache_path.DIRECTORY_SEPARATOR.$file);
            
        }
        /* $cors = new Cors();
        $cors->consoleLog("info", "cache clear"); */
        file_put_contents("php://stdout", "[info] cache clear...");
       // rmdir(self::$cache_path);
    }
    public static function compileCode($code)
    {
        $code = self::compileBlock($code);
        $code = self::compileYield($code);
        $code = self::compileEscapedEchos($code);
        $code = self::compileEchos($code);
        $code = self::compilePHP($code);
        return $code;

    }
    public static function compileEscapedEchos($code)
    {
        return preg_replace('~\{{{\s*(.+?)\s*\}}}~is', '<?php echo htmlentities($1, ENT_QUOTES, \'UTF-8\') ?>', $code);
    }
    public static function includeFiles($file)
    {
        $code = file_get_contents(self::$file_path.$file);
        preg_match_all('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', $code, $matches, PREG_SET_ORDER);
        foreach ($matches as $value) {
            $code = str_replace($value[0], self::includeFiles($value[2]), $code);
        }
        $code = preg_replace('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', '', $code);
        return $code;
    }
    public static function compileBlock($code)
    {
        preg_match_all('/{% ?block ?(.*?) ?%}(.*?){% ?endblock ?%}/is', $code, $matches, PREG_SET_ORDER);
        foreach ($matches as $value) {
            if (!array_key_exists($value[1], self::$blocks)) {
                self::$blocks[$value[1]] = '';
            }

            if (strpos($value[2], '@parent') === false) {
                self::$blocks[$value[1]] = $value[2];
            } else {
                self::$blocks[$value[1]] = str_replace('@parent', self::$blocks[$value[1]], $value[2]);
            }
            $code = str_replace($value[0], '', $code);
        }
        return $code;
    }

    public static function compileYield($code)
    {
        foreach (self::$blocks as $block => $value) {
            $code = preg_replace('/{% ?yield ?' . $block . ' ?%}/', $value, $code);
        }
        $code = preg_replace('/{% ?yield ?(.*?) ?%}/i', '', $code);
        return $code;
    }

}
