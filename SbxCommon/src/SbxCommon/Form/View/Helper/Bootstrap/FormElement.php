<?php
namespace SbxCommon\Form\View\Helper\Bootstrap;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormElement as ZendFormElement;

class FormElement extends ZendFormElement
{
    protected $classMap = array(
        'Zend\Form\Element\Button'         => 'formbutton',
        'Zend\Form\Element\Captcha'        => 'formcaptcha',
        'Zend\Form\Element\Csrf'           => 'formhidden',
        'Zend\Form\Element\Collection'     => 'formcollection',
        'Zend\Form\Element\DateTimeSelect' => 'formdatetimeselect',
        'Zend\Form\Element\DateSelect'     => 'bootstrapformdateselect',
        'Zend\Form\Element\MonthSelect'    => 'formmonthselect',
    );

    public function render(ElementInterface $element)
    {
        if (!$element->hasAttribute('id')) {
            $element->setAttribute('id', str_replace(['[', ']'], ['-', ''], $element->getName()));
        }

        $class = $element->getAttribute('class');
        if (!stristr($class, 'form-control') && !in_array($element->getAttribute('type'), ['checkbox', 'radio', 'button', 'submit'])) {
            $element->setAttribute('class', trim('form-control ' . $class));
        }
        elseif(($element->getAttribute('type') == 'button' || $element->getAttribute('type') == 'submit') && !stristr('btn ', $class))
        {
            $element->setAttribute('class', 'btn ' . $class);
        }
        return parent::render($element);
    }

    /**
     * Render element by instance map
     *
     * @param ElementInterface $element
     * @return string|null
     */
    protected function renderInstance(ElementInterface $element)
    {
        foreach ($this->classMap as $class => $pluginName) {
            if ($element instanceof $class) {
                return $this->renderHelper($pluginName, $element);
            }
        }
        return null;
    }
}