<?php
namespace SbxCommon\Form\View\Helper\Bootstrap;

use Zend\Form\View\Helper\Form as ZendForm;
use Zend\Form\FormInterface;
use Zend\Form\FieldsetInterface;

class Form extends ZendForm
{
    /**
     * Render a form from the provided $form,
     *
     * @param FormInterface $form
     *
     * @return string
     */
    public function render(FormInterface $form)
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $formContent = '';

        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->bootstrapFormCollection($element);
            } else {
                $formContent.= $this->getView()->bootstrapFormRow($element);
            }
        }

        return $this->openTag($form) . $formContent . $this->closeTag();
    }

}