<?php

namespace Mortagus\Library\Intern;

class PageBuilder {

    /**
     * @var PageBuilder $instance
     */
    public static $instance;

    /**
     * @var string titre
     * représente le titre de la page HTML
     */
    private $title;

    /**
     * @var string $shortcutIconPath
     * représente le chemin vers l'image du shortcut (petite image avec l'onglet de la page
     */
    private $shortcutIconPath;

    /**
     * @var array $metaCollection
     * permet de contenir l'ensemble des balises meta présentent dans le <head>
     */
    private $metaCollection;

    /**
     * @var array $divCollection
     * permet de contenir un ensemble des blocs principaux contenus dans le body
     */
    private $divCollection;

    /**
     * @var string $html
     * contient l'ensemble de l'HTML à afficher côté client
     */
    private $html;

    /**
     * @var array $cssCollection
     */
    private $cssCollection;

    /**
     * @var array $jsCollection
     */
    private $jsCollection;

    /**
     * Constructor
     */
    private function __construct() {
        $this->title = '';
        $this->baseHref = '';
        $this->shortcutIconPath = '';
        $this->metaCollection = array();
        $this->divCollection = array();
        $this->cssCollection = array();
        $this->jsCollection = array();
        $this->html = '';
    }

    /**
     * Singleton Pattern
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $constructor = __CLASS__;
            self::$instance = new $constructor();
        }
        return self::$instance;
    }

    /**
     * buildPageHtml : permet de construire l'ensemble de la page en concaténant
     * chaque morceau contenu dans les variables de classe.
     */
    public function buildPageHtml() {
        $this->html = '';
        $this->html .= '<!DOCTYPE html>' . PHP_EOL;
        $this->html .= '<html>' . PHP_EOL;
        $this->html .= '    <head>' . PHP_EOL;
        $this->html .= $this->buildTitleHtml();
        $this->html .= $this->buildShortcutIconHtml();
        $this->html .= $this->buildMetaHtml();
        $this->html .= $this->buildCssHtml();
        $this->html .= '    </head>' . PHP_EOL;
        $this->html .= '    <body>' . PHP_EOL;
        $this->html .= $this->buildDivBlocsHtml();
        $this->html .= $this->buildJsHtml();
        $this->html .= '    </body>' . PHP_EOL;
        $this->html .= '</html>' . PHP_EOL;
    }

    /**
     * printHtml : permet de faire un echo du code HTML contenu dans la variable de classe homonyme
     */
    public function printHtml() {
        $this->buildPageHtml();
        echo $this->html;
    }

    /**
     * @return string
     */
    private function buildTitleHtml() {
        return '<title>' . $this->title . '</title>' . PHP_EOL;
    }

    /**
     * @return string titre
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @param string $string
     */
    public function appendTitle($string) {
        $this->title .= ' - ' . $string;
    }

    /**
     * @return string
     */
    private function buildShortcutIconHtml() {
        return (!empty($this->shortcutIconPath)) ? '<link rel="shortcut icon" href="' . $this->shortcutIconPath . '" />' . PHP_EOL : '';
    }

    /**
     * @return string shortcutIconPath
     */
    public function getShortcutIconPath() {
        return $this->shortcutIconPath;
    }

    /**
     * @param string $shortcutIconPath
     */
    public function setShortcutIconPath($shortcutIconPath) {
        $this->shortcutIconPath = $shortcutIconPath;
    }

    /**
     * @return array
     */
    public function getMetaCollection() {
        return $this->metaCollection;
    }

    /**
     * @param array $metaCollection
     */
    public function setMetaCollection(array $metaCollection) {
        $this->metaCollection = $metaCollection;
    }

    /**
     * @param string $metaName
     * @param array $metaParams
     */
    public function addMeta($metaName, array $metaParams) {
        $this->metaCollection[$metaName] = $metaParams;
    }

    /**
     * @return string HTML
     */
    private function buildMetaHtml() {
        $html = '';
        foreach ($this->metaCollection as $metaData) {
            $html .= '<meta';
            $html .= (isset($metaData['charset'])) ? ' charset="' . $metaData['charset'] . '"' : '';
            $html .= (isset($metaData['content'])) ? ' content="' . $metaData['content'] . '"' : '';
            $html .= (isset($metaData['http-equiv'])) ? ' http-equiv="' . $metaData['http-equiv'] . '"' : '';
            $html .= (isset($metaData['name'])) ? ' name="' . $metaData['name'] . '"' : '';
            $html .= (isset($metaData['scheme'])) ? ' scheme="' . $metaData['scheme'] . '"' : '';
            $html .= ' />' . PHP_EOL;
        }
        return $html;
    }

