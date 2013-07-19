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
        $this->_conn->getRedis()->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param float $expire
     */
    protected function setValue($key, $value, $expire) {
        $this->_conn->getRedis()->set($key, $value, $expire);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param float $expire
     */
    protected function addValue($key, $value, $expire) {
        $this->setValue($key, $value, $expire);
    }

    protected function deleteValue($key) {
        $this->_conn->getRedis()->del($key);
    }

    protected function flushValues() {
        $this->_conn->getRedis()->flushAll();
    }

    /**
     * @return RedisConnection
     */
    public function getConnection() {
        return $this->_conn;
    }
}

