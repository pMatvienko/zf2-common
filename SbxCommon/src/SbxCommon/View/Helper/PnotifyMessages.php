<?php
namespace SbxCommon\View\Helper;

use Zend\View\Helper\FlashMessenger;
use Zend\Mvc\Controller\Plugin\FlashMessenger as PluginFlashMessenger;

class PnotifyMessages extends FlashMessenger{
    /**
     * Render Messages
     *
     * @param  array $messages
     * @param  array $classes
     * @return string
     */
    protected function renderMessages($namespace = PluginFlashMessenger::NAMESPACE_DEFAULT, array $messages = array(), array $classes = array())
    {
        // Flatten message array
        $escapeHtml      = $this->getEscapeHtmlHelper();
        $messagesToPrint = array();
        $translator = $this->getTranslator();
        $translatorTextDomain = $this->getTranslatorTextDomain();

        array_walk_recursive($messages, function ($item) use (&$messagesToPrint, $escapeHtml, $translator, $translatorTextDomain, $namespace) {
            if ($translator !== null) {
                $item = $translator->translate(
                    $item,
                    $translatorTextDomain
                );
            }
            $messagesToPrint[] = 'new PNotify({text:"' . $escapeHtml($item) . '", type:"' . $namespace . '"});';
        });

        if (empty($messagesToPrint)) {
            return '';
        }
        return implode("\n", $messagesToPrint);
    }
}