<?php
namespace SbxCommon;

use Zend\ModuleManager\Feature;

class Module implements Feature\AutoloaderProviderInterface, Feature\ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap($e)
    {
        $application = $e->getApplication();
        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $application->getServiceManager();

        $pm = $serviceManager->get('ViewHelperManager')->get('Navigation')->getPluginManager();
        $pm->setInvokableClass('menu', 'SbxCommon\View\Helper\Navigation\Menu');
    }
}
