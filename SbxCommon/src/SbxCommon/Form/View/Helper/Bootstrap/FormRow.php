<?php
namespace SbxCommon\Form\View\Helper\Bootstrap;

use Zend\Form\View\Helper\FormRow as ZendFormRow;
use Zend\Form\ElementInterface;
class FormRow extends ZendFormRow
{
    /**
     * The class that is added to element that have errors
     *
     * @var string
     */
    protected $inputErrorClass = 'input-error';

    protected function getElementLabel(ElementInterface $element){
        $label = $element->getLabel();

        if (!empty($label)) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }
        }
        return $label;
    }

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param  ElementInterface $element
     *
     * @throws \Zend\Form\Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $labelHelper = $this->getLabelHelper();
        $elementHelper = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();
        $type = $element->getAttribute('type');

        if($type == 'button' || $type == 'submit') {
            $label = $this->getElementLabel($element);
            $element->setValue($label);
        }

        if ($this->renderErrors) {
            $elementErrors = $elementErrorsHelper->render($element, ['class' => 'form-errors col-xs-6 col-md-7 col-md-offset-5 col-lg-8 col-lg-offset-4']);
        }


        $elementString = $elementHelper->render($element);
        $controlElementWrapperClass = 'col-xs-7 col-md-8 col-lg-9';

        if($element instanceof \Zend\Form\Element\DateSelect) {
            $controlElementWrapperClass .= ' form-inline';
        }

        if ($type !== 'hidden') {

            if ($type === 'multi_checkbox' || $type === 'radio' || $element instanceof \Zend\Form\Element\MonthSelect) {

                $element->setLabelAttributes(['class' => 'col-lg-3 col-xs-5 col-md-4 control-label']);
                $markup = $labelHelper($element) . '<div class="' . $controlElementWrapperClass . '">
                    <div class="btn-group" data-toggle="buttons">' . $elementString . '</div></div>';

            } elseif($type == 'button' || $type == 'submit') {
                $markup = '<div class="' . $controlElementWrapperClass . ' col-md-offset-4 col-lg-offset-3">' . $elementString . '</div>';
            } else {
                switch ($this->labelPosition) {
                    case self::LABEL_APPEND:
                        $element->setLabelAttributes(['class' => 'col-lg-3 col-xs-5 col-md-4 control-label']);
                        $markup = '<div class="' . $controlElementWrapperClass . '">' . $elementString . '</div>' . $labelHelper($element);
                        break;
                    case self::LABEL_PREPEND:
                    default:
                        $element->setLabelAttributes(['class' => 'col-lg-3 col-xs-5 col-md-4 control-label']);
                        $markup = $labelHelper($element) . '<div class="' . $controlElementWrapperClass . '">' . $elementString . '</div>';
                        break;
                }
            }

            $rowClass = 'form-group col-lg-12 col-md-12 col-xs-12';
            if ($this->renderErrors && !empty($elementErrors)) {
                $markup .= $elementErrors;
                $rowClass .= ' has-error';
            }
            $markup = '<div class="row"><div class="' . $rowClass . '">' . $markup . '</div></div>';
        } else {
            if ($this->renderErrors) {
                $markup = $elementString . $elementErrors;
            } else {
                $markup = $elementString;
            }
        }
        return $markup;
    }

    /**
     * Retrieve the FormElement helper
     *
     * @return FormElement
     */
    protected function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('bootstrap_form_element');
        }

        if (!$this->elementHelper instanceof FormElement) {
            $this->elementHelper = new FormElement();
        }

        return $this->elementHelper;
    }

    /**
     * Retrieve the FormElementErrors helper
     *
     * @return FormElementErrors
     */
    protected function getElementErrorsHelper()
    {
        if ($this->elementErrorsHelper) {
            return $this->elementErrorsHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementErrorsHelper = $this->view->plugin('bootstrap_form_element_errors');
        }

        if (!$this->elementErrorsHelper instanceof FormElementErrors) {
            $this->elementErrorsHelper = new FormElementErrors();
        }

        return $this->elementErrorsHelper;
    }

    protected function getLabelHelper()
    {
        if ($this->labelHelper) {
            return $this->labelHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->labelHelper = $this->view->plugin('bootstrap_form_label');
        }

        if (!$this->labelHelper instanceof FormLabel) {
            $this->labelHelper = new FormLabel();
        }

        if ($this->hasTranslator()) {
            $this->labelHelper->setTranslator(
                $this->getTranslator(),
                $this->getTranslatorTextDomain()
            );
        }

        return $this->labelHelper;
    }
}