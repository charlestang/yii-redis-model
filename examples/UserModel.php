<?php

/**
 * This is an example to show how to use the RedisHashModel.
 * 
 * The class will implement a common web site user model.
 * It will save some user meta data to redis like what you 
 * have done to MySQL table.
 *
 * @author Charles Tang<charlestang@foxmail.com>
 */
class UserModel extends RedisHashModel {

    public $password_repeat;

    public function attributeNames() {
        return array(
            'id',
            'account',
            'password',
            'name',
            'gender',
            'email',
            'register_time',
        );
    }

    public function attributeLabels() {
        return array(
            'id'            => 'User\'s ID',
            'account'       => 'Login name',
            'password'      => 'Password',
            'name'          => 'The real name',
            'gender'        => 'The gender',
            'email'         => 'The mail box',
            'register_time' => 'The time user register the account',
        );
    }

    public function defaultValues() {
        return array(
            'gender' => 'male',
        );
    }

    /**
     * This method return the key prefix of the redis hash.
     * Like what you did in CActiveRecord::tableName()
     */
    public function getKeyNamePrefix() {
        return 'user:';
    }

    public function rules() {
        return array(
            array('id', 'numerical', 'integerOnly' => true),
            array('password', 'compare'),
            array('account, name', 'length', 'max' => 20),
            array('email', 'email'),
            array('id, account, password, email', 'required'),
        );
    }

}
