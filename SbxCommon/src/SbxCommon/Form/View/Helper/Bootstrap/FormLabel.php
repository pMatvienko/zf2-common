<?php
namespace SbxCommon\Form\View\Helper\Bootstrap;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\LabelAwareInterface;

class FormLabel extends \Zend\Form\View\Helper\FormLabel
{
    /**
     * Generate a form label, optionally with content
     *
     * Always generates a "for" statement, as we cannot assume the form input
     * will be provided in the $labelContent.
     *
     * @param  ElementInterface $element
     * @param  null|string      $labelContent
     * @param  string           $position
     * @throws Exception\DomainException
     * @return string|FormLabel
     */
    public function __invoke(ElementInterface $element = null, $labelContent = null, $position = null)
    {
        if (!$element) {
            return $this;
        }

        $openTag = $this->openTag($element);
        $label   = '';
        if ($labelContent === null || $position !== null) {
            $label = $element->getLabel();
            if (empty($label)) {
                throw new Exception\DomainException(sprintf(
                    '%s expects either label content as the second argument, ' .
                    'or that the element provided has a label attribute; neither found',
                    __METHOD__
                ));
            }

            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }

            if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
                $escapeHtmlHelper = $this->getEscapeHtmlHelper();
                $label = $escapeHtmlHelper($label);
            }
        }
        $label .= ':';
        if ($label && $labelContent) {
            switch ($position) {
                case self::APPEND:
                    $labelContent .= $label;
                    break;
                case self::PREPEND:
                default:
                    $labelContent = $label . $labelContent;
                    break;
            }
        }

        if ($label && null === $labelContent) {
            $labelContent = $label;
        }

        return $openTag . $labelContent . $this->closeTag();
    }
}