<?php

namespace Core;
class Autoloader
{
    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    public function loadClass($className)
    {
        $className = strval($className);
        $dir = appDir();
        $classPath = $dir . '/' . $className . '.php';
        $classPath = normalizeSlashes($classPath);
        if (file_exists($classPath)) {
            require_once $classPath;
        }
    }
}