    /**
     * @return array
     */
    public function getDivCollection() {
        return $this->divCollection;
    }

    /**
     * @param array $divCollection
     */
    public function setDivCollection($divCollection) {
        $this->divCollection = $divCollection;
    }

    /**
     * @param string $divId : unique id d'un bloc div de la page
     * @param string $class : valeur de la classe HTML
     * @param string $html : code html du bloc
     */
    public function addDiv($divId, $html = '', $class = '') {
        $this->divCollection[$divId]['id'] = $divId;
        $this->divCollection[$divId]['class'] = $class;
        $this->divCollection[$divId]['html'] = $html;
    }

    /**
     * @param string $divId : unique id d'un bloc div de la page
     * @param string $html : code html qui va être ajouter à la suite d'un contenu déjà existant
     *
     * @throws \Exception
     */
    public function appendDivContent($divId, $html) {
        if (isset($this->divCollection[$divId])) {
            $this->divCollection[$divId]['html'] .= $html;
        } else {
            throw new \Exception('divId ' . $divId . ' bloc unknown !');
        }
    }

    /**
     * @param string $divId : unique id d'un bloc div de la page
     * @param string $html : code html qui va être ajouter à la suite d'un contenu déjà existant
     *
     * @throws \Exception
     */
    public function prependDivContent($divId, $html) {
        if (isset($this->divCollection[$divId])) {
            $this->divCollection[$divId]['html'] = $html . $this->divCollection[$divId]['html'];
        } else {
            throw new \Exception('divId ' . $divId . ' bloc unknown !');
        }
    }

    /**
     *
     */
    private function buildDivBlocsHtml() {
        $html = '';
        if (isset($this->divCollection['main'])) {
            $html .= '<main';
            $html .= (isset($this->divCollection['main']['class']) && !empty($this->divCollection['main']['class'])) ? ' class="' . $this->divCollection['main']['class'] .'" ' : '';
            $html .= '>' . PHP_EOL;
        }

        foreach ($this->divCollection as $divData) {
            if ($divData['id'] == 'main') {
                continue;
            }
            $html .= '<div id="' . $divData['id'] . '"';
            $html .= (!empty($divData['class'])) ? ' class="' . $divData['class'] . '" >' : ' >';
            $html .= (!empty($divData['html'])) ? PHP_EOL . $divData['html'] . PHP_EOL : '';
            $html .= '</div>' . PHP_EOL;
        }

        if (isset($this->divCollection['main'])) {
            $html .= '</main>' . PHP_EOL;
        }
        return $html;
    }

    /**
     * @return string html
     */
    public function getHtml() {
        return $this->html;
    }

    /**
     * @param string $html
     */
    public function setHtml($html) {
        $this->html = $html;
    }

    /**
     * @return array
     */
    public function getCssCollection() {
        return $this->cssCollection;
    }

    /**
     * @param array $cssCollection
     * @return PageBuilder
     */
    public function setCssCollection($cssCollection) {
        $this->cssCollection = $cssCollection;
        return $this;
    }

    public function addCss($cssName, $filename) {
        $this->cssCollection[$cssName] = $filename;
    }

    public function buildCssHtml() {
        $html = '';
        if (IS_DEV_MOD === true) {
            foreach ($this->cssCollection as $href) {
                $html .= '<link rel="stylesheet" type="text/css" href="' . $href . '" />' . PHP_EOL;
            }
        } else {
            $html = '<link rel="stylesheet" type="text/css" href="' . WEB_DIR . DS . 'public' . DS . 'css' . DS . 'main.css" />' . PHP_EOL;
        }
        return $html;
    }

    /**
     * @return array
     */
    public function getJsCollection() {
        return $this->jsCollection;
    }

    /**
     * @param array $jsCollection
     * @return PageBuilder
     */
    public function setJsCollection($jsCollection) {
        $this->jsCollection = $jsCollection;
        return $this;
    }

    public function addJs($jsName, $filename) {
        $this->jsCollection[$jsName] = $filename;
    }

    public function buildJsHtml() {
        $html = '';
        if (IS_DEV_MOD === true) {
            foreach ($this->jsCollection as $src) {
                $html .= '<script src="' . $src . '"></script>' . PHP_EOL;
            }
        } else {
            $html = '<script src="' . WEB_DIR . DS . 'public' . DS . 'js' . DS . 'main.js"></script>' . PHP_EOL;
        }
        return $html;
    }

}