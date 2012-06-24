<?php 
/**
**  @file Model.php
**  @author Dregian
**  @date 2012-06-24
**/
namespace ORM;

class Model 
{	
	private $_isNewRecord = true;
	private $_isReadonly = false;
	
	private $_attributes = array();

	static public $primaryKey;
	
	public function __wakeup() { 
		static::table();
	}
	
	public function __construct( array &$attrs = array(), $newRecord = true ) {
		$this->_isNewRecord = $newRecord;
		$this->_setAttributes( $attrs );
	}
	
	public function _setAttributes( array &$attrs ) {
		
		foreach ( $attrs as $key => $value ) {
			$this->_attributes[$key] = $value;
		}
	}
	
	public static function table() {
		return Table::load(get_called_class());
	}
	
	public function isNewRecord() {
		return $this->_isNewRecord;
	}
	
	static public function create( array $attributes ) {
	
		$class_name = get_called_class();
		
		$model = new $class_name($attributes);
		$model->save();
		return $model;
	}
	
	static public function findBySQL( $sql ) {
		$this->_isNewRecord = false;
		return static::table()->findBySQL( $sql );
	}
	
	public function &__get($name)
	{
		// check for getter
		if ( method_exists($this, "get".ucfirst($name)) ) {
			$name = "get".ucfirst($name);
			$value = $this->$name();
			return $value;
		}
		return $this->_attributes[$name];
	}

	public function __set($prop, $val) {
		
		if ( method_exists($this, "set".ucfirst($prop)) ) {
			$name = "set".ucfirst($prop);
			return $this->$name($value);
		}
		
		if ( array_key_exists($prop, $this->_attributes) ) {
			return $this->_attributes[$prop] = $val;
		}
		$this->_attributes[$prop] = $val;
	}

	// find object
	static public function find(/*$mode, $options = false*/) {

		$args = func_get_args();

		if ( $args[0] === 'all' || $args[0] === 'first' || $args[0] === 'last' ) {

			if ( isset($args[1]) && is_array($args[1]) ) {
				$options = $args[1];
			} else {
				$options = array();
			}

			switch ( $args[0]) {
				
				case 'all' :
				    $options['select'] = '*';
					break;
	
				// @todo: provide support for reversing order
				case 'last' :
					$options['order'] = static::table()->pk . ' DESC';
				case 'first' :
					$options['limit'] = 1;
			 		$options['offset'] = 0;
					break;
			}
		} else if ( is_numeric($args[0]) ) {
			$options = array('where' => array(static::table()->pk => $args[0]));
		} else {
			$options = $args[0];
		}
		return static::table()->find($options);
	}

	// @todo
	static public function count() { }
	
	public function newRecord($value) {
		$this->_isNewRecord = $value;
	}
	
	// @todo
	public function save($validate = true) { 
		$ret = $this->isNewRecord() ? $this->_insert($validate) : $this->_update($validate);
		return $ret;
	}
	
	// @todo: validate
	private function _insert( $validate = true ) {
		$table = static::table()->insert($this->_attributes);
	}
	
	private function _update( $validate = true ) {
		$table = static::table()->update($this->_attributes, array($this->table()->pk => $this->_attributes[$this->table()->pk]));
	}
	
	// Remove object
	public function delete() {
		return static::table()->delete(array('where' => array($this->table()->pk => $this->_attributes[$this->table()->pk])));
	}
	
	static public function deleteAll() {
		return static::table()->deleteAll();
	}

	// alias for find('all');
	static public function all() {
		return static::find('all'); //call_user_func_array('$this->find', array_merge(array('all'), func_get_args()));
	}

	// @todo
	static public function first() { 
		return static::find('first');
	}
	static public function last() { 
		return static::find('last');
	}
}
