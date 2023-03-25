<?php

class CaptchaModel extends baseModel
{

    private $image_resource = null;
    public $image_type = 'image/png';
    private $chars = '0123456789ABCDEFGHJKLMNPRSTUVWXY'; //字符串，去了掉容易混淆的字符
    public $font = 'captcha.ttf';
    private $captcha = '';
    private $captcha_id = '';

    public function startImageCaptcha($captcha = '')
    {
        if (!empty($captcha_id)) {
            $this->captcha_id = $captcha_id;
        }
        $width = 120;
        $height = 40;
        $this->image_resource = imagecreatetruecolor($width, $height);

        $white_color = imagecolorallocate($this->image_resource, mt_rand(250, 255), mt_rand(250, 255), mt_rand(250, 255)); //分配白色
        $black_color = imagecolorallocate($this->image_resource, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100)); //分配黑色
        imagefilledrectangle($this->image_resource, 0, 0, $width, $height, $white_color); //画布背景颜色

        for ($i = 0; $i < 100; $i++) {
            imagechar($this->image_resource, 2, mt_rand(0, $width), mt_rand(0, $height), '.', $black_color);
        }
        $font_ttf_file = $this->getFontFile();
        $this->getCaptcha();
        for ($i = 0; $i < 5; $i++) {
            //$rand_color = imagecolorallocate($this->image_resource, mt_rand(100, 225), mt_rand(100, 225), mt_rand(100, 225));
            //imagestring($this->image_resource, 3, mt_rand(0, $width), mt_rand(0, $height), $this->captcha, $rand_color);
        }
        //写入验证码

        $font_size = $height / 2;
        $x = $font_size / 1.5;
        $y = $height - 5;
        for ($i = 0; $i < strlen($this->captcha); $i++) {
            $angle = mt_rand(-5, 5);
            imagettftext($this->image_resource, $font_size, $angle, $x + $i * $x * 2, $y + mt_rand(-3, 3), $black_color, $font_ttf_file, substr($this->captcha, $i, 1));
        }

        //写入一条干扰直线 如果要增加条数直接复制即可
        imageline($this->image_resource, mt_rand(0, 10), mt_rand(0, $height), mt_rand(10, $width), mt_rand(10, $height), $black_color);
        imageline($this->image_resource, mt_rand(0, 10), mt_rand(0, $height), mt_rand(10, $width), mt_rand(10, $height), $black_color);
        imageline($this->image_resource, mt_rand(0, 10), mt_rand(0, $height), mt_rand(10, $width), mt_rand(10, $height), $black_color);
        imageline($this->image_resource, mt_rand(0, 10), mt_rand(0, $height), mt_rand(10, $width), mt_rand(10, $height), $black_color);
        imageline($this->image_resource, mt_rand(0, 10), mt_rand(0, $height), mt_rand(10, $width), mt_rand(10, $height), $black_color);
    }

    public function getCaptcha()
    {
        $captcha = null;
        $length = mt_rand(3, 5);
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($this->chars, mt_rand(0, strlen($this->chars)), 1);
            $captcha = $captcha . $char;
        }
        $session_cmp = framework::cm()->get('com.session');
        if (!empty($this->captcha_id)) {
            $session_cmp->setId($this->captcha_id);
        }
        $session_cmp->start();
        $session_cmp->set('captcha', $captcha);
        $this->captcha = $captcha;
    }

    public function validateCaptcha($input = null, $captcha_id = '')
    {
        $return = false;
        $session_cmp = framework::cm()->get('com.session');
        if (!empty($captcha_id)) {
            $session_cmp->setId($captcha_id);
        }
        $session_cmp->start();
        $session_captcha = $session_cmp->get('captcha');
        if (!is_null($session_captcha) && !is_null($input)) {
            if (strtolower($input) === strtolower($session_captcha)) {
                $return = true;
            }

        }
        $session_cmp->set('captcha', null);
        return $return;
    }

    public function getFontFile()
    {
        $font_path = DIR_FRAMEWORK . DS . 'fonts';
        $font_file = $font_path . DS . $this->font;
        return $font_file;
    }

    public function getImageCaptchaContent()
    {
        ob_start();
        imagepng($this->image_resource);
        $image_content = ob_get_contents();
        ob_end_clean();
        imagedestroy($this->image_resource);
        return $image_content;
    }

}
