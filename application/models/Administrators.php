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
 * The model of Administrator
 *
 * @author coola
 */
class Application_Model_Administrators extends Application_Model_Abstract {

    /**
     * primary key
     *
     * @var int
     */
    protected $_id;

    /**
     * administrator for group
     * 
     * @var int
     */
    protected $_group_id;

    /**
     * unique key only for administrator
     *
     * @var string
     */
    protected $_uid;

    /**
     * rand key only for adminstrator
     * 
     * @var string
     */
    protected $_rnd;

    /**
     * name
     *
     * @var string
     */
    protected $_name;

    /**
     * password
     * 
     * @var password
     */
    protected $_pass;

    /**
     * email
     * 
     * @var string
     */
    protected $_email;

    /**
     * group of type
     * @var type
     */
    protected $_type;

    /**
     * last login ip address
     *
     * @var string
     */
    protected $_last_ip;

    /**
     * last login time
     * @var datetime
     */
    protected $_last_login;

    /**
     * create user's time
     *
     * @var datetime
     */
    protected $_create_time;

    /**
     * update user's time;
     * @var datetime
     */
    protected $_update_time;

    /**
     * get administrator_id
     *
     * @return int
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * get administrator_group_id
     * 
     * @return int
     */
    public function getGroup_id() {
        return $this->_group_id;
    }

    /**
     * get administrator unique key
     * 
     * @return string
     */
    public function getUid() {
        return $this->_uid;
    }

    /**
     * get administrator rand key
     * 
     * @return string
     */
    public function getRnd() {
        return $this->_rnd;
    }

    /**
     * get administrator's name
     *
     * @return string
     */
    public function getName() {
        return (string) $this->_name;
    }

    /**
     * get administrator's password
     * 
     * @return string
     */
    public function getPass() {
        return $this->_pass;
    }

    /**
     * get administrator's email
     * 
     * @return string
     */
    public function getEmail() {
        return (string) $this->_email;
    }

    /**
     * get administrator's type
     * 
     * @return string
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * get administrator's login_ip
     * 
     * @return string
     */
    public function getLast_ip() {
        return $this->_last_ip;
    }

    /**
     * get administrator's last login time
     *
     * @return string
     */
    public function getLast_login() {
        return $this->_last_login;
    }

    /**
     * get administrator's create time
     *
     * @return string
     */
    public function getCreate_time() {
        return $this->_create_time;
    }

    /**
     * get administrator's update time
     *
     * @return string
     */
    public function getUpdate_time() {
        return $this->_update_time;
    }

    /**
     * 设置管理用户组
     * @param int $group_id
     */
    public function setGroup_id($group_id) {
        $this->_group_id = $group_id;
    }

    /**
     * 设置唯一值
     * @param string $uid
     */
    public function setUid($uid) {
        $this->_uid = $uid;
    }

    /**
     * 设置随机数
     * @param string $rnd
     */
    public function setRnd($rnd) {
        $this->_rnd = $rnd;
    }

    /**
     * 设置名称
     * @param string $name
     */
    public function setName($name) {
        $this->_name = $name;
    }

    /**
     * 设置密码
     * @param password $pass
     */
    public function setPass($pass) {
        $this->_pass = $pass;
    }

    /**
     * 设置邮箱
     * @param string $email
     */
    public function setEmail($email) {
        $this->_email = $email;
    }

    /**
     * 设置类型
     * @param string $type
     */
    public function setType($type) {
        $this->_type = $type;
    }

    /**
     * 设置最后登录ip
     * @param string $last_ip
     */
    public function setLast_ip($last_ip) {
        $this->_last_ip = $last_ip;
    }

    /**
     * 设置最后登录时间
     * @param datetime $last_login
     */
    public function setLast_login($last_login) {
        $this->_last_login = $last_login;
    }

    /**
     * 设置添加时间
     * @param string $create_time
     */
    public function setCreate_time($create_time) {
        $this->_create_time = $create_time;
    }

    /**
     * 设置更改时间s
     * @param string $update_time
     */
    public function setUpdate_time($update_time) {
        $this->_update_time = $update_time;
    }

    /**
     * 返回数组数据
     * @return array
     */
    public function toArray() {
        return array(
            'id'          => $this->_id,
            //'rnd'         => $this->_rnd,
            'uid'         => $this->_uid,
            'group_id'    => $this->_group_id,
            'name'        => $this->_name,
            'pass'        => $this->_pass,
            'email'       => $this->_email,
            'type'        => $this->_type,
            'last_ip'     => $this->_last_ip,
            'last_login'  => $this->_last_ip,
            'create_time' => $this->_create_time,
            'update_time' => $this->_update_time,
        );
    }

}
