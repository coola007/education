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
 * Description of Certificate
 *
 * @author coola
 */
class Application_Model_Certificates extends Application_Model_Abstract {

    /**
     * primary key
     *
     * @var int
     */
    protected $_id = NULL;

    /**
     * length:24
     *
     * @var string
     */
    protected $_title = NULL;

    /**
     * lenght:32
     *
     * @var string
     */
    protected $_name_cn = NULL;

    /**
     * lenght:32
     *
     * @var string
     */
    protected $_name_en = NULL;

    /**
     * lenght:500
     *
     * @var string
     */
    protected $_intro = NULL;

    /**
     * id
     * @return int
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * 标题 seo
     * @return string
     */
    public function getTitle() {
        return $this->_title;
    }

    /**
     * 中文名
     * @return string
     */
    public function getName_cn() {
        return $this->_name_cn;
    }

    /**
     * 英文名
     * @return string
     */
    public function getName_en() {
        return $this->_name_en;
    }

    /**
     * 简介
     * @return string
     */
    public function getIntro() {
        return $this->_intro;
    }

    /**
     * 设置标题
     * @param string $title
     */
    public function setTitle($title) {
        $this->_title = $title;
    }

    /**
     * 设置中文名
     * @param string $name_cn
     */
    public function setName_cn($name_cn) {
        $this->_name_cn = $name_cn;
    }

    /**
     * 设置英文名
     * @param string $name_en
     */
    public function setName_en($name_en) {
        $this->_name_en = $name_en;
    }

    /**
     * 设置简介
     * @param string $intro
     */
    public function setIntro($intro) {
        $this->_intro = $intro;
    }

    /**
     * 返回数组数据
     * @return array
     */
    public function toArray() {
        return array(
            'id'      => $this->_id,
            'title'   => $this->_title,
            'name_cn' => $this->_name_cn,
            'name_en' => $this->name_en,
            'intro'   => $this->_intro,
        );
    }

}
