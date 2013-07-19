<?php

/**
 * The Yii component wrapper of the php redis extension.
 *
 * @author charles
 */
class RedisConnection extends CApplicationComponent {

    /**
     * @var string can be a host, or the path to a unix domain socket 
     */
    public $host;

    /**
     * @var int (optional) the port of the redis server 
     */
    public $port = 6379;

    /**
     * @var float value in seconds (optional, default is 0 meaning unlimited) 
     */
    public $timeout = 0;

    /**
     * @var boolean whether or not to use the persistent connection. Set this
     *      option true, the redis connection will not be closed on close() or
     *      end of request until the PHP processor ends. 
     */
    public $persistent = true;

    /**
     * @var string the password used to connect the redis server. Defaul value
     *      false means no password needed. 
     */
    public $auth = false;

    /**
     * @var array the opption should be set to the redis object.
     */
    public $options = array();

    /**
     * @var Redis the object of the PHP redis extension provided.
     */
    private $_redis = null;

    public function __call($name, $parameters) {
        if (method_exists($this->_redis, $name)) {
            return call_user_func_array(array($this->_redis, $name), $parameters);
        }
        parent::__call($name, $parameters);
    }

    public function init() {
        parent::init();

        //the PHP redis extension
        $this->_redis = new Redis();

        if ($this->persistent) {
            $this->_redis->pconnect($this->host, $this->port, $this->timeout);
        } else {
            $this->_redis->connect($this->host, $this->port, $this->timeout);
        }

        if (!empty($this->options)) {
            foreach ($this->options as $key => $value) {
                $this->_redis->setOption($key, $value);
            }
        }
    }

    /**
     * Get the original redis object provided by the phpredis extension.
     * @return Redis
     */
    public function getRedis() {
        if ($this->_redis == null) {
            $this->init();
        }
        return $this->_redis;
    }

    public function close() {
        $this->_redis->close();
    }

}
