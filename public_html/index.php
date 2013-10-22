<?php

set_include_path('../library' . PATH_SEPARATOR . get_include_path());

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
defined('APPLICATION_ENV') ||
    define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?
        getenv('APPLICATION_ENV'): 'development'));



require_once 'Zend/Application.php';
require_once 'Symfony/Component/ClassLoader/UniversalClassLoader.php';

$autoloader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$autoloader->registerNamespaces(array('Cast'  => ROOT_PATH . '/library'));
$autoloader->useIncludePath(true);
$autoloader->register();


$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/core.ini'
);


try {
    $application->bootstrap();
    $front = $application->getBootstrap()->getResource('FrontController');
    $front->addControllerDirectory(APPLICATION_PATH.'/modules/core/controllers');
    $application->run();

} catch (Exception $e) {
    throw $e;
}

