<?php

/**
 * Description of RedisCache
 *
 * @author charles
 */
class RedisCache extends CCache {

    /**
     * @var boolean whether to md5-hash the cache key for normalization purposes. 
     *      Defaults to false. That because in redis we need human readable 
     *      key names. 
     */
    public $hashKey = false;

    /**
     * @var mixed the redis connection component name or the config array, 
     *      when the component name provided, the cache component will try to
     *      get the RedisConnection component, otherwise, it will create the 
     *      component itself. 
     */
    public $connectionConfig;

    /**
     * @var RedisConnection the redis connection component 
     */
    private $_conn = null;

    public function init() {
        parent::init();
        if (is_string($this->connectionConfig)) {
            $this->_conn = Yii::app()->getComponent($this->connectionConfig);
            if (!$this->_conn instanceof RedisCache) {
                throw new CException("RedisCache cannot be created, lack of RedisConnection component.");
            }
        } elseif (is_array($this->connectionConfig)) {
            $this->_conn = Yii::createComponent($this->connectionConfig);
            if (!$this->_conn instanceof RedisCache) {
                throw new CException("RedisCache cannot be created, lack of RedisConnection component.");
            }
            $this->_conn->init();
        } else {
            throw new CException("Unrecognized connection configuration.");
        }
    }

    protected function getValue($key) {
        return $this->_conn->getRedis()->get($key);
    }

    /**
     * Set the value of the key. If the key already exists, then update the value
     * and the expire time of it.
     * @param string $key
     * @param mixed $value
     * @param float $expire
     * @return boolean
     */
    protected function setValue($key, $value, $expire) {
        $ret = $this->_conn->getRedis()->set($key, $value);

        if ($ret && $expire) {
            $this->_conn->getRedis()->expire($key, $expire);
        }

        return $ret;
    }

    /**
     * Add value to redis if the key does not exist, otherwise do nothing
     * @param string $key
     * @param mixed $value
     * @param float $expire
     * @return boolean 
     */
    protected function addValue($key, $value, $expire) {
        $ret = $this->_conn->getRedis()->setnx($key, $value);

        if ($ret && $expire) {
            $this->_conn->getRedis()->expire($key, $expire);
        }

        return $ret;
    }

    /**
     * @param string $key
     * @return boolean if no error happens during deletion
     */
    protected function deleteValue($key) {
        $this->_conn->getRedis()->delete($key);
        return true;
    }

    protected function flushValues() {
        $this->_conn->getRedis()->flushAll();
        return true;
    }

    /**
     * @return RedisConnection
     */
    public function getConnection() {
        return $this->_conn;
    }

}

