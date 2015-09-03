<?php
namespace SbxCommon\Form\Moxiemanager;


class Preset
{
    private $name = null;
    private $endpoint = null;
    private $configuration = null;
    private $moxmanLocation = null;

    public function __construct($options = array())
    {
        foreach ($options as $option => $value) {
            $setter = 'set' . ucfirst($option);
            if (method_exists(__CLASS__, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param null $endpoint
     *
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @return null
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param null $configuration
     *
     * @return $this
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @param $param
     * @param $value
     *
     * @return $this
     */
    public function setConfigurationProperty($param, $value)
    {
        $this->configuration[$param] = $value;

        return $this;
    }


    public function process()
    {
        $GLOBALS['moxieManagerConfig'] = $moxieManagerConfig = $this->getConfiguration();

        require($this->getMoxmanLocation());
        $context = \MOXMAN_Http_Context::getCurrent();
        $pluginManager = \MOXMAN::getPluginManager();
        foreach ($pluginManager->getAll() as $plugin) {
            if ($plugin instanceof \MOXMAN_Http_IHandler) {
                $plugin->processRequest($context);
            }
        }
    }

    /**
     * @return null
     */
    public function getMoxmanLocation()
    {
        return $this->moxmanLocation;
    }

    /**
     * @param null $moxmanLocation
     *
     * @return $this
     */
    public function setMoxmanLocation($moxmanLocation)
    {
        $this->moxmanLocation = $moxmanLocation;
        return $this;
    }
}