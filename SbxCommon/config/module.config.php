<?php
return [
    'view_helpers'    => [
        'invokables' => [
            'bootstrapFormElement'       => 'SbxCommon\Form\View\Helper\Bootstrap\FormElement',
            'tinyFormRichEditor'         => 'SbxCommon\Form\View\Helper\Bootstrap\FormRichEditorTiny',
            'bootstrapFormElementErrors' => 'SbxCommon\Form\View\Helper\Bootstrap\FormElementErrors',
            'bootstrapFormRow'           => 'SbxCommon\Form\View\Helper\Bootstrap\FormRow',
            'bootstrapForm'              => 'SbxCommon\Form\View\Helper\Bootstrap\Form',
            'bootstrapFormCollection'    => 'SbxCommon\Form\View\Helper\Bootstrap\FormCollection',
            'bootstrapFormFooter'        => 'SbxCommon\Form\View\Helper\Bootstrap\FormFooter',
            'bootstrapFormTabbed'        => 'SbxCommon\Form\View\Helper\Bootstrap\FormTabbed',
            'bootstrapFormLabel'         => 'SbxCommon\Form\View\Helper\Bootstrap\FormLabel',
            'bootstrapFormDateSelect'    => 'SbxCommon\Form\View\Helper\Bootstrap\FormDateSelect',
            'bootstrapcaptcha/image'     => 'SbxCommon\Form\View\Helper\Bootstrap\Captcha\Image',
            'flashmessangerPnotify'      => 'SbxCommon\View\Helper\PnotifyMessages',
            'menu'                       => 'SbxCommon\View\Helper\Navigation\Menu',
        ],
    ],
    'service_manager' => [
        'factories'          => [
            'Moxiemanager/Bridge' => 'SbxCommon\Form\Moxiemanager\BridgeFactory',
        ],

    ],
    'form_elements'   => array(
        'invokables' => array(
            'richEditor' => 'SbxCommon\Form\Element\RichEditor'
        )
    ),

    'moxiemanager'    => [
        'location' => 'public/res/lib/moxiemanager',
        'pluginScriptUrl' => '/res/lib/moxiemanager/plugin.js',
        'presets' => array(

        ),
    ],

    'richeditor'      => array(
        'default' => array(
            'type'    => 'tiny',
            'options' => array(
                'theme'        => 'modern',
                'plugins'      => array(
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor colorpicker textpattern"
                ),
//                'external_plugins' => array(
//                    'moxiemanager' => "/res/lib/moxiemanager/plugin.js"
//                ),
                'toolbar1'     => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                'toolbar2'     => "print preview media | forecolor backcolor emoticons",
                'image_advtab' => true
            ),
        )
    )
];
