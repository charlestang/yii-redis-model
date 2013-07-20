<?php

/**
 * Redis hash is kind of hash table and can be used to store small object. This
 * class implement the the CModel interface in Yii framework, which help people
 * to use redis hash like use a database table.
 *
 * @author charles
 */
abstract class RedisHashModel extends CModel {

    /**
     * @var RedisHashMetaData 
     */
    private $_md;

    /**
     * @var array 
     */
    private static $_models;
    private $_attributes;
    private $_new;
    private $_id = null;

    /**
     * The key prefix of the redis hash.
     * 
     * This key prefix should be colon ended, like "users:"
     *  
     * @return string the virtual table name, which should be a key prefix ended
     *         with colon
     */
    abstract function getKeyNamePrefix();

    /**
     * If the object's property should have its default value, this method 
     * should be overwritten.
     * 
     * @return array
     */
    public function defaultValues() {
        return array();
    }

    public function behaviors() {
        return array();
    }

    public function init() {
        
    }

    /**
     * If the object can be searched by any field, keys should be specified by
     * this method.
     * @return array
     */
    public function indices() {
        return array();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        }

        return parent::__get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        if (isset($this->_attributes[$name])) {
            $this->_attributes[$name] = $value;
        }

        parent::__set($name, $value);
    }

    /**
     * Constructor
     * @param string $scenario
     * @return RedisHashModel
     */
    public function __construct($scenario = 'insert') {
        if ($scenario == null) {
            return;
        }

        $this->setScenario($scenario);
        $this->setIsNewRecord(true);
        $this->_attributes = $this->getMetaData()->attributeDetaults;

        $this->init();

        $this->attachBehaviors($this->behaviors());
        $this->afterConstruct();
    }

    /**
     * 
     * @param RedisHashModel $class
     */
    public static function model($class = __CLASS__) {
        if (isset(self::$_models[$class])) {
            return self::$_models[$class];
        } else {
            $model = self::$_models[$class] = new $class;
            $model->_md = new RedisHashMetaData($model);
            $model->attachBehaviors($model->behaviors());
            return $model;
        }
    }

    /**
     * @return RedisHashMetaData
     */
    public function getMetaData() {
        if ($this->_md == null) {
            $this->_md = self::$_models[get_class($this)]->_md;
        }
        return $this->_md;
    }

    protected function afterConstruct() {
        if ($this->hasEventHandler('onAfterConstruct'))
            $this->onAfterConstruct(new CEvent($this));
    }

    public function setIsNewRecord($value) {
        $this->_new = $value;
    }

    public function getId() {
        return $this->_id;
    }

}

/**
 * This is the metadata of the redis hash model.
 */
class RedisHashMetaData {

    /**
     * @var string the virtual table name of the object storage, which is a redis hash 
     */
    public $keyNamePrefix;

    /**
     * @var array the properties' names of the object
     */
    public $properties;

    /**
     * @var array the default values of each property 
     */
    public $attributeDetaults = array();

    /**
     * @var RedisHashModel 
     */
    private $_model;

    /**
     * Constructor
     * @param RedisHashModel $model
     */
    public function __construct($model) {
        $this->_model = $model;
        $this->keyNamePrefix = $model->getKeyNamePrefix();
        $this->properties = $model->attributeNames();
        $this->attributeDetaults = $model->defaultValues();

        //make all the property has the key in defauls array
        foreach ($this->properties as $property) {
            if (!isset($this->attributeDetaults[$property])) {
                $this->attributeDetaults[$property] = null;
            }
        }
    }

}