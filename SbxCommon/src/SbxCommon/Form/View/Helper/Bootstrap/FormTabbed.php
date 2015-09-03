<?php
namespace SbxCommon\Form\View\Helper\Bootstrap;

use Zend\Form\ElementInterface;
use Zend\Form\Element;
//use Zend\Form\View\Helper\FormCollection as ZendFormCollection;

class FormTabbed extends FormCollection
{
    protected $formCollectionHelper = 'bootstrapFormCollection';
    protected $wrapper = '<div role="tabpanel" class="tab-pane" %4$s>%1$s%3$s</div>';

    public function render(ElementInterface $element)
    {
        $fieldsets = array();
        $tabs = array();
        $index = 0;

        /**
         * @var \Zend\Form\Fieldset $fst
         */
        foreach($element as $fst){
            if(null == $fst->getAttribute('id')){
                $fst->setAttribute('id', uniqid());
            }

            $label = $fst->getLabel();
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }

            $tabs[] = '<li role="presentation" class="' . (!empty($fst->getMessages()) ? 'has-error': '') . ($index == 0 ? ' active' : '') . '">
                <a href="#'.$fst->getAttribute('id').'" aria-controls="'.$fst->getAttribute('id').'" role="tab" data-toggle="tab">' . $label . '</a>
            </li>';
            $itemHtml = parent::render($fst);
            if($index == 0){
                $itemHtml = str_replace('class="tab-pane"', 'class="tab-pane active"', $itemHtml);
            }
            $fieldsets[] = $itemHtml;
            ++$index;
        }


        return '
            <ul class="nav nav-tabs" role="tablist">' . implode('',$tabs) . '</ul>
            <div class="tab-content tabbed-form-content">' . implode('',$fieldsets) . '</div>
        ';
    }
}