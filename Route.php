<?php

namespace Mortagus\Library\Intern\Router;

use Mortagus\Library\Intern\DebugTool;

class Route {

    /**
     * @var Router
     */
    private $routerParent;

    /**
     * @var string
     */
    private $path;

    /**
     * @var function | string
     */
    private $callable;

    /**
     * @var array
     */
    private $matches = array();

    /**
     * @var array
     */
    private $additionalRules = array();

    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    public function __construct($routerParent, $path, $callable) {
        $this->routerParent = $routerParent;
        $this->path = trim($path, '/');
        $this->callable = $callable;
    }

    /**
     * toString
     */
    public function __toString() {
        return $this->path . PHP_EOL . $this->callable;
    }

    /**
     * @return mixed
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action) {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * @param mixed $module
     */
    public function setModule($module) {
        $this->module = $module;
    }

    /**
     * @return mixed
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller) {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }
    
    public function match ($url) {
        $flagMatch = true;
        $url = trim($url, '/');
        $path = preg_replace_callback('/:([\w]+)/', array($this, 'paramMatch'), $this->path);
        $regex = '!^' . $path . '$!i';
        if (!preg_match($regex, $url, $this->matches)) {
            $flagMatch = false;
        } else {
            array_shift($this->matches);
        }
        return $flagMatch;
    }
    
    private function paramMatch($match) {
        $regexToReturn = '([^\/]+)';
        if (isset($this->additionalRules[$match[1]])) {
            $regexToReturn = '(' . $this->additionalRules[$match[1]] . ')';
        }
        return $regexToReturn;
    }
    
    public function call() {
        try {
            if (is_string($this->callable)) {
                $elements = explode(':', $this->callable);
                $this->module = $elements[0] . 'Module';
                $this->controller = $elements[1] . 'Controller';
                $this->action = $elements[2] . 'Action';
                $controller = 'Mortagus\\Modules\\' . $this->module . '\\Controller\\' . $this->controller;
                DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'LOG' => 'instanciation du controller suivant : ' . $controller));
                $controller = new $controller($this);
                return call_user_func_array(array($controller, $this->action), $this->matches);
            } else {
                return call_user_func_array($this->callable, $this->matches);
            }
        } catch (\Exception $e) {
            DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'EXCEPTION' => $e->getMessage()));
            header('Location: ' . BASE_URL . DS . 'errorOccurred');
        }
    }
    
    public function with($paramName, $regex) {
        $this->additionalRules[$paramName] = str_replace('(', '(?:', $regex);
        return $this;
    }
    
    public function getUrl($params) {
        $path = $this->path;
        foreach ($params as $name=>$value) {
            $path = str_replace(':'.$name, $value, $path);
        }
        return $path;
    }

    /**
     * @return Router
     */
    public function getRouter() {
        return $this->routerParent;
    }
    
}
