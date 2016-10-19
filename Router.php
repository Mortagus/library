<?php

namespace Mortagus\Library\Intern\Router;

use Mortagus\Library\Intern\DebugTool;

class Router {
    
    private $url;
    
    private $routeCollection = array();
    
    private $namedRouteCollection = array();
    
    public function __construct($url) {
        $this->url = $url;
    }
    
    public function addGetPath($path, $callable, $name = null) {
//        return $this->addRoute($path, $callable, $name, 'GET');
        $this->addRoute($path, $callable, $name, 'GET');
    }
    
    public function addPostPath($path, $callable, $name = null) {
//        return $this->addRoute($path, $callable, $name, 'POST');
        $this->addRoute($path, $callable, $name, 'POST');
    }
    
    private function addRoute($path, $callable, $name, $method) {
        $route = new Route($this, $path, $callable);
        $this->routeCollection[$method][] = $route;
        if (is_string($callable) && is_null($name)) {
            $name = $callable;
        }
        if (!is_null($name)) {
            $this->namedRouteCollection[$name] = $route;
        }
    }

    public function getPathByName($name) {
        return isset($this->namedRouteCollection[$name]) ? $this->namedRouteCollection[$name]->getPath() : null;
    }
    
    public function run() {
        DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'DEBUG' => "\n\t\t url = " . $this->url));
        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        if (!isset($this->routeCollection[$method])) {
            DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'EXCEPTION' => "\n\t\t" . $method . ' does not exist !'));
            DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'REDIRECTION' => "\n\t\tNEW URL : " . BASE_URL . DS . 'exceptionOccurred' . ' !'));
            header('Location : ' . BASE_URL . DS . 'exceptionOccurred');
            exit();
//            throw new \Exception('[' . __CLASS__ . "]\n\t\t[" . __FUNCTION__ . "]\n\t\t\t" . $method . ' does not exist !');
        }
        /** @var Route $routeObject */
        foreach ($this->routeCollection[$method] as $routeObject) {
            if ($routeObject->match($this->url)) {
                return $routeObject->call();
            }
        }
        DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'EXCEPTION' => "\n\t\tNo matching routes url = " . $this->url . ' !'));
        DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'REDIRECTION' => "\n\t\tNEW URL : " . BASE_URL . DS . 'exceptionOccurred' . ' !'));
        header('Location : ' . BASE_URL . DS . 'exceptionOccurred');
        exit();
//        throw new \Exception('[' . __CLASS__ . "]\n\t\t[" . __FUNCTION__ . "]\n\t\t\tNo matching routes url = " . $this->url . ' !');
    }
    
    public function url($routeName, $params = array()) {
        if (!isset($this->routeCollection[$routeName])) {
            DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'EXCEPTION' => "\n\t\tNo route matches the name " . $routeName . ' !'));
            DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'REDIRECTION' => "\n\t\tNEW URL : " . BASE_URL . DS . 'exceptionOccurred' . ' !'));
            header('Location : ' . BASE_URL . DS . 'exceptionOccurred');
            exit();
//            throw new \Exception('[' . __CLASS__ . "]\n\t\t[" . __FUNCTION__ . "]\n\t\t\tNo route matches the name " . $routeName . ' !');
        }
        $this->routeCollection[$routeName]->getUrl($params);
    }
    
}
