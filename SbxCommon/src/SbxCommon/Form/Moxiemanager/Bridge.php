<?php
namespace SbxCommon\Form\Moxiemanager;

use Auth\External\Exception\Exception;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class Bridge implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const WEB_ENDPOINT = 'endpoint';
    const PLUGIN_CONFIGURATION = 'configuration';
    const PLUGIN_ROOTPATH = 'filesystem.rootpath';

    private $libraryLocation = null;
    private $libraryPluginScriptUrl = null;

    /**
     * @return null
     */
    public function getLibraryLocation()
    {
        return substr($this->libraryLocation, -1) == '/' ? $this->libraryLocation : $this->libraryLocation . '/';
    }

    /**
     * @param null $libraryLocation
     *
     * @return $this
     */
    public function setLibraryLocation($libraryLocation)
    {
        $this->libraryLocation = $libraryLocation;
        return $this;
    }

    /**
     * @return null
     */
    public function getLibraryPluginScriptUrl()
    {
        if($this->libraryPluginScriptUrl == null && stristr($_SERVER['DOCUMENT_ROOT'], $this->getLibraryLocation())){
            $this->libraryPluginScriptUrl = substr($this->getLibraryLocation(), strlen($_SERVER['DOCUMENT_ROOT']));
            $this->libraryPluginScriptUrl .= 'plugin.js';
        }
        return $this->libraryPluginScriptUrl;
    }

    /**
     * @param null $libraryPluginScriptUrl
     *
     * @return $this
     */
    public function setLibraryPluginScriptUrl($libraryPluginScriptUrl)
    {
        $this->libraryPluginScriptUrl = $libraryPluginScriptUrl;
        return $this;
    }

    public function getPreset($presetName)
    {
        if (!defined('MOXMAN_ROOT')) {
            /**
             * Path to the root of the moxiemanager.
             *
             * @package MOXMAN
             */
            define('MOXMAN_ROOT', $this->getLibraryLocation());
        }

        $preset = new Preset($this->getPresetConfig($presetName));
        $preset->setName($presetName)->setMoxmanLocation($this->getLibraryLocation() . '/classes/MOXMAN.php');
        return $preset;
    }

    public function getPresetConfig($presetName)
    {
        $config = $this->getServiceLocator()->get('config');
        if(!array_key_exists($presetName, $config[BridgeFactory::CONFIG_SECTION][BridgeFactory::CONFIG_PRESETS])){
            throw new Exception('Moxiemanager Preset "' . $presetName . '" is not set');
        }

        $config = $config[BridgeFactory::CONFIG_SECTION][BridgeFactory::CONFIG_PRESETS][$presetName];

        if(empty($config[self::WEB_ENDPOINT])){
            throw new Exception('Web endpoint is not set for Preset "' . $presetName . '".');
        }
        if(empty($config[self::PLUGIN_CONFIGURATION][self::PLUGIN_ROOTPATH])){
            throw new Exception('Rootpath is not set for Preset "' . $presetName . '".');
        }

        if(!is_dir($config[self::PLUGIN_CONFIGURATION][self::PLUGIN_ROOTPATH])){
            mkdir($config[self::PLUGIN_CONFIGURATION][self::PLUGIN_ROOTPATH], 0777, true);
        }

        if(is_array($config[self::WEB_ENDPOINT])){
            $config[self::WEB_ENDPOINT] = $this->assembleEndpointUrl($config[self::WEB_ENDPOINT]);
        }

        $defaultConfig = $this->getManagerDefaultConfig();
        foreach($config[self::PLUGIN_CONFIGURATION] as $param => $value){
            $defaultConfig[$param] = $value;
        }
        $config[self::PLUGIN_CONFIGURATION] = $defaultConfig;

        return $config;
    }

    private function assembleEndpointUrl($endpoint)
    {
        if(empty($endpoint['route'])){
            $endpoint['route'] = $this->getCurrentRoute();
        }
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        return $router->assemble($endpoint['url'], array('name' => $endpoint['route']));
    }

    private function getCurrentRoute()
    {
        return $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getMatchedRouteName();
    }

    public function getManagerDefaultConfig()
    {
        include $this->getLibraryLocation() . '/config.php';
        return $moxieManagerConfig;
    }


//    use ServiceLocatorAwareTrait;
//
//    const CONFIG_SECTION = 'moxiemanager';
//    const CONFIG_LOCATION = 'location';
//    const CONFIG_LOCATION_WEB = 'webLocation';
//    const CONFIG_PRESETS = 'presets';
//    const PRESET_ENDPOINT = 'endpoint';
//
//    public function process($preset)
//    {
//        if (!defined('MOXMAN_ROOT')) {
//            /**
//             * Path to the root of the moxiemanager.
//             *
//             * @package MOXMAN
//             */
//            define('MOXMAN_ROOT', $this->getMoxiemanagerLocation());
//        }
//
//        $GLOBALS['moxieManagerConfig'] = $moxieManagerConfig = $this->getManagerDefaultConfig();
//        $config = $this->getPresetConfig($preset);
//        $config = $config['configuration'];
//        $config['filesystem.rootpath'] = $_SERVER['DOCUMENT_ROOT'] . (substr($config['filesystem.rootpath'], 0, 1) == '/' ? $config['filesystem.rootpath'] : '/'.$config['filesystem.rootpath']);
//        if(!is_dir($config['filesystem.rootpath'])){
//            mkdir($config['filesystem.rootpath'], 0777, true);
//        }
//
//        foreach($config as $option => $value){
//            $GLOBALS['moxieManagerConfig'][$option] = $value;
//        }
//
//        require_once($this->getMoxiemanagerLocation() . '/classes/MOXMAN.php');
//        $context = \MOXMAN_Http_Context::getCurrent();
//        $pluginManager = \MOXMAN::getPluginManager();
//        foreach ($pluginManager->getAll() as $plugin) {
//            if ($plugin instanceof \MOXMAN_Http_IHandler) {
//                $plugin->processRequest($context);
//            }
//        }
//        exit();
//    }
//
//    /**
//     * @return null
//     * @throws Exception
//     */

//

//
//    public function getPresetConfig($preset)
//    {
//        $config = $this->getConfig();
//        if(!array_key_exists($preset, $config[self::CONFIG_PRESETS])){
//            throw new BridgeException('Moxiemanager preset "'.$preset.'" is not configured');
//        }
//        return $config[self::CONFIG_PRESETS][$preset];
//    }
//
//    public function getMoxiemanagerWebLocation()
//    {
//        $config = $this->getConfig();
//        return $config[self::CONFIG_LOCATION_WEB];
//    }
//
//    public function getMoxiemanagerLocation()
//    {
//        $config = $this->getConfig();
//        $lastChar = substr($config[self::CONFIG_LOCATION], -1);
//        return ($lastChar == '/' || $lastChar == '\\') ? substr($config[self::CONFIG_LOCATION],0,-1) : $config[self::CONFIG_LOCATION];
//    }
//
//    public function getConfig()
//    {
//        $config = $this->getServiceLocator()->get('config');
//        return $config[self::CONFIG_SECTION];
//    }
//
//    public function getManagerDefaultConfig()
//    {
//        include $this->getMoxiemanagerLocation() . '/config.php';
//        return $moxieManagerConfig;
//    }
}