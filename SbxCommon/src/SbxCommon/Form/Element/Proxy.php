<?php
namespace SbxCommon\Form\Element;

use RuntimeException;

class Proxy extends \DoctrineModule\Form\Element\Proxy
{
    private $groupProperty;
    private $propertyPartsConcatBy = ' ';
    private $targetOrderBy = null;

    public function setOptions($options)
    {
        if (isset($options['group_property'])) {
            if(is_string($options['group_property'])){
                $options['group_property'] = array($options['group_property']);
            }
            $this->setGroupProperty($options['group_property']);
        }
//
        if (isset($options['property_parts_concat_by'])) {
            $this->setPropertyPartsConcatBy($options['property_parts_concat_by']);
        }

        if (isset($options['target_order_by'])) {
            $this->setTargetOrderBy($options['target_order_by']);
        }

        if (isset($options['property'])) {
            if(!is_array($options['property']))
            {
                $options['property'] = explode(',', $options['property']);
            }
            $this->setProperty($options['property']);
        }
        return parent::setOptions($options);
    }

    /**
     * @return null
     */
    public function getTargetOrderBy()
    {
        return $this->targetOrderBy;
    }

    /**
     * @param null $targetOrderBy
     * @return $this
     */
    public function setTargetOrderBy($targetOrderBy)
    {
        $this->targetOrderBy = $targetOrderBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getPropertyPartsConcatBy()
    {
        return $this->propertyPartsConcatBy;
    }

    /**
     * @param string $propertyPartsConcatBy
     * @return $this
     */
    public function setPropertyPartsConcatBy($propertyPartsConcatBy)
    {
        $this->propertyPartsConcatBy = $propertyPartsConcatBy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGroupProperty()
    {
        return $this->groupProperty;
    }

    /**
     * @param mixed $groupProperty
     * @return $this
     */
    public function setGroupProperty($groupProperty)
    {
        $this->groupProperty = $groupProperty;
        return $this;
    }

    /**
     * Load value options
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function loadValueOptions()
    {
        if (!($om = $this->objectManager)) {
            throw new RuntimeException('No object manager was set');
        }

        if (!($targetClass = $this->targetClass)) {
            throw new RuntimeException('No target class was set');
        }

        $metadata   = $om->getClassMetadata($targetClass);
        $identifier = $metadata->getIdentifierFieldNames();
        $objects    = $this->getObjects();
        $options    = array();

        if ($this->displayEmptyItem || empty($objects)) {
            $options[] = $this->getEmptyItemLabel();
        }

        if (!empty($objects)) {
            foreach ($objects as $key => $object) {
                if (null !== ($generatedLabel = $this->generateLabel($object))) {
                    $label = $generatedLabel;
                } elseif ($property = $this->property) {
                    $label = $this->getPropertyValue($property, $object, $metadata);
                } else {
                    if (!is_callable(array($object, '__toString'))) {
                        throw new RuntimeException(
                            sprintf(
                                '%s must have a "__toString()" method defined if you have not set a property'
                                . ' or method to use.',
                                $targetClass
                            )
                        );
                    }

                    $label = (string) $object;
                }

                if (count($identifier) > 1) {
                    $value = $key;
                } else {
                    $value = current($metadata->getIdentifierValues($object));
                }
                $groupProperties = $this->getGroupProperty();
                if(empty($groupProperties)) {
                    $options[$value] = $label;
                }else{
//                    $groupLabel = array();
//                    foreach($groupProperties as $prop){
//                        $groupLabel[] = $this->getPropertyValue($prop, $object, $metadata);
//                    }
//                    $groupLabel = implode($this->getPropertyPartsConcatBy(), $groupLabel);
                    $groupLabel =  $this->getPropertyValue($groupProperties, $object, $metadata);
                    $options[$groupLabel]['label'] = $groupLabel;
                    $options[$groupLabel]['options'][$value] = $label;
                }
            }
        }

        $this->valueOptions = $options;
    }

    private function getPropertyValue($properties, $object, $metadata)
    {
        $targetClass = $this->targetClass;
        $values = array();
        foreach($properties as $property){
            if ($this->isMethod == false && !$metadata->hasField($property)) {
                throw new RuntimeException(
                    sprintf(
                        'Property "%s" could not be found in object "%s"',
                        $property,
                        $targetClass
                    )
                );
            }

            $getter = 'get' . ucfirst($property);
            if (!is_callable(array($object, $getter))) {
                throw new RuntimeException(
                    sprintf('Method "%s::%s" is not callable', $this->targetClass, $getter)
                );
            }
            $values[] = $object->{$getter}();
        }


        return implode($this->getPropertyPartsConcatBy(), $values);
    }

    protected function loadObjects()
    {
        if (!empty($this->objects)) {
            return;
        }

        $findMethod = (array) $this->getFindMethod();
        if (!$findMethod) {
            if($this->getTargetOrderBy() == null) {
                $this->objects = $this->objectManager->getRepository($this->targetClass)->findAll();
            } else {
                $this->objects = $this->objectManager->getRepository($this->targetClass)->findBy(array(), $this->getTargetOrderBy());
            }

        } else {
            if (!isset($findMethod['name'])) {
                throw new RuntimeException('No method name was set');
            }
            $findMethodName   = $findMethod['name'];
            $findMethodParams = isset($findMethod['params']) ? array_change_key_case($findMethod['params']) : array();

            $repository = $this->objectManager->getRepository($this->targetClass);
            if (!method_exists($repository, $findMethodName)) {
                throw new RuntimeException(
                    sprintf(
                        'Method "%s" could not be found in repository "%s"',
                        $findMethodName,
                        get_class($repository)
                    )
                );
            }

            $r    = new ReflectionMethod($repository, $findMethodName);
            $args = array();
            foreach ($r->getParameters() as $param) {
                if (array_key_exists(strtolower($param->getName()), $findMethodParams)) {
                    $args[] = $findMethodParams[strtolower($param->getName())];
                } else {
                    $args[] = $param->getDefaultValue();
                }
            }
            $this->objects = $r->invokeArgs($repository, $args);
        }
    }
}