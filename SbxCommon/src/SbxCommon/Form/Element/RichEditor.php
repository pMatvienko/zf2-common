<?php
namespace SbxCommon\Form\Element;

class RichEditor extends \Zend\Form\Element\Textarea
{
    /**
     * @return null
     */
    public function getEditorVersion()
    {
        return $this->options['editorVersion'];
    }

}