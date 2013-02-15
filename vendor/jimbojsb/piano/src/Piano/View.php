<?php
namespace Piano;

class View
{
    protected static $basePath;
    protected $viewScript;

    public static function setBathPath($path)
    {
        self::$basePath = $path;
    }

    public function __construct($vars = array())
    {
        foreach ($vars as $key => $val) {
            $this->$key = $val;
        }
        return $this;
    }

    public function render($viewScript, $vars = array())
    {
        if ($vars) {
            $view = clone($this);
            foreach ($vars as $key => $val) {
                $view->$key = $val;
            }
            return $view->render($viewScript);

        } else {
            ob_start();
            include self::$basePath . '/' . $viewScript;
            $result = ob_get_contents();
            ob_end_clean();
            return $result;
        }
    }
}