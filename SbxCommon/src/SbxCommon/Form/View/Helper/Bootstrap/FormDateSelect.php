<?php
namespace SbxCommon\Form\View\Helper\Bootstrap;

use Zend\Form\ElementInterface;
use Zend\Form\Element\DateSelect;
use Zend\Form\View\Helper\FormDateSelect as ZendFormDateSelect;


class FormDateSelect extends ZendFormDateSelect
{
    /**
     * @param DateSelect $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $elements = array(
            'day' => $element->getDayElement(),
            'moth'=> $element->getMonthElement(),
            'year'=> $element->getYearElement()
        );

        foreach($elements as $item)
        {
            $class = $item->getAttribute('class');
            if (!stristr($class, 'form-control')) {
                $item->setAttribute('class', trim('form-control ' . $class));
            }
        }
        return parent::render($element);
    }
}