<?php
namespace SbxCommon\Form;

use Zend\Form\Fieldset;

class TabbedFieldset extends Fieldset
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }
}