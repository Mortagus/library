<?php

namespace Mortagus\Library\Intern;


class ErrorManager extends Controller {

    /**
     *
     */
    static public function register() {
        error_reporting(E_ALL);
        set_error_handler(array(__CLASS__, 'errorHandler'));
        set_exception_handler(array(__CLASS__, 'exceptionHandler'));
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @param $errcontext
     */
    static public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        http_response_code(503);
        $errno = ErrorManager::errorNoConverter($errno);
        DebugTool::debugTrace(array(
            'ERROR_NO' => $errno,
            'ERROR_STR' => $errstr,
            'ERROR_FILE' => $errfile,
            'ERROR_LINE' => $errline,
            'ERROR_CONTEXT' => $errcontext
        ));
        die;
    }

    /**
     * @param \Exception $exception
     * @throws Exception
     */
    static public function exceptionHandler(\Exception $exception) {
        http_response_code(503);
        DebugTool::debugTrace(array(
            'EXCEPTION MESSAGE' => $exception->getMessage(),
            'EXCEPTION TRACE' => "\n" . $exception->getTraceAsString()
        ));
        die;
    }

    /**
     * @param $errNo
     * @return string
     */
    static private function errorNoConverter($errNo) {
        switch($errNo) {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
            default:
                return '!!! UNKNOWN ERROR CODE !!!';
        }
    }

}