<?php
namespace SbxCommon\Form\View\Helper\Bootstrap;

use SbxCommon\Form\Element\RichEditor;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormElement as ZendFormElement;

class FormRichEditorTiny extends ZendFormElement
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

        return parent::render($element).'<script type="text/javascript">' . $this->getEditorInitScript($element) . '</script>';
    }

    private function getEditorInitScript(RichEditor $element)
    {
        $initScript = '';
        $editorConfig = $this->getRIchEditorConfigByPreset($element->getEditorVersion());
        if(empty($editorConfig) || empty($editorConfig['options'])){
            throw new \RuntimeException('RichEditor by name "' . $element->getName() . '", must have a configured editor preset (editorVersion)');
        }

        if(!empty($editorConfig['moxiemanager'])){
            $bridge = $this->getServiceLocator()->get('Moxiemanager/Bridge');
            $editorConfig['options']['external_plugins']['moxiemanager'] = $bridge->getLibraryPluginScriptUrl();
            $initScript = 'window.moxiemanagerApiEndpoint = "' . $bridge->getPreset($editorConfig['moxiemanager'])->getEndpoint() . '";';
        }

        $editorConfig = $editorConfig['options'];
        $editorConfig['selector'] = '#'.$element->getAttribute('id');
        $editorConfig['language'] = substr(\Locale::getDefault(), 0, 2);
        $initScript .= 'tinymce.init(' . \Zend\Json\Encoder::encode($editorConfig) . ')';
        return $initScript;
    }

    private function getRIchEditorConfigByPreset($preset)
    {
        $config = $this->getServiceLocator()->get('config');
        return !empty($config['richeditor'][$preset]) ? $config['richeditor'][$preset] : null;
    }

    private function getServiceLocator()
    {
        return $this->getView()->getHelperPluginManager()->getServiceLocator();
    }
}