<?php

namespace SbxCommon\Form\Moxiemanager;

use Auth\External\Exception\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BridgeFactory implements FactoryInterface
{
    const CONFIG_SECTION = 'moxiemanager';
    const CONFIG_LOCATION = 'location';
    const CONFIG_PLUGIN_URL = 'pluginScriptUrl';
    const CONFIG_PRESETS = 'presets';
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $sm
     *
     * @return mixed
     * @throws Exception
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $bridge = new Bridge();
        $bridge->setServiceLocator($sm);

        $config = $this->getConfig($sm);
        if(empty($config[self::CONFIG_LOCATION])){
            throw new Exception('Moxiemanager code location is not set. For configuration see documentation for sbxCommon module');
        }
        if(empty($config[self::CONFIG_PRESETS])){
            throw new Exception('Moxiemanager have no configured presets. For configuration see documentation for sbxCommon module');
        }
        $bridge->setLibraryLocation($config[self::CONFIG_LOCATION]);

        if(!empty($config[self::CONFIG_PLUGIN_URL])){
            $bridge->setLibraryPluginScriptUrl($config[self::CONFIG_PLUGIN_URL]);
        }

        return $bridge;
    }



    public function getConfig(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('config');
        if(empty($config[self::CONFIG_SECTION])){
            throw new Exception('Moxiemanager bridge is not configured. For configuration see documentation for sbxCommon module');
        }
        return $config[self::CONFIG_SECTION];
    }
}
