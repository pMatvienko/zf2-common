<?php
namespace SbxCommon\Form\View\Helper\Bootstrap;

use SbxCommon\Form\FooterFieldset;
use Zend\Form\ElementInterface;
use Zend\Form\Element;
use Zend\Form\View\Helper\FormCollection as ZendFormCollection;

class FormCollection extends ZendFormCollection
{
    protected $defaultElementHelper = 'bootstrapformrow';
    protected $footerCollectionHelper = 'bootstrapformfooter';

    public function render(ElementInterface $element)
    {
        if($element instanceof FooterFieldset){
            return $this->view->plugin($this->footerCollectionHelper)->render($element);
        }
        return parent::render($element);
    }
}