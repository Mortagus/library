<?php

namespace Mortagus\Library\Intern;

class DebugTool {

    public static $flagActivate = true;
    public static $flagUseFile = true;
    public static $flagTypeWrite = false;
    
    public static $repositoryName = "debug";

    public static function debugTrace(array $parameters, $path = "") {
        if (DebugTool::$flagActivate) {
            $string = self::buildMessageFromParameters($parameters);
            if (DebugTool::$flagUseFile) {
                $finalFilePath = self::getDebugPath($path);
                if (self::checkFile($finalFilePath)) {
                    $fileHandler = self::getFileHandler($finalFilePath);
                    self::updateRights($finalFilePath);
                    fwrite($fileHandler, $string);
                    fclose($fileHandler);
                } else {
                    throw new Exception("[DebugTool][ERROR] check file FAIL");
                }
            } else {
                echo $string;
            }
        }
    }

    private static function buildMessageFromParameters(array $parameters) {
        $string = '[' . date('H:i:s') . ']' . self::getEOL();
        foreach ($parameters as $key => $value) {
            $value = (is_array($value)) ? print_r($value, true) : $value;
            $string .= "\t[$key] : $value" . self::getEOL();
        }
        return $string;
    }
    
    private static function getEOL() {
        return (self::$flagUseFile) ? PHP_EOL : "<br>";
    }

    private static function getDebugPath($path = "") {
        $fileName = date('Y-m-d') . " - " . session_id();
        $finalDirName = self::createDebugDirectoryIfNotExist($path);
        $fileName = $finalDirName . DS . $fileName . '.txt';
        return $fileName;
    }
    
    private static function createDebugDirectoryIfNotExist($path = '') {
        $debugDir = "." . DS . DebugTool::$repositoryName;
        //création du répertoire debug
        if (!file_exists($debugDir)) {
            mkdir($debugDir);
        }
        
        $debugDir .= (!empty($path)) ? DS . $path : "";
        
        if (!file_exists($debugDir)) {
            mkdir($debugDir);
        }
        return $debugDir;
    }

    private static function getFileHandler($path) {
        $handler = ((self::$flagTypeWrite) ? fopen($path, 'wb') : fopen($path, 'ab'));
        if (!$handler) {
            throw new Exception("ERROR FOPEN DEBUG_TOOL");
        }
        return $handler;
    }
    
    private static function checkFile($path) {
        $flag = true;
        if (file_exists($path)) {
            $flag &= is_writeable($path);
        }
        return $flag;
    }
    
    private static function updateRights($path) {
        chmod($path,0755);
    }

}