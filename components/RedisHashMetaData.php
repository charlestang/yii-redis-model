<?php
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
