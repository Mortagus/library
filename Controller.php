<?php

namespace Mortagus\Library\Intern;

use Mortagus\Library\Intern\Router\Route;

class Controller {

    /**
     * @var Route $route
     */
    protected $route;

    /**
     * @var PageBuilder $pb : PageBuilder Instance
     */
    protected $pb;

    /**
     * @param DoctrineManager $doctrine
     */
    protected $doctrine;

    /**
     * @param Translator $trans
     */
    protected $translator;

    /* **************************************
     * START FUNCTION Global of Controller
     * **************************************/

    public function __construct(Route $route) {
        $this->route = $route;
        $this->pb = PageBuilder::getInstance();
        $this->doctrine = DoctrineManager::getInstance();
        $this->initWebPage();
    }

    /**
     * general initiation of the web page
     */
    protected function initWebPage() {
        $this->pb->setTitle(WEB_TITLE);
        $this->pb->setShortcutIconPath('favicon.ico');
        $this->pb->setMetaCollection(array(
            'charset' => array('charset' => 'UTF-8'),
            'viewport' => array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0')
        ));
        $this->pb->addDiv('main');
        $this->pb->addDiv('menu');
        $this->pb->addDiv('content');
        $this->pb->addDiv('footer');

        $this->pb->addCss('bootstrap', EXT_GLB_RSC_URL . DS . 'bootstrap' . DS . 'css' . DS . 'bootstrap.min.css');
        $this->pb->addCss('main', INT_GLB_RSC_URL . DS . 'css' . DS . 'main.css');
        $this->pb->addJs('jquery', EXT_GLB_RSC_URL . DS. 'jquery' . DS . 'jquery-2.2.3.min.js');
        $this->pb->addJs('bootstrap', EXT_GLB_RSC_URL . DS. 'bootstrap' . DS . 'js' . DS . 'bootstrap.min.js');
    }

    protected function render($viewPath, array $parameters = null) {
        $return = '';
        if (!is_null($parameters)) {
            extract($parameters);
        }

        if (!is_file($viewPath)) {
            DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'ERROR' => $viewPath . ' does not exist !'));
            header('Location: ' . BASE_URL . DS . 'errorOccurred');
            die;
        } else {
            DebugTool::debugTrace(array('class' => __CLASS__, 'function' => __FUNCTION__, 'LOG' => 'include du fichier suivant : ' . $viewPath));
            ob_start();
            include $viewPath;
            $return = ob_get_clean();
        }
        return $return;
    }

    protected function loadExtraController($moduleName, $controllerName) {
        $controllerInstance = null;
        $controllerName = 'Mortagus\\Module\\' . $moduleName . 'Module\\Controller\\' . $controllerName . 'Controller.php';
        $controllerInstance = new $controllerName($this->route);
        return $controllerInstance;
    }

    protected function addMenuBlock() {
        $menuTemplePath = GLB_PATH . DS . 'intern' . DS . 'views' . DS . 'menu.html.php';
        $this->pb->appendDivContent('menu', $this->render($menuTemplePath, array(
            'logoutPath' => BASE_URL . DS . 'logout',
            'menuItemsCollection' => $this->buildMenuItemsCollection(),
        )));
    }

    private function buildMenuItemsCollection() {
        return array();
    }

    /**
     * @param string $controllerName
     * @param string $moduleName
     * @return string
     */
    protected function getViewPath($moduleName, $controllerName) {
        return SRC_DIR . DS . $moduleName . 'Module' . DS . 'Resources' . DS . 'views' . DS . $controllerName;
    }

    /**
     * @return Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * @return PageBuilder
     */
    public function getPb() {
        return $this->pb;
    }

    /**
     * @return DoctrineManager
     */
    public function getDoctrine() {
        return $this->doctrine;
    }

    /**
     * @param $name
     * @return string
     */
    public function getUrl($name) {
        return $this->getRoute()->getRouter()->getPathByName($name);
    }

}
