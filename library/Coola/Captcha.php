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

    protected $_code;
    protected $_img;
    protected $_widht;
    protected $_height;
    protected $_format;
    protected $_length;

    public function __construct($width = 100, $height = 40, $format = 'png') {
        $this->_widht = $width;
        $this->_height = $height;
        $this->_format = $format;
        $this->_length = 4;
    }

    public static function init(array $config = array()) {

        if (is_array($config)) {
            foreach ($config as $key => $val) {
                if (isset($this->_{$key})) {
                    $this->_{$key} = $val;
                }
            }
        }

        return $this;
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
    }

    /**
     * 取得随机数
     * @return string
     */
    public function getCode() {
        if (!$this->_code) {
            $this->getRandCode();
        }
        
        return $this->_code;
    }

    /**
     * 随机颜色
     */
    private function randColor(){
        $color = array(
            
        );
    }

}
