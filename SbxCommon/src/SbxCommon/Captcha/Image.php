<?php
namespace SbxCommon\Captcha;

use Zend\Captcha\Image as ZendCaptchaImage;

class Image extends ZendCaptchaImage
{
    /**
     * Get helper name used to render captcha
     *
     * @return string
     */
    public function getHelperName()
    {
        return 'bootstrapcaptcha/image';
    }
}