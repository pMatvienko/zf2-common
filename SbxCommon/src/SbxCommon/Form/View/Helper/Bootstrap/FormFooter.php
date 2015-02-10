<?php
namespace SbxCommon\Form\View\Helper\Bootstrap;

use Zend\Form\ElementInterface;
use Zend\Form\Element;
use Zend\Form\View\Helper\FormCollection as ZendFormCollection;

class FormFooter extends ZendFormCollection
{
    protected $defaultElementHelper = 'bootstrap_form_element';

    public function render(ElementInterface $element)
    {
        $markup = '';
        foreach ($element->getIterator() as $elementOrFieldset) {
            if (!($elementOrFieldset instanceof FieldsetInterface)) {
                $markup .= $this->getElementHelper()->render($elementOrFieldset);
            }
        }

        return '<div class="form-group col-lg-12 col-md-12 col-xs-12"><div class="col-xs-6 col-md-7 col-lg-8 col-lg-offset-4 col-lg-offset-5 col-xs-offset-6 btn-group">' . $markup . '</div></div>';
    }
}