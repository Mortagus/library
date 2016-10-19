<?php
/**
 * Created by PhpStorm.
 * User: Benouz
 * Date: 29/03/2016
 * Time: 18:48
 */

namespace Mortagus\Library\Intern;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;


class DoctrineManager {

    /**
     * @var DoctrineManager $instance
     */
    private static $instance;

    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @var string $dbDriver
     */
    private $dbDriver;

    /**
     * @var string $dbHost
     */
    private $dbHost;

    /**
     * @var string $dbName
     */
    private $dbName;

    /**
     * @var string $dbUser
     */
    private $dbUser;

    /**
     * @var string $dbPassword
     */
    private $dbPassword;

    /**
     * @var boolean $isDevMode
     */
    private $isDevMode;

    /**
     * @var array $entityFilesPaths
     */
    private $entityFilesPaths = array();

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

    private function __construct() {
        $this->entityFilesPaths = array(MDL_DIR . DS . 'Entities');
        $this->dbDriver = DB_DRIVER;
        $this->dbName = DB_NAME;
        $this->dbHost = DB_HOST;
        $this->dbUser = DB_USER;
        $this->dbPassword = DB_PWD;
        $this->isDevMode = IS_DEV_MOD;
        $this->initEntityManager();
    }

    private function initEntityManager() {
        $dbParams = array(
            'driver' => $this->dbDriver,
            'user' => $this->dbUser,
            'password' => $this->dbPassword,
            'dbname' => $this->dbName,
            'host' => $this->dbHost
        );
        $config = Setup::createAnnotationMetadataConfiguration($this->entityFilesPaths, $this->isDevMode);
        $this->entityManager = EntityManager::create($dbParams, $config);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager() {
        return $this->entityManager;
    }

    /**
     * @return ConsoleRunner
     */
    private function getConsoleHelper() {
        return ConsoleRunner::createHelperSet($this->entityManager);
    }

    public function runConsoleHelper() {
        $helperSet = $this->getConsoleHelper();
        ConsoleRunner::run($helperSet);
    }

}