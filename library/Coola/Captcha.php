<?php

/*
 *  Copyright (c) <2014> <霖默醉语>
 * 
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  version 1.0
 */

/**
 * validecode of Captcha
 *
 * @author coola
 */
define('codeType', 'DEF');

class Coola_Captcha {

    private $_code;
    private $_img;
    private $_widht;
    private $_height;
    private $_format;
    private $_length;
    private $_image;
    private $_size = 24;
    private $_font;
    private $_fontDir;

    /**
     * 是否开启干扰
     * 0 为关闭  1为加点  2为加圆弧  3加线条  4混色  5英文与数字中是否整齐排列
     * @var int
     */
    private $_disturb = 1;

    /**
     * 干扰数量
     * @var int 
     */
    private $_disturbNum = 5;

    public function __construct($width = 100, $height = 40, $format = 'png') {
        session_start();
        $this->_widht = $width;
        $this->_height = $height;
        $this->_format = $format;
        $this->_length = 4;
        $this->_image = imagecreate($this->_widht, $this->_height);
    }

    /**
     * 生成图片
     */
    public function image() {
        $this->getCode();
        $this->backgroud();
        $this->addDisturb();
        $this->addWordEn();
    }

    /**
     * 取得随机字符串
     */
    private function getRandCode() {
        $chars = array(
            'DEF' => 'ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789',
            'ALL' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
            'ENL' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
            'ENB' => 'abcdefghijklmnopqrstuvwxyz',
            'ENA' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'EAD' => 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789',
            'EBD' => 'abcdefghijkmnpqrstuvwxyz123456789',
            'DIG' => '0123456789',
        );

        $string = "";
        while (strlen($string) < $this->_length) {
            $string .= substr($chars[codeType], (mt_rand() % strlen($chars[codeType])), 1);
        }

        $this->_code = $string;

        return $string;
    }

    /**
     * 取得随机数
     * @return string
     */
    public function getCode() {
        if (!$this->_code) {
            $_SESSION['test_captcha'] = date('H:i:s');
            $this->getRandCode();
        }

        return $this->_code;
    }

    /**
     * 随机颜色
     */
    private function randColor($start = 220, $end = 255) {
        return array(
            rand($start, $end), rand($start, $end), rand($start, $end)
        );
    }

    /**
     * 取得随机颜色
     * @param int $start
     * @param int $end
     * @return resource
     */
    private function getRandColor($start = 220, $end = 255) {
        $color = $this->randColor($start, $end);
        return imagecolorallocate($this->_image, $color[0], $color[1], $color[2]);
    }

    /**
     * 返回字体目录
     * @return string
     */
    private function getFontDir() {
        if (!$this->_fontDir) {
            $this->_fontDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fonts';
        }

        return $this->_fontDir;
    }

    /**
     * 获取字体
     * @return string
     */
    private function getFont() {
        $fontDir = $this->getFontDir();
        //文字目录下的文件
        $fonts = array(
            'AHGBold', 'AntykwaBold', 'Candice', 'Ding-DongDaddy',
            'Duality', 'Heineken', 'Jura', 'StayPuft', 'VeraSansBold'
        );

        $this->_font = $fontDir . DIRECTORY_SEPARATOR . $fonts[mt_rand(0, 8)] . '.ttf';

        return $this->_font;
    }

    /**
     * 返回文字大小
     * @return array
     */
    private function getFontSize() {
        return array('min' => $this->_height * 0.4, 'max' => $this->_height * 0.6);
    }

    /**
     * 增加背景色
     */
    private function backgroud() {
        if ($this->_image) {
            $bgColor = $this->getRandColor();
            imagefill($this->_image, 0, 0, $bgColor);
            imagecolortransparent($this->_image, $bgColor);
            unset($bgColor);
        }
    }

    /**
     * 添加干扰
     */
    private function addDisturb() {
        //添加噪点
        $this->addPixel($this->getRandColor(100, 255), 100);
        $this->addLine($this->getRandColor(100, 200), 4);
//        $this->addSnow($this->getRandColor(150, 205));
    }

    private function addSnow($color, $num = 50) {
        for ($i = 0; $i < $num; $i++) {
            imagestring($this->_image, mt_rand(1, 5), mt_rand(0, $this->_widht), mt_rand(0, $this->_height), '*', $color);
        }
        unset($color);
    }

    private function addArc($color, $num = 20) {

    }

    /**
     * 添加线条
     * @param imagecoolorallocate $color
     * @param int $num
     */
    private function addLine($color, $num = 10) {
        $num = $num > 0 ? $num : 20;

        for ($i = 0; $i < $num; $i ++) {
            imageline($this->_image, mt_rand($this->_widht * 0.01, $this->_widht * 0.9), mt_rand($this->_height * 0.01, $this->_height * 0.9), mt_rand($this->_widht * 0.2, $this->_widht * 0.99), mt_rand($this->_height * 0.2, $this->_height * 0.99), $color);
        }

        unset($color);
    }

    /**
     * 添加噪点
     * @param imagecolorallocate $color
     * @param int $num
     */
    private function addPixel($color, $num = 50) {
        $num = $num > 0 ? $num : 100;
        for ($i = 0; $i < $num; $i++) {
            imagesetpixel($this->_image, rand(0, $this->_widht), rand(0, $this->_height), $color);
        }

        unset($color);
    }

    /**
     * 添加英文单词
     */
    private function addWordEn() {
        $_SESSION['coola_captcha'] = $this->_code;
        if (!$fontSize = $this->_size) {
            $size = $this->getFontSize();
            $fontSize = mt_rand($size['min'], $size['max']);
        }
        $font = $this->getFont();
        $x = $this->_widht / $this->_length;
        for ($i = 0; $i < $this->_length; $i++) {
            $color = $this->getRandColor(0, 160);
            imagettftext($this->_image, $fontSize, mt_rand(-30, 30), $x * $i + mt_rand(1, 5), $this->_height / 1.3, $color, $font, $this->_code[$i]);
            unset($color);
        }
    }

    public function show() {
        $this->image();
        $create_image = array(
            'png'  => 'imagepng',
            'jpeg' => 'imagejpeg',
            'jpg'  => 'imagejpeg',
            'wbmp' => 'imagewbmp'
        );
        $headerType = array(
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'jpeg' => 'image/jpeg',
            'jpg'  => 'image/jpeg',
            'wbmp' => 'image/vnd.wap.wbmp'
        );
        header("Content-type: {$headerType[$this->_format]}");
        $create_image[$this->_format]($this->_image);
        imagedestroy($this->_image);
    }

    /**
     * 验证码验证
     * @param string $code
     * @return boolean
     */
    public function isValid($code) {
        return strcasecmp($code, $_SESSION['coola_captcha']) == 0 ? TRUE : FALSE;
    }

}
