<?php
return [
    'view_helpers'    => [
        'invokables' => [
            'bootstrapFormElement'       => 'SbxCommon\Form\View\Helper\Bootstrap\FormElement',
            'bootstrapFormElementErrors' => 'SbxCommon\Form\View\Helper\Bootstrap\FormElementErrors',
            'bootstrapFormRow'           => 'SbxCommon\Form\View\Helper\Bootstrap\FormRow',
            'bootstrapForm'              => 'SbxCommon\Form\View\Helper\Bootstrap\Form',
            'bootstrapFormCollection'    => 'SbxCommon\Form\View\Helper\Bootstrap\FormCollection',
            'bootstrapFormFooter'    => 'SbxCommon\Form\View\Helper\Bootstrap\FormFooter',
            'bootstrapFormLabel'         => 'SbxCommon\Form\View\Helper\Bootstrap\FormLabel',
            'bootstrapFormDateSelect'    => 'SbxCommon\Form\View\Helper\Bootstrap\FormDateSelect',

            'flashmessangerPnotify'      => 'SbxCommon\View\Helper\PnotifyMessages',
            'menu' => 'SbxCommon\View\Helper\Navigation\Menu'
        ],
    ],
];
