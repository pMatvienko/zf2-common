<?php
namespace SbxCommon\Form\Element;

use DoctrineModule\Form\Element\ObjectSelect as DoctrineObjectSelect;

class ObjectSelect extends DoctrineObjectSelect
{
    /**
     * @return Proxy
     */
    public function getProxy()
    {
        if (null === $this->proxy) {
            $this->proxy = new Proxy();
        }
        return $this->proxy;
    }
}