<?php

namespace PFBC;

use Zend\ModuleManager\ModuleManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\EventInterface;
use PFBC\Form;

class Module
{

    public function onBootstrap(EventInterface $e)
    {
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . "/src/",
                ],
            ],
        ];
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'PFBC\Form' => function ($serviceLocator) {
                    return new Form($serviceLocator);
                },
                'PFBC\Validation\Database' => function ($serviceLocator) {
                    return new \PFBC\Validation\Database('', '', '', $serviceLocator);
                },
            ],
        ];
    }
}
