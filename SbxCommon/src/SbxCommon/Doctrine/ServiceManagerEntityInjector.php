<?php
namespace SbxCommon\Doctrine;

use Zend\ServiceManager\ServiceManager;

class ServiceManagerEntityInjector
{
    protected $sm;

    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    public function postLoad($eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $class = new \ReflectionClass($entity);
        if ($class->implementsInterface('Zend\ServiceManager\ServiceLocatorAwareInterface')) {
            $entity->setServiceLocator($this->sm);
        }
    }
}