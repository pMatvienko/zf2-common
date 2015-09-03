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

        if($element instanceof \SbxCommon\Form\Element\RichEditor) {
            $editorConfig = $this->getRIchEditorConfigByPreset($element->getEditorVersion());
            if(empty($editorConfig) || empty($editorConfig['type'])){
                throw new \RuntimeException('RichEditor by name "' . $element->getName() . '", must have a configured editor preset (editorVersion)');
            }
            return $this->renderHelper($editorConfig['type'] . 'FormRichEditor', $element);
        }

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

    private function getRIchEditorConfigByPreset($preset)
    {
        $config = $this->getServiceLocator()->get('config');
        return !empty($config['richeditor']['staticPage']) ? $config['richeditor']['staticPage'] : null;
    }

    private function getServiceLocator()
    {
        return $this->getView()->getHelperPluginManager()->getServiceLocator();
    }

    /**
     * Render element by type map
     *
     * @param ElementInterface $element
     * @return string|null
     */
    protected function renderType(ElementInterface $element)
    {
        $type = $element->getAttribute('type');

        if (isset($this->typeMap[$type])) {
            return $this->renderHelper($this->typeMap[$type], $element);
        }
        return null;
    }

    /**
     * Render element by helper name
     *
     * @param string $name
     * @param ElementInterface $element
     * @return string
     */
    protected function renderHelper($name, ElementInterface $element)
    {
        $helper = $this->getView()->plugin($name);
        return $helper($element);
    }
}